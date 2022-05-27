<?php
require 'Route.php';
require 'Layout.php';
require 'Response.php';
require 'Controller.php';
require 'ControllerContext.php';

class MvcApp {
	private $_routes = array();

	public $controllers_path;
	public $views_path;
	public $app_root;

	public function __construct($arr = null) {
		$this->controllers_path = self::getArrayVal($arr, 'controllers_path') ?? __DIR__.'/controllers';
		$this->views_path = self::getArrayVal($arr, 'views_path') ?? __DIR__.'/views';
		$this->app_root = self::getArrayVal($arr,  'app_root') ?? '/';
	}

	public function registerRoute($arr) {
		$route = new Route();
		$route->path = $this->makePath(self::getArrayVal($arr, 'path'));
                $route->controller = self::getArrayVal($arr, 'controller') or die('missing required key: controller');
                $route->action = self::getArrayVal($arr, 'action') or die('missing required key: action');
                $route->controller_class = self::getArrayVal($arr, 'controller_class') ?? ucfirst($route->controller).'Controller';
		$route->methods = self::toArray(self::getArrayVal($arr, 'methods')) ?? array('GET', 'POST');
		$this->_routes[] = $route;
	}

	public function getRoute() {
		foreach ($this->_routes as $route) {
			if ($route->path === $this->getPath())
				return $route;
		}
		return null;
	}

	public function findRoute($controller, $action) {
		foreach ($this->_routes as $route) {
			if ($route->controller === $controller && $route->action === $action)
				return $route;
		}
		return null;
	}

	public function makePath($p) {
		if (!$p) return $this->app_root;
		if (!self::endsWith($p, '/')) $p .= '/';
		if (self::startsWith($p, '/')) return $p;
		return $this->app_root.$p;
	}

	public function handleRequest() {
		$route = $this->getRoute();
		$response = $this->execute($route);
		$response->render();
	}

	private function execute($route) {
		$response = null;

		if ($route) {
			$ctx = new ControllerContext();
			$ctx->mvcapp = $this;
			$ctx->route = $route;

			require $this->controllers_path.'/'.$ctx->route->controller.'.php';

			$controller = $ctx->createController();
			$action = $ctx->route->action;

			if ($controller) {
				if (in_array($ctx->method, $route->methods)) {
					if (method_exists($controller, $action)) {
					        $response = call_user_func(array($controller, $action));
					} else {
			        		$response = $controller->badRequest("unknown action '$action'");
					}
				} else {
					$response = $controller->notAllowed();
				}
			} else {
				$response = $this->notFound();
			}
		} else {
			$response = $this->notFound();
		}

		return $response;
	}

    public function notFound() {
        $response = new ContentResponse();
        $response->status_code = 404;
        $response->content = 'not found: '.self::getPath();
        return $response;
    }

	public static function dumpVar($v, $die = false) {
		echo '<pre>';
		if ($v !== null) print_r($v);
		else echo '[null]';
		echo '</pre>';
		if ($die) die();
	}

	public static function startsWith($haystack, $needle) {
	        $length = strlen($needle);
	        return substr($haystack, 0, $length) === $needle;
	}

	public static function endsWith($haystack, $needle) {
	        $length = strlen($needle);
	        if (!$length) return true;
        	return substr($haystack, -$length) === $needle;
	}

	private static function getPath() {
	        $request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);
        	$path = $request_uri[0];
	        if (!self::endsWith($path, '/')) $path .= '/';
        	return $path;
	}

	public static function toObject($x) {
        	return json_decode(json_encode($x));
	}

	public static function toArray($x) {
		if ($x === null) return $x;
		if (is_array($x)) return $x;
		return array($x);
	}

	public static function getArrayVal($arr, $key) {
		if (!is_array($arr)) return null;
        	return isset($arr[$key]) ? $arr[$key] : null;
	}
}

