<?php

class menu {

	function Make($wrapper) {
		global $control;

		$page = Tree::tree_all();

		if ($control->cid == 1) $page->main = "1";
		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>