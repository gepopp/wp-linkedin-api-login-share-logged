<?php


namespace LIOAUTH_Classes;


class Register extends ApiActions {


	public function __construct( $token, $redirect = false ) {
		parent::__construct( $token, $redirect );

		return $this;

	}


	public function Rergister() {


		if ( get_user_by( 'email', $this->token->email ) ) {
			( new Login( $this->token, $this->redirect ) );
		} else {

			$name  = $this->token->name;
			$split = explode( ' ', $name );

			$user_id = wp_insert_user( [
				'user_pass'     => wp_generate_uuid4(),
				'user_login'    => $name . ' ' . uniqid(),
				'first_name'    => array_shift( $split ),
				'last_name'     => implode( ' ', $split ),
				'user_nicename' => $this->token->name,
				'user_email'    => $this->token->email,
				'role'          => 'subscriber',
			] );

			wp_clear_auth_cookie();
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id );
			wp_safe_redirect( $this->redirect ? $this->redirect : get_field( 'field_5da3b06108b48', 'option' ) );
			exit;

		}

	}

}