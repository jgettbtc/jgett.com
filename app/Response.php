<?php
abstract class Response {
	public $status_code = 200;
	public $content_type = 'text/html';

	abstract function render();

	protected function prepare() {
		http_response_code($this->status_code);
		header("Content-Type: $this->content_type; charset=UTF-8");
	}
}

class ContentResponse extends Response {
	public $content;

	public function render() {
		$this->prepare();
		echo $this->content;
	}
}

class ViewResponse extends Response {
    public $action;
    public $model;
    public $layout;

    private $sections = array();
    private $html = array();

    public function render() {
        $this->prepare();

        $layout = $this->layout;
        $controller = $layout->controller;
        $action = $this->action;
        $model = $this->model;

        $body = $controller->context->mvcapp->views_path.'/'.$controller->name.'/'.$action.'.php';

        if ($layout->name) {
            ob_start();
            require $body;
            $this->html["body"] = ob_get_clean();

            require $controller->context->mvcapp->views_path.'/'.$layout->name.'.php';
        } else {
            require $body;
        }
    }

    public function url($controller, $action) {
        $route = $this->layout->controller->context->mvcapp->findRoute($controller, $action);
        if ($route)
            return $route->path;
        else
            throw new Exception('Cannot found route.');
    }

    public function getBody() {
        if (array_key_exists("body", $this->html)) {
            return $this->html["body"];
        } else {
            return "";
        }
    }

    public function section($name) {
        if (!array_key_exists($name, $this->sections)) {
            $this->sections[$name] = new ViewSection();
        }
        return $this->sections[$name];
    }

    public function getSection($name, $required = false) {
        if (array_key_exists($name, $this->sections)) {
            return $this->sections[$name]->html;
        } else {
            if ($required)
                throw new Exception("Missing required section: $name");
            else
                return "";
        }
    }
}

class ViewSection {
    public $html;
    private $items = array();

    public function begin() {
        $this->html = "";
        ob_start();
    }

    public function end() {
        $this->html = ob_get_clean();
    }
}

class JsonResponse extends ContentResponse {
	public $content_type = 'application/json';

	public function render() {
		$this->prepare();
		header('Access-Control-Allow-Origin: *');
		echo json_encode($this->content);
	}
}
