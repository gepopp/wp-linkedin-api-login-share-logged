<?php


namespace LIOAUTH_Classes;


use topleader\FormSession;

class Login extends ApiActions {



	public function __construct( $token, $redirect = false ) {
		parent::__construct( $token, $redirect );

		return $this;

	}


	public function Login(){

		$this->GetUser();

		if(in_array('declined', $this->user->roles)){
			wp_logout();
			$session = new FormSession();
			$session->Add('errors', 'Ihr Account wurde deaktiviert, bitte wenden Sie sich an den Administratr unter <a href="mailto:kontakt@top-leader.at" class="underline">kontakt@top-leader.at</a>.');
			wp_safe_redirect(get_field('field_60f7e8aad0afa', 'option'));
			exit;
		}



		wp_clear_auth_cookie();
		wp_set_current_user($this->user->ID);
		wp_set_auth_cookie($this->user->ID);
		do_action('wp_login', null, $this->user);
		wp_safe_redirect($this->redirect ? $this->redirect : get_field('field_5da3b06108b48', 'option'));
		exit;

	}

}