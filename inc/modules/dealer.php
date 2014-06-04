<?php
class dealer {

	public function __construct() {
		global $control;
		$this->printList($control->module_parent);
	}

	private function printList($cid) {
		global $control;
		$list = new Listing($control->module_wrap, 'blocks', $cid, "GROUP BY city");
		$list->sortfield = "city";
		$list->sortby = "ASC";
		$list->getList();
		$list->getItem();
		$page->city = $list->item;

		$i = 0;
		$coords = array();
		foreach ($page->city as $key => $val) {
			$list = new Listing($control->module_wrap, 'blocks', $cid, "city='".$val->city."' AND ");
			$list->getList();
			$list->getItem();
			$item = $list->item;
			foreach ($item as $key => $val) {
				$val->coords = str_replace(array("(", ")"), "", $val->coords);
				$coords[]->coords = $val->coords;
			}
			$page->item[$i]->item = $item;

			$i++;
		}

		$control->coords = $coords;

		$page->text = all::c_data_all($cid, $control->template)->text;

		$page->name = $control->name;
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>