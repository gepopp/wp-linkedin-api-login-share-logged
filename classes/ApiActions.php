<?php


namespace LIOAUTH_Classes;


class ApiActions {

	protected Token $token;


	protected $redirect;




	public function __construct( $token, $redirect ) {


		$this->token    = $token;
		$this->redirect = $redirect;


		return $this;
	}


}