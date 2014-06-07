<?php
require_once(DOC_ROOT."/inc/helpers/helper_exc.php");
class exc extends helper_exc {

	public function __construct() {
		global $control;
		$this->page = $control->page;
		if ($control->oper == 'view') {
			$this->printOne($control->bid);
		}
		else {
			$this->printList($control->module_parent);
		}
	}

	private function printOne($bid) {
		global $control;
		$bid = $bid + 1 - 1;

		// Экскурсия
		$list = new Listing($control->module_wrap, 'blocks', "all", "id=$bid AND ");
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$page->item = $this->exc_formatOne($page->item);


		// Фотографии тура
		$list = new Listing("photoexc", "blocks", 28, "exc=$bid AND ");
		$list->getList();
		$list->getItem();
		$page->image = $list->item;

		foreach ($page->image as $key => $val) {
			$val->size = all::getRandom(1,2);
		}

		if (isset($_SESSION['uid'])) {
			$page->auth = true;
		}

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;

		// Фильтры
		$cri = "";

		// Нагрузка
		if ($_GET['charge'] != "") {
			$charge = trim($_GET['charge'], "/");
			$cri .= "charge='$charge' AND ";
		}

		// Активность
		if ($_GET['activity'] != "") {
			$activity = trim($_GET['activity'], "/");

			// Активности
			$activityArray = array("Велосипедный маршрут", "Кросс-маршрут", "Скандинавская ходьба", "Серфинг", "Лыжный маршрут");
			$cri .= "activity LIKE '%".$activityArray[$activity]."%' AND ";
		}

		// Стоимость
		if ($_GET['price'] != "") {
			$price = explode(";", trim($_GET['price'], "/"));
			$minprice = $price[0];
			$maxprice = $price[1];
			$cri .= "fullprice + 1 - 1 >= $minprice AND fullprice + 1 - 1 <= $maxprice AND ";
		}

		// Протяженность
		if ($_GET['length'] != "") {
			$length = explode(";", trim($_GET['length'], "/"));
			$minlength = $length[0];
			$maxlength = $length[1];
			$cri .= "length + 1 - 1 >= $minlength AND length + 1 - 1 <= $maxlength AND ";
		}

		// Продолжительность
		if ($_GET['duration'] != "") {
			$duration = explode(";", trim($_GET['duration'], "/"));
			$minduration = $duration[0];
			$maxduration = $duration[1];
			$cri .= "duration + 1 - 1 >= $minduration AND duration + 1 - 1 <= $maxduration AND ";
		}

		// Калории
		if ($_GET['calories'] != "") {
			$calories = explode(";", trim($_GET['calories'], "/"));
			$mincalories = $calories[0];
			$maxcalories = $calories[1];
			$cri .= "calories + 1 - 1 >= $mincalories AND calories + 1 - 1 <= $maxcalories AND ";
		}

		$list = new Listing($control->module_wrap, 'blocks', $cid, $cri);
		$list->limit = 6;
		$list->page = $control->page;
		$list->tmp_url = all::getUrl($control->module_parent);
		$list->getList();
		$list->getItem();
		$list->getPage();

		$page->item = $list->item;
		$page->page = $list->navigation;
		$page->itemSelector = ".item-wrapper";
		$page->containerSelector = "#exc-list";
		$page->url_last = $list->url_last;
		$page->url_p = $list->url_p;
		$page->url_n = $list->url_n;
		$page->url_next = $list->url_next;

		$page->item = $this->exc_formatList($page->item);

		if (isset($_SESSION['uid'])) {
			$page->auth = true;
		}


		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>