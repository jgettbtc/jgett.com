<?php
class HomeController extends Controller {
	public function view($action, $model = null) {
		return $this->layout('layout')->view($action, $model);
	}

	public function index() {
		$model = new stdClass();
		$model->title = 'Jgett.com';
		$model->message = 'You can do anything at Jgett.com!';
		return $this->view('index', $model);
	}

	public function about() {
		$model = new stdClass();
		$model->title = 'About';
		$model->message = 'This is the about page.';
		return $this->view('about', $model);
	}
}
