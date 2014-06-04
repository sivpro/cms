<?php

class teamerblock {

	function Make($wrapper) {
		$list = new Listing("excman", "blocks", "all");
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		foreach ($page->item as $key => $val) {
			$names = explode(" ", $val->name);
			foreach ($names as $name) {
				$val->name2[]->name = $name;
			}
		}

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>