<?php

class newsblock {

	function Make($wrapper) {
		$list = new Listing("news", "blocks", 323);
		$list->sortfield = "date";
		$list->sortby = "desc";
		$list->limit = 3;
		$list->getList();
		$list->getItem();

		$page->item = $list->item;

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>