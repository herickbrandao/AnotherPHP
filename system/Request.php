<?php namespace Another\System;

interface iRequest {
	public static function get(string|array $name = null);
	public static function post(string|array $name = null);
	public static function put(string|array $name = null);
	public static function delete(string|array $name = null);
	public static function patch(string|array $name = null);
	public static function take(string|array $name = null);
	public static function segment(int $int = 0);
	public static function params(string $pos = null);
	public static function getUserIp();
	public static function cookie(string $name = null);
	public static function set(string $type, string|array $data, $cont = false);
	public static function getMethod();
}

class Request implements iRequest {
	private static array|object $PUT=[],$DEL=[],$PAT=[],$HEADER=[],$SEGMENT=[],$PARAMS=[];
	private static string $REQUEST_METHOD;

	public static function get(string|array $name = null) {
		if( empty($name) ) {
			return $_GET;
		} else {
			if(is_array($name)) {
				$array_ = array();

				foreach ($name as $n)
					$array_[$n] = ( isset($_GET[$n]) ) ? $_GET[$n] : null;

				return $array_;
			} else {
				return ( isset($_GET[$name]) ) ? $_GET[$name] : null;
			}
		}
	}

	public static function post(string|array $name = null) {
		if(empty($name)) {
			return $_POST;
		} else {
			if(is_array($name)) {
				$array_ = array();

				foreach ($name as $n)
					$array_[$n] = ( isset($_POST[$n]) ) ? $_POST[$n] : null;

				return $array_;
			} else {
				return ( isset($_POST[$name]) ) ? $_POST[$name] : null;
			}
		}
	}

	public static function put(string|array $name = null) {
		if( empty($name) ) {
			return self::$PUT;
		} else {
			if( is_array($name) ) {
				$array_ = array();

				foreach ($name as $n)
					$array_[$n] = ( isset(self::$PUT[$n]) ) ? self::$PUT[$n] : null;

				return $array_;
			}
			else
			{
				return ( isset(self::$PUT[$name]) ) ? self::$PUT[$name] : null;
			}
		}
	}

	public static function delete(string|array $name = null) {
		if( empty($name) ) {
			return self::$DEL;
		} else {
			if( is_array($name) ) {
				$array_ = array();

				foreach ($name as $n)
					$array_[$n] = ( isset(self::$DEL[$n]) ) ? self::$DEL[$n] : null;

				return $array_;
			} else {
				return ( isset(self::$DEL[$name]) ) ? self::$DEL[$name] : null;
			}
		}
	}

	public static function patch(string|array $name = null) {
		if( empty($name) ) {
			return self::$PAT;
		} else {
			if( is_array($name) ) {
				$array_ = array();

				foreach ($name as $n)
					$array_[$n] = ( isset(self::$PAT[$n]) ) ? self::$PAT[$n] : null;

				return $array_;
			}
			else
			{
				return ( isset(self::$PAT[$name]) ) ? self::$PAT[$name] : null;
			}
		}
	}

	public static function take(string|array $name = null) {

		$status = match(self::$REQUEST_METHOD) {
			'get'	 => self::get($name),
			'post'	 => self::post($name),
			'put'	 => self::put($name),
			'delete' => self::delete($name),
			'patch'	 => self::patch($name),
			default  => throw new \Exception('Unsupported')
		};

		# set params in return
		if(is_null($name)) {
			foreach (self::$PARAMS as $key => $value) {
				if(!isset($status[$key])) {
					$status[$key] = $value;
				}
			}
		} else {
			if(is_array($name)) {
				foreach ($name as $value) {
					$status[$value] = isset(self::$PARAMS[$value]) ? self::$PARAMS[$value] : $status[$value];
				}
			} else if(!empty($name)&&isset(self::$PARAMS[$name])&&!isset($status[$name])) {
				$status[$name] = self::$PARAMS[$name];
			}
		}
		return $status;
	}

	public static function segment(int $int = 0) {
		if(!empty(self::$SEGMENT[$int])) {
			return self::$SEGMENT[$int];
		} else {
			if ($int===-1) {
				return end(self::$SEGMENT);
			} else {
				return false;
			}
		}
	}

	public static function params(string $pos = null) {
		if ( $pos ) {
			return isset(self::$PARAMS[$pos]) ? self::$PARAMS[$pos] : false;
		} else {
			return self::$PARAMS;
		}
	}

	public static function getUserIp() {
		return isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
	}

	public static function header(string|array $name = null) {
		self::$HEADER = apache_request_headers();

		if( empty($name) ) {
			return self::$HEADER;
		} else {
			if( is_array($name) ) {
				$array_ = array();

				foreach ($name as $n)
					$array_[$n] = ( isset(self::$HEADER[$n]) ) ? self::$HEADER[$n] : null;

				return $array_;
			} else {
				return ( isset(self::$HEADER[$name]) ) ? self::$HEADER[$name] : null;
			}
		}
	}

	public static function cookie(string $name = null) {
		if(empty($name)) {
			return ( empty($_COOKIE) ) ? array() : $_COOKIE;
		} else {
			return ( isset($_COOKIE[$name]) ) ? $_COOKIE[$name] : false;
		}
	}

	public static function set(string $type, string|array $data, $cont = false): void {
		switch ( strtolower($type) ) {
			case 'get':
				if( is_array($data) ) {
					foreach ($data as $key => $value)
						$_GET[$key] = $value;
				} else if ( $cont ) {
					$_GET[$data] = $cont;
				}
				break;

			case 'post':
				if( is_array($data) ) {
					foreach ($data as $key => $value)
						$_POST[$key] = $value;
				} else if ( $cont ) {
					$_POST[$data] = $cont;
				}
				break;

			case 'put':
				if( is_array($data) ) {
					foreach ($data as $key => $value)
						self::$PUT[$key] = $value;
				} else if ( $cont ) {
					self::$PUT[$data] = $cont;
				}
				break;

			case 'patch':
				if( is_array($data) ) {
					foreach ($data as $key => $value)
						self::$PAT[$key] = $value;
				} else if ( $cont ) {
					self::$PAT[$data] = $cont;
				}
				break;

			case 'delete':
				if( is_array($data) ) {
					foreach ($data as $key => $value)
						self::$DEL[$key] = $value;
				} else if ( $cont ) {
					self::$DEL[$data] = $cont;
				}
				break;

			case 'cookie':
				if( is_array($data) ) {
					foreach ($data as $key => $value)
						$_COOKIE[$key] = $value;
				} else if ( $cont ) {
					$_COOKIE[$data] = $cont;
				}
				break;

			case 'segment':
				self::$SEGMENT = $data;
				$http = (isset($_SERVER['HTTPS'])) ? 'https://' : 'http://';
				$http = $http . $_SERVER['HTTP_HOST'];
				self::$SEGMENT[0] = $http;
				break;

			case 'params':
				self::$PARAMS = $data;
				break;
		}
	}

	public static function getMethod(): string {
		return self::$REQUEST_METHOD;
	}

	public static function setParser(): void {
		self::$REQUEST_METHOD = strtolower($_SERVER['REQUEST_METHOD']);
		
		switch (self::$REQUEST_METHOD) {
			case 'post':
				$rest_api = json_decode(file_get_contents("php://input"), true);
				if(!empty($rest_api)) { $_POST = $rest_api; }
				break;
			case 'put':
				self::$PUT = json_decode(file_get_contents("php://input"), true);
				break;
			case 'delete':
				self::$DEL = json_decode(file_get_contents("php://input"), true);
				break;
			case 'patch':
				self::$PAT = json_decode(file_get_contents("php://input"), true);
				break;
		}
	}
}
