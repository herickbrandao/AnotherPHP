<?php namespace system;

interface iRouter {
	# restApi's methods
	public static function add(string $request, mixed $callback, string $title = "");
	public static function get(string $request, mixed $callback, string $title = "");
	public static function post(string $request, mixed $callback, string $title = "");
	public static function put(string $request, mixed $callback, string $title = "");
	public static function delete(string $request, mixed $callback, string $title = "");
	public static function patch(string $request, mixed $callback, string $title = "");

	# other functions
	public static function find(array $uri);
	public static function set404(string $callback = "system/404.html", string $title = "");
	public static function call404();
}

class Router implements iRouter {
	private static array $router=[], $titles=[], $overwrite=[];

	public static function get(string $request, mixed $callback, string $title = "") {
		return self::storage('get', $request, $callback, $title);
	}
	public static function post(string $request, mixed $callback, string $title = "") {
		return self::storage('post', $request, $callback, $title);
	}
	public static function pos(string $request, mixed $callback, string $title = "") {
		return self::storage('post', $request, $callback, $title);
	}
	public static function put(string $request, mixed $callback, string $title = "") {
		return self::storage('put', $request, $callback, $title);
	}
	public static function delete(string $request, mixed $callback, string $title = "") {
		return self::storage('delete', $request, $callback, $title);
	}
	public static function del(string $request, mixed $callback, string $title = "") {
		return self::storage('delete', $request, $callback, $title);
	}
	public static function patch(string $request, mixed $callback, string $title = "") {
		return self::storage('patch', $request, $callback, $title);
	}
	public static function pat(string $request, mixed $callback, string $title = "") {
		return self::storage('patch', $request, $callback, $title);
	}
	public static function add(string $request, mixed $callback, string $title = "") {
		self::$router['get'][$request] = $callback;
		self::$router['post'][$request] = $callback;
		self::$router['put'][$request] = $callback;
		self::$router['delete'][$request] = $callback;
		self::$router['patch'][$request] = $callback;

		if(!empty($title)) {
			self::$titles['get'][$request] = $title;
			self::$titles['post'][$request] = $title;
			self::$titles['put'][$request] = $title;
			self::$titles['delete'][$request] = $title;
			self::$titles['patch'][$request] = $title;
		}
		return $this;
	}

	public static function set404(string $callback = "system/404.html", string $title = "") {
		if(!empty($title)) { self::$titles['404'] = $title; }
		self::$router['404'] = '../'.$callback;
	}

	public static function call404(): void {
		http_response_code(404);

		if(isset(self::$router['404']) && file_exists(self::$router['404'])) {
			include_once self::$router['404'];

			# setting page title
			if(isset(self::$titles['404']))
				echo '<title>'.self::$titles['404'].'</title>';
		} else {
			include_once "404.html";
		}

		exit;
	}
	
	public static function find(array $uri): array {
		$method = strtolower($_SERVER['REQUEST_METHOD']);
		$routes = isset(self::$router[$method]) ? self::$router[$method] : false;
		self::$overwrite = [];

		if($routes) {
			$arrayRoutes = array_filter($routes, function(string $key) use ($uri) {
				$routesFromSystem  = strlen($key)>1 ? explode("/", $key) : [''];
				$uriOver = $uri;
				$uriSet  = false;	

				if(count($routesFromSystem)===count($uri)) {	
					$tries = 0;
					foreach ($routesFromSystem as $k => $v) {
						# overwrites the parameters
						if(strlen($v)>1 && substr($v,0,1)===":") {
							$uriOver[$k] = $routesFromSystem[$k];
							$tries++;
						} else {
							$uriOver[$k] = $uri[$k];
						}
					}

					# acceptable route (pushed into self::$overwrite)
					if(implode("/", $uriOver)===implode("/", $routesFromSystem)) {
						self::$overwrite[] = ['tries'=>$tries,'key'=>$key];
						$uriSet = true;
					}	
				}

				return $uriSet;
			}, ARRAY_FILTER_USE_KEY);

			# sort by the number of overwrites in request (less tries = right response)
			usort(self::$overwrite, function($a, $b) { return $a['tries'] <=> $b['tries']; });

			if(isset(self::$overwrite[0]) && isset($arrayRoutes[self::$overwrite[0]['key']])) {
				$return = [
					$arrayRoutes[self::$overwrite[0]['key']],
					self::$overwrite[0]['key'],
				];

				if(isset(self::$titles[$method][$return[1]])) {
					$return[] = self::$titles[$method][$return[1]];
				}
				
				return $return;
			} else {
				self::call404();
			}
		} else {
			self::call404();
		}
	}

	private static function storage(string $method, string $request, mixed $callback, string $title = "") {
		self::$router[$method][$request] = $callback;
		if(!empty($title)) { self::$titles[$method][$request] = $title; }

		return new self;
	}
}