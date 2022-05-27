<?php
class FaqController extends Controller {
	public function view($action, $model = null) {
		return $this->layout('layout')->view($action, $model);
	}

	public function index() {
        $model = new stdClass();
        $model->title = 'FAQ';
		return $this->view('index', $model);
	}

	public function twentyone_million_limit() {
		$model = new stdClass();
		$model->title = '21 Million Limit';
		return $this->view('21-million-limit', $model);
	}

	public function bitcoin_is_myspace() {
		$model = new stdClass();
		$model->title = 'Bitcoin is MySpace';
		return $this->view('bitcoin-is-myspace', $model);
	}
}
