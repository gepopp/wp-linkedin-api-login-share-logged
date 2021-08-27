<?php


namespace LIOAUTH_Classes;


class Login extends ApiActions {



	public function __construct( $token, $redirect = false ) {
		parent::__construct( $token, $redirect );

		return $this;

	}


	public function Login(){

		$this->GetUser();

		wp_clear_auth_cookie();
		wp_set_current_user($this->user->ID);
		wp_set_auth_cookie($this->user->ID);
		do_action('wp_login', null, $this->user);
		wp_safe_redirect($this->redirect ? $this->redirect : get_field('field_5da3b06108b48', 'option'));
		exit;

	}

}