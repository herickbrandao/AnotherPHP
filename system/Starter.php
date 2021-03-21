<?php namespace Another\System;

require_once("Router.php");
require_once("Request.php");
require_once("Response.php");
require_once("Database.php");
require_once("Session.php");

interface iStarter {
	public static function run();
}

class Starter implements iStarter {
	# contains the server response
	private static string $uri;

	# folder paths config
	private static string $config = "../config/";
	
	public static function run(): void {
		self::$uri = self::setUri();
		self::callController();
	}

	private static function setUri(): string {
		# server request
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$uri = strlen($uri)>1 && substr($uri,-1)==="/" ? substr($uri,0,-1) : $uri;
		$par = strlen($uri)>1 ? explode('/', $uri) : [''];

		# config requests
		require_once(self::$config.'routes.php');
		require_once(self::$config.'databases.php');
		require_once(self::$config.'sessions.php');

		# search for the request route by parsed
		$choose = Router::find(uri: $par);

		# setting elements for Request::class
		Request::setParser();
		Request::set(type: "segment", data: $par);

		# set params for Request::class
		$parsedUri = explode("/", $choose[1]);
		$context = [];
		foreach ($parsedUri as $i => $parsed) {
			if(strlen($parsed)>1 && substr($parsed,0,1)===":")
				$context[substr($parsed,1)] = $par[$i];
		}
		Request::set(type: "params", data: $context);

		# setting page title
		if(isset($choose[2]))
			echo '<title>'.$choose[2].'</title>';

		return is_callable($choose[0]) ? $choose[0]() : $choose[0];
	}

	private static function callController(): void {
		# checks if it is calling a class
		if(strpos(self::$uri, "::") !== false) {
			$method = explode("::", self::$uri);
			$controller = $method[0].".php";
		} else {
			$controller = strpos(self::$uri, ".") === false ? self::$uri.'.php' : self::$uri;
		}

		if(file_exists("../".$controller)) {
			require_once("../".$controller);

			if(isset($method)) {
				$call = array_reverse(explode("/", $method[0]))[0];
				$call = new $call();

				$method = $method[1];
				$call->$method();
			}

			# end of framework (closing all db connections)
			Database::close();
		} else {
			Router::call404();
		}
	}
}
