<?php


namespace LIOAUTH_Classes;


class Login extends ApiActions {


	protected $user;


	public function __construct( $token, $redirect = false ) {
		parent::__construct( $token, $redirect );

		return $this;

	}


	public function Login(){

		$this->GetUser();


		wp_clear_auth_cookie();
		wp_set_current_user($this->user->ID);
		wp_set_auth_cookie($this->user->ID);
		wp_safe_redirect($this->redirect ? $this->redirect : get_field('field_5da3b06108b48', 'option'));
		exit;


	}



	public function GetUser(){


		$user = get_user_by( 'email', $this->token->email );


		if ( ! $user ) {

			$names = explode(' ', $this->token->name);

			$user = wp_insert_user( [
				'user_login' => $this->token->name .'_'.time(),
				'first_name' => $names[0],
				'last_name'  => $names[1],
				'user_email' => $this->token->email,
				'user_pass'  => wp_unique_id(),
			]);
			if($user){
				do_action('tl_activate_user', get_user_by('ID', $user));
			}
		}

		$this->user = $user;


	}

}