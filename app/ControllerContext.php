<?php
class ControllerContext {
        public $method;
	public $route;
	public $mvcapp;

        public function __construct() {
                $this->method = $_SERVER['REQUEST_METHOD'];
        }

	public function createController() {
		if (class_exists($this->route->controller_class)) {
			$result = new $this->route->controller_class();
			$result->name = $this->route->controller;
			$result->context = $this;
			return $result;
		} else {
			return false;
		}
	}

        public function requestVal($key) {
                return MvcApp::getArrayVal($_REQUEST, $key);
        }

        public function getVal($key) {
                return MvcApp::getArrayVal($_GET, $key);
        }

        public function postVal($key) {
                return MvcApp::getArrayVal($_POST, $key);
        }
}
?>
