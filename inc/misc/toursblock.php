<?php

class toursblock {

	function Make($wrapper) {
		global $control;

		$page->toursUrl = all::getUrl(20);
		$page->excUrl = all::getUrl(21);

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>