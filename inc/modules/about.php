<?php
class about {

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
		$page = all::b_data_all($bid, $control->module_wrap);

		$page->back = all::getUrl($control->module_parent).all::addUrl($this->page);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;
		$list = new Listing("texthtml", 'blocks', $cid);
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$list = new Listing("document", 'blocks', $cid);
		$list->getList();
		$list->getItem();
		$page->document = $list->item;

		$page->text = all::c_data_all($cid, $control->template)->text;

		$page->name = $control->name;
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>