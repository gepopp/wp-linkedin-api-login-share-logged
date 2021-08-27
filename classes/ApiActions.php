<?php


namespace LIOAUTH_Classes;


class ApiActions {

	protected Token $token;


	protected $redirect;


	protected $user;


	public function __construct( $token, $redirect ) {

		$this->token    = $token;
		$this->redirect = $redirect;

		return $this;
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