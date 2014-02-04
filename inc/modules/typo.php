<?php
class typo {

	public function __construct() {
		global $control;
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
		$list = new Listing($control->module_wrap, 'blocks',$cid);
		$list->page = $control->page;
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');

	}
}
?>