<?php
class Layout {
	public $name;
	public $controller;

	public function view($action, $model = null) {
		$view = new ViewResponse();
		$view->action = $action;
		$view->model = $model;
		$view->layout = $this;
		return $view;
	}
}
