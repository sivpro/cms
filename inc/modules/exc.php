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

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;
		$list = new Listing($control->module_wrap, 'blocks', $cid);
		$list->page = $control->page;
		$list->tmp_url = all::getUrl($control->module_parent);
		$list->getList();
		$list->getItem();
		$list->getPage();

		$page->item = $list->item;
		$page->page = $list->navigation;
		$page->url_last = $list->url_last;
		$page->url_p = $list->url_p;
		$page->url_n = $list->url_n;
		$page->url_next = $list->url_next;

		$page->item = $this->exc_formatList($page->item);


		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>