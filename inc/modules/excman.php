<?php
require_once(DOC_ROOT."/inc/helpers/helper_exc.php");
class excman extends helper_exc {

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

		// Экскурсовод
		$list = new Listing($control->module_wrap, 'blocks', "all", "id=$bid AND ");
		$list->getList();
		$list->getItem();
		$page = $list->item[0];


		// Проходимся по списку экскурсий
		$page->exc = $this->exc_formatList($page->exc);

		$page->back = all::getUrl($control->module_parent).all::addUrl($this->page);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;
		$list = new Listing("excman", 'blocks', $cid);
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		foreach ($page->item as $key => $val) {
			$names = explode(" ", $val->name);
			foreach ($names as $name) {
				$val->name2[]->name = $name;
			}
		}

		$page->text = all::c_data_all($cid, $control->template)->text;


		$page->name = $control->name;
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>