<?php

require_once('app/includes/class.importer.php');

class ApiController extends Controller {
	public $response_type = 'json';

	public function guid() {
		return $this->json(array('guid' => $this->createGuid()));
	}

	public function sha256() {
		$result = null;

		$data = $this->context->requestVal('data');

		if (!$data)
			$result = $this->badRequest("missing required parameter (data)");
		else
			$result = $this->json(array('sha256' => hash('sha256', $data, false)));

		return $result;
	}

    public function pods() {

        $id_token = $this->context->requestVal('id_token');

        if (!$id_token) {
            $result = $this->badRequest("missing required paramter (id_token)");
        } else {
            $mysqli = new mysqli("localhost", "webapp", "badkitty", "jgettdata");
            $sql = sprintf("SELECT * FROM podcasts WHERE user_id_token = '%s'", $mysqli->real_escape_string($id_token));

            $res = $mysqli->query($sql);
            if (!$res)
                die($mysqli->error);

            $rows = array();

            while ($row = $res->fetch_assoc()) {
                $rows[] = $row;
            }

            $result = $this->json(array('pods' => array()));
        }

        return $result;
    }

    public function price() {
        $today = date('Y-m-d');
        $s2t = strtotime($_GET['d'] ?? false);
        $d = $s2t ? date('Y-m-d', $s2t) : $today;

        $importer = new Importer();
        $price = $importer->get_bitbo_price($d);

        $result = $this->json($price);

        return $result;
    }

    private function execUrl($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($json, true);
        return $data;
    }

	private function createGuid() {
	        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(16384, 20479),
			mt_rand(32768, 49151),
			mt_rand(0, 65535),
			mt_rand(0, 65535),
			mt_rand(0, 65535));
	}
}

