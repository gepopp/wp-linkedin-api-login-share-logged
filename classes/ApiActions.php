<?php


namespace LIOAUTH_Classes;


use topleader\FormSession;

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

			$session = new FormSession();
			$session->Add('errors', "Wir konnten keinen Account mit dieser E-Mail Adresse finden. Bitte registrieren Sie sich zuerst.");
			wp_safe_redirect(get_field('field_60f7e3cf65354', 'option'));
			exit;
		}

		$this->user = $user;
	}

}