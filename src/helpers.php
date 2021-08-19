<?php
namespace linkedinoauth;


function CreateAuthLink( $redirect = '', $action = '' ) {

	if ( ! defined( 'LINKEDIN_CLIENT_ID' ) || ! defined( 'LINKEDIN_CLIENT_SECRET' ) ) {
		throw new \Exception( 'Please set your Linkedin Client ID and Secret in the wp-config.php file.' );
	}

	$baseurl = 'https://www.linkedin.com/oauth/v2/authorization';

	$state = [
		'nonce'    => wp_create_nonce( 'linkedinoauth' ),
		'redirect' => $redirect,
		'action'   => $action
	];


	return add_query_arg( [
		'state'         => base64_encode( json_encode( $state ) ),
		'response_type' => 'code',
		'client_id'     => LINKEDIN_CLIENT_ID,
		'redirect_uri'  => home_url( 'linkedinoauth' ),
		'scope'         => 'r_liteprofile%20r_emailaddress%20w_member_social',
	], $baseurl );

}