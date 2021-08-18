<?php

namespace linkedinoauth;


add_action( 'parse_request', function () {

	if ( strpos( $_SERVER['REQUEST_URI'], 'linkedinoauth' ) === false ) {
		return;
	}

	$gets = json_decode(base64_decode($_GET['state']));

	$nonce = $gets->nonce;
	$post_id = isset($gets->redirect) && get_post_status($gets->redirect) ? $gets->redirect : false;


	$body = sprintf( 'grant_type=authorization_code&code=%s&redirect_uri=%s&client_id=%s&client_secret=%s',
		$_GET['code'],
		home_url( 'linkedinoauth' ),
		LINKEDIN_CLIENT_ID,
		LINKEDIN_CLIENT_SECRET );


	$token = wp_remote_post(
		'https://www.linkedin.com/oauth/v2/accessToken?' . $body,
		[
			'headers' => [
				'Content-Type' => 'x-www-form-urlencoded',
			],

		]
	);

	$token = json_decode( wp_remote_retrieve_body( $token ) );


	$profile = wp_remote_get( 'https://api.linkedin.com/v2/me',
		[
			'headers' => [
				'Authorization' => 'Bearer ' . $token->access_token,
			],
		] );

	$profile = json_decode( wp_remote_retrieve_body( $profile ) );

//wp_die(var_dump($profile));


	$email = wp_remote_get( 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))',
		[
			'headers' => [
				'Authorization' => 'Bearer ' . $token->access_token,
			],
		] );

	$email = json_decode( wp_remote_retrieve_body( $email ) );
	$email = $email->elements[0]->{'handle~'}->emailAddress;

	global $wpdb;
	$wpdb->delete( $wpdb->prefix . 'linkein_oauth_token', [ 'email' => $email ] );
	$wpdb->insert( $wpdb->prefix . 'linkein_oauth_token', [
		'linkedin_id'  => $profile->id,
		'email'        => $email,
		'name'         => $profile->localizedFirstName . ' ' . $profile->localizedLastName,
		'access_token' => $token->access_token,
		'expires_in'   => $token->expires_in,
	] );

	$url  = get_the_permalink($post_id);
	$title = get_the_title($post_id);
	$desc = get_the_excerpt($post_id);

	$body = <<<EOM
{
    "author": "urn:li:person:$profile->id",
    "lifecycleState": "PUBLISHED",
    "specificContent": {
        "com.linkedin.ugc.ShareContent": {
            "shareCommentary": {
                "text": "$desc"
            },
            "shareMediaCategory": "ARTICLE",
            "media": [
                {
                    "status": "READY",
                    "description": {
                        "text": "$title"
                    },
                    "originalUrl": "$url",
                    "title": {
                        "text": "$title"
                    }
                }
            ]
        }
    },
    "visibility": {
        "com.linkedin.ugc.MemberNetworkVisibility": "PUBLIC"
    }
}
EOM;


	$post = wp_remote_post( 'https://api.linkedin.com/v2/ugcPosts',
		[
			'headers' => [
				'Authorization'             => 'Bearer ' . $token->access_token,
				'X-Restli-Protocol-Version' => '2.0.0',
				'Content-Type'              => 'application/json',
			],
			'body'    => $body,
		] );

	$share = json_decode( wp_remote_retrieve_body( $post ) );

	$wpdb->insert( $wpdb->prefix . 'linkein_shares', [
		'linkedin_id' => $profile->id,
		'email'       => $email,
		'name'         => $profile->localizedFirstName . ' ' . $profile->localizedLastName,
		'share_id'    => $share->id,
		'post_id'     => $post_id ?? 999999
	] );

} );


function CreateAuthLink($redirect = '') {

	if ( ! defined( 'LINKEDIN_CLIENT_ID' ) || ! defined( 'LINKEDIN_CLIENT_SECRET' ) ) {
		throw new \Exception( 'Please set your Linkedin Client ID and Secret in the wp-config.php file.' );
	}

	$baseurl = 'https://www.linkedin.com/oauth/v2/authorization';

	$state = [
		'nonce' => 	wp_create_nonce( 'linkedinoauth' ),
		'redirect' => $redirect
	];



	return add_query_arg( [
		'state'         => base64_encode(json_encode($state)),
		'response_type' => 'code',
		'client_id'     => LINKEDIN_CLIENT_ID,
		'redirect_uri'  => home_url( 'linkedinoauth' ),
		'scope'         => 'r_liteprofile%20r_emailaddress%20w_member_social',
	], $baseurl );

}

add_filter( 'http_request_timeout', function ( $time ) {
	// Default timeout is 5
	return 30;
} );