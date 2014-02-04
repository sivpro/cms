<?php
class subcatgroup {

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
		$list = new Listing($control->module_wrap, 'blocks', $control->module_parent, "id=".$bid." AND ");
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$page->back = all::getUrl($control->module_parent).all::addUrl($this->page);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;

		//Show limits
		$page->limits[]->limit = 9;
		$page->limits[]->limit = 18;
		$page->limits[]->limit = 36;
		$page->limits[]->limit = 54;
		$page->limits[]->limit = 72;
		$page->limits[]->limit = 99;

		if (all::getVar("limit") != "") {
			$limit = all::getVar("limit");
		}
		else {
			$limit = 9;
		}

		$page->limit = $limit;

		//Brands
		$list = new Listing($control->module_wrap, 'blocks', $control->module_parent, "GROUP BY brand");
		$list->getList();
		$list->getItem();
		$page->brand = $list->item;

		if (all::getVar("rand") != "") {
			$brand = all::getVar("rand");
		}
		else {
			$brand = "0";
		}

		$page->currentBrand = $brand;

		if ($brand != 0) {
			$brand = sql::one_record("SELECT brand FROM prname_b_catitem WHERE id=$brand");
			if ($brand) {
				$cri = " brand='".$brand."' AND ";
			}
		}

		if (!$cri) {
			$cri = "";
		}



		//Items
		$list = new Listing($control->module_wrap, 'blocks', $control->module_parent, $cri);
		$list->limit = $limit;
		$list->page = $control->page;
		$list->sortfield = $control->module_filtr[$control->module_wrap];
		$list->sortby = $control->module_filtrby[$control->module_wrap];
		$list->tmp_url = all::getUrl($control->module_parent)."/_limit".$page->limit."_rand".$page->currentBrand;
		$list->getList();
		$list->getPage();
		$list->getItem();
		$list->setPage($list->tmp_url);
		$page->item = $list->item;
		$page->page = $list->navigation;
		$page->url_last = $list->url_last;
		$page->url_p = $list->url_p;
		$page->url_n = $list->url_n;
		$page->url_next = $list->url_next;


		//Total count
		$page->totalcount = sql::one_record("SELECT COUNT(id) FROM prname_b_catitem WHERE parent=$cid");
		$page->totalcount = all::declOfNum($page->totalcount, array("товар", "товара", "товаров"));


		//Navigation out of its template
		$page->navigation = $this->getNavigation();

		//subs
		$parents = $control->parents;
		$subs = tree::tree_all($parents[2]);
		$page->subs = $subs->item;

		//current address
		$page->currentAddress = $control->module_url;

		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	private function getNavigation() {
		global $control;

		$page->items = Tree::tree_url();


		$page->name = $control->name;
		$page->printurl = all::getUrl($control->cid)."/printpage";

		foreach ($page->items as $key => $val) {
			if ($val->template == 'catgroup') {
				unset($page->items[$key]);
			}
		}

		if($control->bid) {
			if ($control->template == 'subcatgroup') {
				$template = 'catitem';

				$page->name = $page->items[count($page->items)]->name = sql::fetch_row(sql::query("select name from prname_b_".$template." where `id`='".$control->bid."'"),0,1);
				$page->printurl .= '/_aview_b'.$control->bid;
			}
			else {
				$page->name = $page->items[count($page->items)]->name = sql::fetch_row(sql::query("select name from prname_b_".$control->template." where `id`='".$control->bid."'"),0,1);
				$page->printurl .= '/_aview_b'.$control->bid;
			}
		}

		$this->html['text'] = sprintt($page, 'templates/misc/pagepatch.html');
		return $this->html['text'];
	}


}
?>