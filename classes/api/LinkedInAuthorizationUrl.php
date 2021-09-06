<?php

namespace LIOAUTH_Classes\api;

class LinkedInAuthorizationUrl {


	protected $redirect_url;


	public function __construct() {

		$options = get_option(Constants::LIOAUTH_GENERAL_SETTING);
		$this->redirect_url = $options['redirect_url'] ?? 'linkedinoauth';

	}

	public function authorziation_url($redirect = '', $action = '' ) : string {

		$state = [
			'nonce'    => wp_create_nonce( 'linkedinoauth' ),
			'redirect' => $redirect,
			'action'   => $action
		];

		return add_query_arg( [
			'state'         => base64_encode( json_encode( $state ) ),
			'response_type' => 'code',
			'client_id'     => Constants::get_client_id(),
			'redirect_uri'  => $this->redirect_url,
			'scope'         => Constants::LIOAUTH_PROFILE_SCOPE . '%20' . Constants::LIOAUTH_EMAIL_SCOPE . '%20' . Constants::LIOAUTH_SCHARING_SCOPE,
		], Constants::LIOAUTH_API_URL );

	}
}