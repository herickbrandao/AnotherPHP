<?php namespace Another;

/**
 * 
 */
include_once 'Router.php';
include_once 'Database.php';
include_once 'Session.php';

/**
 * 
 */
class Root
{
	public function Start()
	{	
		/**
		 * Binds Http Requests into $_POST variable (POST, PUT, DELETE, PATCH)
		 */
		$rest_api = json_decode(file_get_contents("php://input"), true);
		if(!empty($rest_api)) { $_POST = $rest_api; }
		unset($rest_api);

		/**
		 * 
		 */
		include_once('../app/config/routes.php');
	}
}