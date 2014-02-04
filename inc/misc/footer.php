<?php

class footer {

	function Make($wrapper) {
		global $control;

		$page = tree::tree_all();

		if ($control->cid == 1) $page->main = "1";

		$page->info = all::c_data_all(291, "phoneblock");
		$page->info2 = all::c_data_all(244, "footer");


		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>