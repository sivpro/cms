<?php
require_once(DOC_ROOT."/inc/helpers/helper_tours.php");
require_once(DOC_ROOT."/inc/helpers/helper_exc.php");
class compare {

	public function __construct() {
		global $control;

		if (isset($_POST['mode'])) {
			if ($_POST['mode'] == "add") {
				return $this->compareAdd();
			}
			if ($_POST['mode'] == "delete") {
				return $this->compareDelete();
			}
		}

		$this->printList($control->module_parent);
	}


	private function printList($cid) {
		global $control;

		// Сравниваем туры или экскурсии
		$type = "t";
		if ($control->oper == "exc") {
			$type = "e";
		}

		$page->item = array();
		$citem = $_SESSION['compare'][$type];

		$table = $type == "t" ? "tour" : "exc";

		foreach ($citem as $key => $val) {
			$list = new Listing($table, "blocks", "all", "id=$val AND ");
			$list->getList();
			$list->getItem();
			$item = $list->item[0];
			$page->item[] = $item;
		}

		// Обрабатываем полученные данные
		if ($type == "t") {
			$page->item = helper_tours::tours_formatOne($page->item);
		}
		else {
			$page->item = helper_exc::exc_formatOne($page->item);
		}

		$page->count = count($page->item);

		// Выбор что сравнивать
		$page->toursUrl = all::getUrl(33);
		$page->excUrl = all::getUrl(33)."_aexc/";
		$page->toursCount = count($_SESSION['compare']['t']);
		$page->excCount = count($_SESSION['compare']['e']);
		$page->type = $type;
		$page->table = $table;
		if ($type == "t") {
			$page->name = "Сравнение туров";
		}
		else {
			$page->name = "Сравнение экскурсий";
		}

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	private function compareAdd() {
		$id = $_POST['id'] + 1 - 1;
		$type = $_POST['type'] == "t" ? "t" : "e";
		$table = $type == "t" ? "tour" : "exc";

		// проверяем существование тура
		$isset = sql::one_record("SELECT name FROM prname_b_$table WHERE id=$id");

		if ($isset) {
			if (!is_array($_SESSION['compare'][$type])) {
				$_SESSION['compare'][$type] = array();
			}

			array_push($_SESSION['compare'][$type], $id);
		}
		die("ok");
	}

	private function compareDelete() {
		$id = $_POST['id'] + 1 - 1;
		$type = $_POST['type'] == "t" ? "t" : "e";
		$table = $type == "t" ? "tour" : "exc";

		// проверяем существование тура
		$isset = sql::one_record("SELECT name FROM prname_b_$table WHERE id=$id");


		if ($isset) {
			$key = array_search($id, $_SESSION['compare'][$type]);
			unset($_SESSION['compare'][$type][$key]);
		}
		die("ok");
	}
}
?>