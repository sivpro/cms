<?php
class office {

	public function __construct() {
		global $control;
		$this->printList($control->module_parent);
	}

	private function printList($cid) {
		global $control;
		$list = new Listing($control->module_wrap, 'blocks', $cid);
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$coords = array();
		foreach ($page->item as $key => $val) {
			$coordsmap = str_replace(array("(", ")"), "", $val->coords);
			$coords[]->coords = $coordsmap;
		}

		$control->coords = $coords;

		$page->text = all::c_data_all($cid, $control->template)->text;

		$page->name = $control->name;
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>