<?php namespace Another;

/**
 *
 */
class Session
{
	// JWT's secret or switch option
	private string $method;

	function __construct(string $secret = "COOKIE")
	{
		$this->method = $secret;
	}

	public function jwt(string|array $payload): string
	{
		$header = [
			"alg" => "HS256",
			"typ" => "JWT"
		];

		return $this->create_jwt($header, $payload);
	}

	public function base64UrlEncode(string $text): string
	{
		return str_replace(
			['+', '/', '='],
			['-', '_', ''],
			base64_encode($text)
		);
	}

	public function start(string $key): string|bool|self
	{

		if(strtolower($this->method) === "cookie") {
			$this->init_cookie($key);
			return $this;
		}
		
		return $this->validate_jwt($key);
	}

	public function has(string|array|object $var): bool
	{
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

	public function get(string|array $var = null): mixed
	{
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

	public function set(string|array|object $name, mixed $content = false): void
	{
		if( is_array($name) ) {
			foreach ($name as $key => $value) {
				$_SESSION[$key] = $value;
			}
		} else if( !empty($content) )
			$_SESSION[$name] = $content;
	}

	public function unset(string|array|object $name): void
	{
		if( is_array($name) ) {
			foreach ( $name as $k => $v )
				unset( $_SESSION[$k] );
		} else {
			unset( $_SESSION[$name] );
		}
	}

	public function destroy(): void
	{
		if($this->is_session_started()) {
			unset($_SESSION);
			session_destroy();
		}
	}

	public function destroy_all(): void
	{
		if($this->is_session_started()) {
			unset($_SESSION);
			
			foreach ($_COOKIE as $k => $v) {
				setcookie($k, "", time() - 3600); 
			}

			session_destroy();
		}
	}

	public function destroy_one(string $key): void
	{
		if($this->is_session_started()) {	
			foreach ($_COOKIE as $k => $v) {
				if( $k == $key ) {
					setcookie($k, "", time() - 3600); 
				}
			}
		}
	}

	public function extend(string $key, int $time): void
	{
		setcookie($key, "", time()+$time);
	}

	public function is_session_started(): bool
	{
		if (php_sapi_name() !== 'cli') {
			if ( version_compare(phpversion(), '5.4.0', '>=') ) {
				return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
			} else {
				return session_id() === '' ? FALSE : TRUE;
			}
		}
		return FALSE;
	}

	private function init_cookie(string $key): void
	{
		if(!isset($_COOKIE[$key]))
			$_COOKIE[$key] = md5($key.uniqid().mt_rand());

		if ($this->is_session_started())
			session_write_close();
		
		session_id($_COOKIE[$key]);
		session_name($key);
		session_start();
	}

	private function validate_jwt(string $jwt): bool
	{
		// split the token
		$tokenParts = explode('.', $jwt);
		
		if(isset($tokenParts[2])) {
			$header = base64_decode($tokenParts[0]);
			$payload = base64_decode($tokenParts[1]);
			$signatureProvided = $tokenParts[2];

			$generated = $this->create_jwt($header, $payload);

			if($generated === $jwt) {
				return true;
			}
		}

		return false;
	}

	private function create_jwt(string|array $header, string|array $payload): string
	{
		// Encode Header
		$base64UrlHeader = gettype($header)==="string" ? $this->base64UrlEncode($header) : $this->base64UrlEncode(json_encode($header));

		// Encode Payload
		$base64UrlPayload = gettype($payload)==="string" ? $this->base64UrlEncode($payload) : $this->base64UrlEncode(json_encode($payload));

		// Create Signature Hash
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $this->method, true);

		// Encode Signature to Base64Url String
		$base64UrlSignature = $this->base64UrlEncode($signature);

		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

		return $jwt;
	}
}
