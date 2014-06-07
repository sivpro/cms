<?php

class filtersblock {

	function Make($wrapper) {
		global $control;

		// Города вылета
		$sql = "SELECT city.* FROM prname_b_valuer city, prname_b_tour tour WHERE city.id=tour.cityfrom GROUP BY tour.cityfrom ORDER BY city.valuer";
		$query = sql::query($sql);

		while($res = sql::fetch_object($query)) {
			$page->cityfrom[] = $res;
		}

		if (isset($_GET['cityfrom']) && $_GET['cityfrom'] != "") {
			$page->cityfrom_sel = trim($_GET['cityfrom'], "/");
		}


		// Города прилета
		$sql = "SELECT city.* FROM prname_b_valuer city, prname_b_tour tour WHERE city.id=tour.cityto GROUP BY tour.cityto ORDER BY city.valuer";
		$query = sql::query($sql);

		while($res = sql::fetch_object($query)) {
			$page->cityto[] = $res;
		}

		if (isset($_GET['cityto']) && $_GET['cityto'] != "") {
			$page->cityto_sel = trim($_GET['cityto'], "/");
		}

		// Типы номеров
		$list = new Listing("valuer", "blocks", 26);
		$list->getList();
		$list->getItem();
		$page->room = $list->item;

		if (isset($_GET['room']) && $_GET['room'] != "") {
			$page->room_sel = trim($_GET['room'], "/");
		}


		// Ночей
		$prices = sql::fetch_object(sql::query("SELECT MIN(nights + 1 - 1) as minnights, MAX(nights + 1 - 1) as maxnights FROM prname_b_tour"));

		$page->fromnights = $page->minnights = 3;
		$page->tonights = $page->maxnights = 16;

		if (isset($_GET['nights']) && $_GET['nights'] != "") {
			$nights = explode(";", trim($_GET['nights'], "/"));
			$page->fromnights = $nights[0];
			$page->tonights = $nights[1];
		}


		// Звезд
		for ($i=2; $i<6; $i++) {
			$page->stars[$i]->val = $i;
			$page->stars[$i]->text = $i." *";
		}

		if (isset($_GET['stars']) && $_GET['stars'] != "") {
			$page->stars_sel = trim($_GET['stars'], "/");
		}




		// Питание
		$list = new Listing("valuer", "blocks", 25);
		$list->getList();
		$list->getItem();
		$page->food = $list->item;

		if (isset($_GET['food'])) {
			$getfood = $_GET['food'];
			foreach ($getfood as $val) {
				$val = trim($val, "/");
			}
			foreach ($page->food as $key => $val) {
				if (in_array($val->id, $_GET['food'])) {
					$val->checked = true;
				}
			}
		}

		// Стоимость
		$prices = sql::fetch_object(sql::query("SELECT MIN(fullprice + 1 - 1) as minprice, MAX(fullprice + 1 - 1) as maxprice FROM prname_b_tour"));

		$page->fromprice = $page->minprice = $prices->minprice;
		$page->toprice = $page->maxprice = $prices->maxprice;

		if (isset($_GET['price']) && $_GET['price'] != "") {
			$price = explode(";", trim($_GET['price'], "/"));
			$page->fromprice = $price[0];
			$page->toprice = $price[1];
		}

		$page->toursUrl = all::getUrl(20);

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>