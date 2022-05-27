<?php
class PodsController extends Controller {
    public function index() {
        $model = new stdClass();
        $model->title = 'Jgett.com :: Pods';
        return $this->layout('layout')->view('index', $model);
    }
}
