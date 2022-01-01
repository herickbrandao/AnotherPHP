<?php

/**
 * Example class use case's of Session
 * There is 2 available methods: JWT and COOKIE
 */
class baseSession
{
	private $session;

	/**
	 * The following line is required in a extended class constructor:
	 *    parent::__construct(); 
	 */
	function __construct()
	{
		/**
		 * Session by JWT method:
		 * new Another\Session(secret: "YOUR-JWT-SECRET-HERE")
		 * 
		 * Generates a new JWT code (returns a string)
		 * $this->session->jwt(payload: "YOUR-DATA (JSON OR ARRAY)");
		 * 
		 * JWT Validation (returns true/false)
		 * $this->session->start("YOUR-JWT-FOR-VALIDATION");
		 */
		$this->session = new Another\Session('$%(@))%*!)$#!%)!)*!21ve,WGOGWPKE');
		$MY_JWT_HASH =  $this->session->jwt(['id' => 1, 'name' => 'YourName']);
		var_dump( $this->session->start($MY_JWT_HASH) );

		/**
		 * Session by Cookie method
		 * It's the basic session from PHP
		 * 
		 * Examples:
		 * $this->session->set(["name" => "YourName"]); is equal to $_SESSION["name"] = "YourName";
		 * $this->session->get("name"); is equal to $_SESSION["name"];
		 */
		$this->session = new Session();
		$this->session->start("myOwnCookieSession");
		$this->session->set(['id' => 1, 'name' => 'YourName']);
		var_dump( $_SESSION );
	}
}
