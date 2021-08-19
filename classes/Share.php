<?php


namespace LIOAUTH_Classes;


use topleader\FormSession;

class Share extends ApiActions {

	protected $post_id;


	public function __construct( $token, $redirect, $post_id ) {

        parent::__construct($token, $redirect);

		$this->post_id  = $post_id;


		return $this;
	}

	public function SharePost() {

		$excerpt     = str_replace("\n","\\n", get_the_excerpt( $this->post_id ));
		$title       = htmlspecialchars_decode( get_the_title( $this->post_id ) );
		$permalink   = get_the_permalink( $this->post_id );
		$linkedin_id = $this->token->linkedin_id;

		$body = <<<EOM
{
    "author": "urn:li:person:$linkedin_id",
    "lifecycleState": "PUBLISHED",
    "specificContent": {
        "com.linkedin.ugc.ShareContent": {
            "shareCommentary": {
                "text": "$excerpt"
            },
            "shareMediaCategory": "ARTICLE",
            "media": [
                {
                    "status": "READY",
                    "description": {
                        "text": "$excerpt"
                    },
                    "originalUrl": "$permalink",
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
					'Authorization'             => 'Bearer ' . $this->token->access_token,
					'X-Restli-Protocol-Version' => '2.0.0',
					'Content-Type'              => 'application/json',
				],
				'body'    => $body,
			] );

		$share = json_decode( wp_remote_retrieve_body( $post ) );

		$session = new FormSession();

		if(wp_remote_retrieve_response_code($post) > 399){

			$session->Add('errors', 'Ein Fehler beim teilen ist aufgetreten. Versuchen Sie es später erneut.');
			wp_safe_redirect( get_the_permalink( $this->post_id ) );
			exit;
		}

		global $wpdb;
		$wpdb->insert( $wpdb->prefix . 'linkein_shares', [
			'linkedin_id' => $this->token->linkedin_id,
			'email'       => $this->token->email,
			'name'        => $this->token->name,
			'share_id'    => $share->id,
			'post_id'     => $this->post_id ?? 999999,
		] );

		$session->Add('success', 'Erfolgreich geteilt. Vielen Dank!');
		wp_safe_redirect( get_the_permalink( $this->post_id ) );
		exit;
	}
}