<?php
class catgroup {

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


		//Достаем характеристики
		$parent = $control->cid;
		$blockid = $bid;
		$template = $control->template;

		$table = all::c_data_all($parent, $template);
		$table = $table->tablech_sel;


		if ($table != null) {

			//Формируем список характеристик
			$list = new Listing('spec', 'blocks', $table);
			$list->getList();
			$list->getItem();
			$item = $list->item;


			//Проверяем есть ли уже такая запись
			$is = sql::one_record("SELECT id FROM prname_ch_".$table." WHERE itemid=".$blockid);
			if ($is) {
				$data = sql::fetch_assoc(sql::query("SELECT * FROM prname_ch_".$table." WHERE itemid=".$blockid));
				foreach ($item as $key => $val) {
					$type = $val->sname;
					$item[$key]->value = $data[$type];
				}
			}

			$page->chitem = $item;
		}

		$list = new Listing($control->module_wrap, 'blocks', $control->module_parent, "id=".$bid." AND ");
		$list->getList();
		$list->getItem();
		$item = $list->item;


		foreach ($item as $key2 => $val2) {
			$price = preg_replace("/[\s]/", "", $val2->price);
			$price = number_format($price, 0, ".", " ");
			$item[$key2]->price = $price;

			if ($val2->rating == "" || $val2->rating == 0) $item[$key2]->rating = 0;

			$item[$key2]->rating2 = round($item[$key2]->rating);
			$item[$key2]->rating = round($item[$key2]->rating, 1);
			if ($val2->count == "" || $val2->count == 0) $item[$key2]->count = 0;

			$item[$key2]->vote = all::declOfNum($item[$key2]->count, array("оценка", "оценки", "оценок"));
			$item[$key2]->ratingword = all::declOfNum($item[$key2]->rating2, array("балл", "балла", "баллов"), false);

			$table = all::c_data_all($val2->parent, 'catgroup');
			$table = $table->tablech_sel;

			if ($table != "") {
				if (isset($_SESSION['compare'][$table])) {
					$compare = array_search($val2->id, $_SESSION['compare'][$table]);
					if ($compare !== false) $item[$key2]->compare = true;
				}
			}
			if (isset($_SESSION['basket']) && array_key_exists($val2->id, $_SESSION['basket'])) {
				$item[$key2]->inbasket = true;
			}
		}

		$page->item = $item;

		$page->back = all::getUrl($control->module_parent).all::addUrl($this->page);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;
		$list = new Listing($control->module_wrap, 'blocks', $cid);
		$list->limit = 2;
		$list->getList();
		$list->getItem();

		$item = $list->item;


		foreach ($item as $key2 => $val2) {
			$price = preg_replace("/[\s]/", "", $val2->price);
			$price = number_format($price, 0, ".", " ");
			$item[$key2]->price = $price;

			if ($val2->rating == "" || $val2->rating == 0) $item[$key2]->rating = 0;

			$item[$key2]->rating2 = round($item[$key2]->rating);
			$item[$key2]->rating = round($item[$key2]->rating, 1);
			if ($val2->count == "" || $val2->count == 0) $item[$key2]->count = 0;

			$item[$key2]->vote = all::declOfNum($item[$key2]->count, array("оценка", "оценки", "оценок"));
			$item[$key2]->ratingword = all::declOfNum($item[$key2]->rating2, array("балл", "балла", "баллов"), false);

			$table = all::c_data_all($val2->parent, 'catgroup');
			$table = $table->tablech_sel;

			if ($table != "") {
				if (isset($_SESSION['compare'][$table])) {
					$compare = array_search($val2->id, $_SESSION['compare'][$table]);
					if ($compare !== false) $item[$key2]->compare = true;
				}
			}
			if (isset($_SESSION['basket']) && array_key_exists($val2->id, $_SESSION['basket'])) {
				$item[$key2]->inbasket = true;
			}
		}

		$page->item = $item;

		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>