<?php
abstract class Controller {
    public $name;
    public $context;
    public $response_type = 'html';

    private $sections = array();

    public function layout($name) {
        $layout = new Layout();
        $layout->name = $name;
        $layout->controller = $this;
        return $layout;
    }

    public function view($action, $model = null) {
        $layout = new Layout();
        $layout->controller = $this;
        return $layout->view($action, $model);
    }

    public function json($content) {
        $result = new JsonResponse();
        $result->content = $content;
        return $result;
    }

    public function notAllowed() {
        $result = null;
        $content = 'method not allowed: '.$this->context->method;

        if ($this->response_type === 'json') {
            $result = new JsonResponse();
            $result->content = array('message' => $content);
        } else {
            $result = new ContentResponse();
            $result->content = $content;
        }

        $result->status_code = 405;

        return $result;
    }

    public function badRequest($msg) {
        $result = null;
        $content = 'bad request: '.$msg;

        if ($this->response_type === 'json') {
            $result = new JsonResponse();
            $result->content = array('message' => $content);
        } else {
            $response = new ContentResponse();
            $result->content = $content;
        }

        $result->status_code = 400;

        return $result;
    }

    public function isGet() {
        return $this->context->method === 'GET';
    }

    public function isPost() {
        return $this->context->method === 'POST';
    }
}
