<?php

class slider {

	function Make($wrapper) {
		global $control;

		$list = new Listing('slider', 'blocks', 15);
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>