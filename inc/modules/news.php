<?php
class news {

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

		$bid = (int)$bid;
		$cri = " id<>$bid AND ";
		$list = new Listing('news','blocks',$control->module_parent, $cri);
		$list->limit = 3;
		$list->sortfield = 'date';
		$list->sortby ='desc';
		$list->getList();
		$list->getItem();
		$page->item = $list->item;



		$page->back = all::getUrl($control->module_parent).all::addUrl($this->page);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;

		$year = date("Y");
		$month = $this->getMonth(date("m"));


		if (all::getVar("year") != "") {
			$year = all::getVar("year");
			$cri = "YEAR(date)=".$year." AND ";
			$page->cri = $control->cid == 323?"Новости за ":"Статьи за ".$year." год";
		}

		if (all::getVar("r") != "") {
			$month = all::getVar("r");
			$cri = "YEAR(date)=".$year." AND MONTH(date)=".$month." AND ";
			$page->cri = $control->cid == 323?"Новости за ":"Статьи за ".$this->getMonth($month)." ".$year;
		}


		$list = new Listing('news', 'blocks', $cid, $cri);
		$list->limit = 2;
		$list->page = $control->page;
		$list->sortfield = 'date';
		$list->sortby ='desc';
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

		foreach ($page->item as $key => $val) {
			$date = $val->date_2;
			$dateArr = explode(" ", $date);
			$page->item[$key]->date_day = $dateArr[0];
			$page->item[$key]->date_month = $dateArr[1];
			if ($dateArr[2] < date("Y")) {
				$page->item[$key]->date_month .= " ".$dateArr[2];
			}
		}

		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	private function getMonth($num, $mode=null) {
		$arr = array("Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь");
		if ($mode == null) {
			$num = str_replace("0", "", $num);
			$num = intval($num);

			if ($num > 0 && $num < 13) {
				return $arr[$num-1];
			}
		}

		if ($mode != null) {
			if (in_array($num, $arr)) {
				return array_search($num, $arr);
			}
		}

	}
}
?>