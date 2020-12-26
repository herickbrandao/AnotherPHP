<?php use \system\Session;

Session::write('session_name', function() {
	if( !Session::has('id') ) {
		# forbbiden access #403
		http_response_code(403);
		echo json_encode(["error" => "Access Forbidden"]);
		exit;
	} else {
		$session_duration = 14400; # creating a session time limit (seconds)

		if ( !Session::get('session_time') ) { Session::set( 'session_time', time() ); }
		$session_limit = Session::get('session_time') + $session_duration;

		if ( time() > $session_limit ) {
			Session::destroy();
			
			# session expiration #401 
			http_response_code(401);
			echo json_encode(["error" => "Session time expired"]);
			exit;
		}
	}
});

Session::write('language', function() {
	$lang = Session::has('choosed') ? Session::get('choosed') : substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

	switch ($lang) {
		case 'en':
			# code...
			break;

		case 'pt':
			# code...
			break;
		
		default:
			# code...
			break;
	}
});

/*
CONTROLLER FUNCTIONS
=========================
Session::start('default')
Session::destroy() # current session
Session::destroy_all() # destroy all cookies/Session
Session::has('var') OR session::has(['id','name'])
Session::get('var')
Session::set($array) OR session::set('key', 'value')
*/