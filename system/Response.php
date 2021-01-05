<?php namespace Another\System;

interface iResponse {
	public static function status($num);
	public static function json($json, $opt = false);
	public static function file($file);
	public static function inc($file);
	public static function echo($text);
	public static function view($sys_file_basis, $data = false);
	public static function redirect($url = null);
	public static function call404();
}

class Response implements iResponse {
	public static function status($num) {
		http_response_code($num);
		return new self;
	}

	public static function json($json, $opt = false) {
		if( is_array($json) || is_object($json) ) {
			echo $opt ? json_encode($json, $opt) : json_encode($json) ;
		} else if ( json_decode( $json , true ) != NULL ) {
			echo $json;
		}
		return new self;
	}

	public static function file($file) {
		if( file_exists($file) ) {
			echo file_get_contents($file);
		} else {
			echo "<b>warning:</b> {$file} doesn't exists.";
		}
		return new self;
	}

	public static function inc($file) {
		if( file_exists($flie) ) {
			include $file;
		} else {
			echo "<b>warning:</b> {$file} doesn't exists.";
		}
		return new self;
	}

	public static function echo($text) {
		echo $text;
		return new self;
	}

	public static function view($sys_file_basis, $data = false) {
		if(is_array($sys_file_basis)) {
			foreach ($sys_file_basis as $sys_file_b) {
				$sys_file_path = '../views/' . $sys_file_b . '.php';

				if( file_exists($sys_file_path) ) {
					require($sys_file_path);
				} else {
					echo('<b>Warning:</b> the view "'.$sys_file_b.'" does not exist!');
				}
			}
		} else {
			$sys_file_path = '../views/' . $sys_file_basis . '.php';
			
			if( file_exists($sys_file_path) ) {
				require($sys_file_path);
			} else {
				echo('<b>Warning:</b> the view "'.$sys_file_basis.'" does not exist!');
			}
		}
	}

	public static function redirect($url = null) {
		# if is a complete uri (in theory)
		if(stripos($url, '.') !== false){
			header("Location: ".$url);
		# if is the defaultPage redirection
		}else if($url == null){
			header("Location: /");
		# if is a page inside the App
		}else if ($url != null) {
			if (strpos($url, '/') !== 0) {
				$url = '/'.$url;
			}
			header("Location: ".$url);
		}
		exit;
	}

	public static function call404() {
		Router::call404();
	}
}