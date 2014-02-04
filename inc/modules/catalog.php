<?php
class catalog {

	public function __construct() {
		global $control;
		$this->page = $control->page;

		if (isset($_POST) && count($_POST) > 0) {
			if ($_POST['mode'] == 'rate') {
				return $this->rate();
			}
		}

		$this->printList($control->module_parent);
	}

	private function printList($cid) {
		global $control;

		$page = Tree::tree_all(381);

		foreach ($page->item as $key => $val) {
			$list = new Listing('catitem', 'blocks', $val->id);
			$list->limit = 4;
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

			$page->item[$key]->item = $item;

		}

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	private function rate() {
		$id = (float)$_POST['id'];

		$rating = (float)$_POST['rating'];

		$info = sql::fetch_assoc(sql::query("SELECT rating, count FROM prname_b_catitem WHERE id=".$id));
		$newRating = ($info['rating'] * $info['count'] + $rating) / ($info['count'] + 1);
		sql::query("UPDATE prname_b_catitem SET rating='".$newRating."', count='".($info['count'] + 1)."' WHERE id=".$id);
		$_SESSION['rate'][$id] = $id;

		die(round($newRating, 2));
	}
}
?>