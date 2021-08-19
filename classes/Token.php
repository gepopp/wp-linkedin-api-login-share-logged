<?php


namespace LIOAUTH_Classes;

use Carbon\Carbon;


class Token {

	const TABLE_NAME = 'linkein_oauth_token';


	public $access_token;


	protected $expires_in;


	public $linkedin_id;


	public $name;


	public $email;


	protected $code;


	public function __construct($code) {

		$this->code = $code;

		if(!$this->LoadTokenFromDB()){
			$this->ObtainTokenFromApi()->RetrieveProfile()->RetrieveEmail()->SaveTokenToDB();
		}


	}


	public function LoadTokenFromDB() {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		/**
		 * @var $user
		 * @type \WP_User
		 */
		$user = wp_get_current_user();

		global $wpdb;
		$saved_token = $wpdb->get_row( sprintf( 'SELECT * FROM %s WHERE email = "%s"', $wpdb->prefix . self::TABLE_NAME, $user->user_email ) );

		if ( empty( $saved_token ) ) {
			return false;
		}

		$created = new Carbon( $saved_token->created_at );
		$expires = $created->addSeconds( $saved_token->expires_in - 60 );

		if ( $expires->isPast() ) {
			return false;
		}

		$this->linkedin_id  = $saved_token->linkedin_id;
		$this->email        = $saved_token->email;
		$this->name         = $saved_token->name;
		$this->access_token = $saved_token->access_token;

		return true;

	}

	protected function ObtainTokenFromApi() {

		$body = sprintf( 'grant_type=authorization_code&code=%s&redirect_uri=%s&client_id=%s&client_secret=%s',
			$this->code,
			home_url( 'linkedinoauth' ),
			LINKEDIN_CLIENT_ID,
			LINKEDIN_CLIENT_SECRET
		);


		$token_request      = wp_remote_post(
			'https://www.linkedin.com/oauth/v2/accessToken?' . $body,
			[
				'headers' => [
					'Content-Type' => 'x-www-form-urlencoded',
				],
			]
		);


		if(wp_remote_retrieve_response_code($token_request) > 399){
			wp_safe_redirect(get_field('field_60f7e8aad0afa', 'option'));
		}


		$token  = json_decode( wp_remote_retrieve_body( $token_request ) );


		$this->access_token = $token->access_token;
		$this->expires_in   = $token->expires_in;

		return $this;

	}


	protected function RetrieveProfile() {


		$profile = wp_remote_get( 'https://api.linkedin.com/v2/me',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $this->access_token,
				],
			] );

		$profile = json_decode( wp_remote_retrieve_body( $profile ) );

		$this->name   = $profile->localizedFirstName . ' ' . $profile->localizedLastName;
		$this->linkedin_id = $profile->id;

		return $this;

	}


	public function RetrieveEmail() {

		$email = wp_remote_get( 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))',
			[
				'headers' => [
					'Authorization' => 'Bearer ' . $this->access_token,
				],
			] );

		$email       = json_decode( wp_remote_retrieve_body( $email ) );
		$this->email = $email->elements[0]->{'handle~'}->emailAddress;


		return $this;

	}


	protected function SaveTokenToDB() {


		global $wpdb;
		$wpdb->delete( $wpdb->prefix . 'linkein_oauth_token', [ 'email' => $this->email ] );
		$wpdb->insert( $wpdb->prefix . 'linkein_oauth_token', [
			'linkedin_id'  => $this->linkedin_id,
			'email'        => $this->email,
			'name'         => $this->name,
			'access_token' => $this->access_token,
			'expires_in'   => $this->expires_in,
		] );


		return $this;

	}
}