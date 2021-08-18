<?php namespace Another;

/**
 * 
 */
class Router
{
	private array $routes = [];
	private array $info = [];

	function __construct()
	{
		/**
		 * 
		 */
		$this->info['URL'] = explode("?", $_SERVER['REQUEST_URI'])[0];

		/**
		 * 
		 */
		$this->info['METHOD'] = $_SERVER['REQUEST_METHOD'];

		/**
		 * 
		 */
		$_GET['ARGS'] = [];

		/**
		 * 
		 */
		$this->routes['_404'] = 'system/404.html';
	}

	public function get(string $path, string|object $controller): self
	{
		$this->register('GET', $path, $controller);
		return $this;
	}

	public function post(string $path, string|object $controller): self
	{
		$this->register('POST', $path, $controller);
		return $this;
	}

	public function put(string $path, string|object $controller): self
	{
		$this->register('PUT', $path, $controller);
		return $this;
	}

	public function delete(string $path, string|object $controller): self
	{
		$this->register('DELETE', $path, $controller);
		return $this;
	}

	public function patch(string $path, string|object $controller): self
	{
		$this->register('PATCH', $path, $controller);
		return $this;
	}

	public function define404(string $path): self
	{
		$this->routes['_404'] = $path;
		return $this;
	}

	public function call404(): void
	{
		http_response_code(404);

		if(file_exists('../'.$this->routes['_404'])) {
			include_once '../'.$this->routes['_404'];
		} else {
			include_once '../system/404.html';
		}

		exit;
	}

	public function resolveRoute(): void
	{
		$uri = explode("/", $this->info['URL']);
		
		/**
		 * 
		 */
		$possibilities = [];

		if(isset($this->routes[$this->info['METHOD']])) {
			foreach ($this->routes[$this->info['METHOD']] as $request => $controller) {
				$request_array = explode("/", $request);

				/**
				 * 
				 */
				$is_valid = false;

				/**
				 * 
				 */
				$replaces = 0;

				if(count($request_array) === count($uri)) {
					$is_valid = true;

					foreach ($request_array as $position => $request_key) {
						/**
						 * 
						 */
						if(substr($request_key,0,1) === ":") {
							$_GET['ARGS'][substr($request_key,1)] = $uri[$position];
							$request_key = $uri[$position];
							$replaces++;
						}

						/**
						 * 
						 */
						if($request_key !== $uri[$position]) {
							$is_valid = false;
						}
					}

					if($is_valid) {
						$possibilities[] = [
							"replaces" => $replaces,
							"controller" => $controller
						];
					}
				}
			}
		}

		usort($possibilities, function($a, $b) {
			return $a['replaces'] <=> $b['replaces'];
		});

		$control = $possibilities ? $possibilities[0]['controller'] : $this->routes['_404'];

		if(gettype($control) === 'object') {
			$control = $control();
		}

		$this->info['CURRENT'] = $control;
	}

	public function callRoute(): void
	{
		if($this->routes['_404'] === $this->info['CURRENT']) {
			http_response_code(404);
		}

		/**
		 * Checks if is a Controller
		 */
		if(strpos($this->info['CURRENT'], '::')) {
			$method = explode("::", $this->info['CURRENT']);
			$controller = $method[0].'.php';

			if(file_exists("../app/".$controller)) {
				include_once "../app/".$controller;

				if(isset($method)) {
					$call = array_reverse(explode("/", $method[0]))[0];
					$call = new $call();

					$method = $method[1];
					$call->$method();
				}
			}
		} else if(file_exists('../'.$this->info['CURRENT'])) {
			include_once '../'.$this->info['CURRENT'];
		}
	}

	private function register(string $method, string $path, string|object $controller): void
	{
		$this->routes[$method][$path] = $controller;
	}
}