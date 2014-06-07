<?php

class filtersexcblock {

	function Make($wrapper) {
		global $control;

		// Активности
		$activity = array("Велосипедный маршрут", "Кросс-маршрут", "Скандинавская ходьба", "Серфинг", "Лыжный маршрут");

		foreach ($activity as $key => $val) {
			$page->activity[$key]->name = $val;
			if ($_GET['activity'] != "" && $key == $_GET['activity']) {
				$page->activity[$key]->selected = true;
			}
		}

		// Нагрузка
		if (isset($_GET['charge'])) {
			$page->charge_sel = trim($_GET['charge'], "/");
		}

		// Протяженность
		$length = sql::fetch_object(sql::query("SELECT MIN(length + 1 - 1) as minlength, MAX(length + 1 - 1) as maxlength FROM prname_b_exc"));

		$page->fromlength = $page->minlength = $length->minlength;
		$page->tolength = $page->maxlength = $length->maxlength;

		if (isset($_GET['length']) && $_GET['length'] != "") {
			$length = explode(";", trim($_GET['length'], "/"));
			$page->fromlength = $length[0];
			$page->tolength = $length[1];
		}


		// Продолжительность
		$duration = sql::fetch_object(sql::query("SELECT MIN(duration + 1 - 1) as minduration, MAX(duration + 1 - 1) as maxduration FROM prname_b_exc"));

		$page->fromduration = $page->minduration = $duration->minduration;
		$page->toduration = $page->maxduration = $duration->maxduration;

		if (isset($_GET['duration']) && $_GET['duration'] != "") {
			$duration = explode(";", trim($_GET['duration'], "/"));
			$page->fromduration = $duration[0];
			$page->toduration = $duration[1];
		}


		// Калории
		$calories = sql::fetch_object(sql::query("SELECT MIN(calories + 1 - 1) as mincalories, MAX(calories + 1 - 1) as maxcalories FROM prname_b_exc"));

		$page->fromcalories = $page->mincalories = $calories->mincalories;
		$page->tocalories = $page->maxcalories = $calories->maxcalories;

		if (isset($_GET['calories']) && $_GET['calories'] != "") {
			$calories = explode(";", trim($_GET['calories'], "/"));
			$page->fromcalories = $calories[0];
			$page->tocalories = $calories[1];
		}

		// Стоимость
		$prices = sql::fetch_object(sql::query("SELECT MIN(fullprice + 1 - 1) as minprice, MAX(fullprice + 1 - 1) as maxprice FROM prname_b_exc"));

		$page->fromprice = $page->minprice = $prices->minprice;
		$page->toprice = $page->maxprice = $prices->maxprice;

		if (isset($_GET['price']) && $_GET['price'] != "") {
			$price = explode(";", trim($_GET['price'], "/"));
			$page->fromprice = $price[0];
			$page->toprice = $price[1];
		}

		$page->excUrl = all::getUrl(21);

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>