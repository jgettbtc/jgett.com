<?php

require_once('app/includes/class.importer.php');

class BitcoinController extends Controller {
	public function view($action, $model = null) {
		return $this->layout('layout')->view($action, $model);
	}

	public function price() {
		$model = new stdClass();
		$model->title = 'Bitcoin Price Lookup';
		$model->today = date('Y-m-d');

		$s2t = strtotime($_GET['d'] ?? false);

		// default to today if querystring parameter is missing or not a date
		$model->date = $s2t ? date('Y-m-d', $s2t) : $model->today;

		// get prev and next dates
		$s2t = strtotime("-1 year", strtotime($model->date));
		$model->prev_year = date("Y-m-d", $s2t);

		$s2t = strtotime("+1 year", strtotime($model->date));
		$model->next_year = date("Y-m-d", $s2t);

		$s2t = strtotime("-1 month", strtotime($model->date));
		$model->prev_month = date("Y-m-d", $s2t);

		$s2t = strtotime("+1 month", strtotime($model->date));
		$model->next_month = date("Y-m-d", $s2t);

		$s2t = strtotime("-1 day", strtotime($model->date));
		$model->prev_day = date("Y-m-d", $s2t);

		$s2t = strtotime("+1 day", strtotime($model->date));
		$model->next_day = date("Y-m-d", $s2t);

		$importer = new Importer();

		// always get the current price
		$model->current = $importer->get_bitbo_price($model->today);

		if ($model->today === $model->date)
			$model->historical = $model->current; // reuse current, no need to get again
		else
			$model->historical = $importer->get_bitbo_price($model->date); // get the price on the date

		// get the price change
		$model->diff = $model->current['price'] - $model->historical['price'];

		// get the percent change
		if ($model->historical['price'] > 0)
			$model->percent_change = $model->diff / $model->historical['price'] * 100;
		else
			$model->percent_change = 0;

		$importer->close();

		return $this->view('price', $model);
	}
}
