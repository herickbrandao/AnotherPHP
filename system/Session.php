<?php namespace Another\System;

interface iSession {
	public static function start(string $key);
	public static function write(string $name, mixed $method);
	public static function create(string $key, mixed $array, mixed $content = false);
	public static function is_created(string $key);
	public static function get(string $var=null);
	public static function set(string|array|object $name, mixed $content = false);
	public static function unset(string|array|object $name);
	public static function destroy();
	public static function destroy_all();
	public static function destroy_one(string $key);
	public static function is_session_started();
}

class Session implements iSession {
	private static array $scope=[]; # session's config

	private static function init(string $key) {
		if(!isset($_COOKIE[$key]))
			$_COOKIE[$key] = md5($key.uniqid().mt_rand());

		if (self::is_session_started())
			session_write_close();
		
		session_id($_COOKIE[$key]);
		session_name($key);
		session_start();
	}

	public static function write(string $name, mixed $method) {
		self::$scope[$name] = $method;
	}

	public static function create(string $key, mixed $array, mixed $content = false) {
		self::init($key);

		if(is_array($array)||is_object($array)) {
			$array = (array)$array;
			foreach ($array as $k => $v)
				$_SESSION[$k] = $v;
		} else if ( !empty($content) ) {
			$_SESSION[$array] = $content;
		}
	}

	public static function is_created(string $key) {
		self::init($key);
		return ( empty($_SESSION) ) ? false : true; 
	}

	public static function start(string $key) {
		self::init( $key );

		if(  isset(self::$scope[$key]) AND is_callable(self::$scope[$key]) )
			self::$scope[$key]();
	}

	public static function has(string|array|object $var) {
		if ( is_array($var) ) {
			foreach ($var as $v) {
				if(empty($_SESSION[$v])) {
					return false;
				}
			}
			return true;
		} else if (!empty($_SESSION[$var])) {
			return true;
		} else {
			return false;
		}
	}

	public static function get(string $var=null) {
		if ($var == null)
			return isset($_SESSION) ? $_SESSION : array();

		if( is_array($var) ) {
			$return_ = [];
			foreach ($var as $v)
				$return_[$v] = isset($_SESSION[$v]) ? $_SESSION[$v] : false;

			return $return_;
		} else {
			if ( isset($_SESSION[$var]) )
				return $_SESSION[$var];
			else
				return null;
		}
	}

	public static function set(string|array|object $name, mixed $content = false) {
		if( is_array($name) ) {
			foreach ($name as $key => $value) {
				$_SESSION[$key] = $value;
			}
		} else if( !empty($content) )
			$_SESSION[$name] = $content;
	}

	public static function unset(string|array|object $name) {
		if( is_array($name) ) {
			foreach ( $name as $k => $v )
				unset( $_SESSION[$k] );
		} else {
			unset( $_SESSION[$name] );
		}
	}

	public static function destroy() {
		if( self::is_session_started() ) {
			unset($_SESSION);
			session_destroy();
		}
	}

	public static function destroy_all() {
		if( self::is_session_started() ) {
			unset($_SESSION);
			
			foreach ($_COOKIE as $k => $v) {
				setcookie($k, "", time() - 3600); 
			}

			session_destroy();
		}
	}

	public static function destroy_one(string $key) {
		if(self::is_session_started()) {	
			foreach ($_COOKIE as $k => $v) {
				if( $k == $key ) {
					setcookie($k, "", time() - 3600); 
				}
			}
		}
	}

	public static function is_session_started() {
		# Checks if php has a session...
		if (php_sapi_name() !== 'cli') {
			if ( version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
	}
}
