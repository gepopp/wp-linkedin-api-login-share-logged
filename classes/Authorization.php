<?php

namespace LIOAUTH_Classes;



class Authorization {


	protected $nonce;


	protected $redirect;


	protected $perform_action;


	protected $post_id;


	protected $token;


	protected $action = 'login';


	public function __construct() {

		add_action( 'parse_request', [ $this, 'ParseRequest' ] );

	}

	public function ParseRequest() {

		if ( strpos( $_SERVER['REQUEST_URI'], 'linkedinoauth' ) === false ) {
			return;
		}


		if ( ! isset( $_GET['code'] ) ) {
			return;
		}


		if ( ! isset( $_GET['state'] ) ) {
			return;
		}

		$this->DecodeState( sanitize_text_field( $_GET['state'] ) );

		if ( ! wp_verify_nonce( $this->nonce, 'linkedinoauth' ) ) {
			return;
		}

		$this->token = new Token( sanitize_text_field( $_GET['code'] ));


		if($this->action == 'login'){
			(new Login($this->token, $this->redirect))->Login();
		}else{
			(new Share($this->token, $this->redirect, $this->post_id))->SharePost();
		}
	}


	protected function DecodeState( $state ) {

		$decoded_state = json_decode( base64_decode( $state ) );
		$this->nonce   = $decoded_state->nonce;

		$this->action = $decoded_state->action ?? 'login';

		if ( get_post_status( $decoded_state->redirect ) ) {
			$this->post_id  = $decoded_state->redirect;
			$this->redirect = get_the_permalink( $this->post_id );
		} else {
			$this->redirect = $decoded_state->redirect;
		}


	}



}


