<?php

class menu {

	function Make($wrapper) {
		global $control;

		$page->aboutUrl = all::getUrl(11);
		$page->excmanUrl = all::getUrl(19);
		$page->equipUrl = all::getUrl(32);
		$page->toursUrl = all::getUrl(20);
		$page->excUrl = all::getUrl(21);
		$page->infoUrl = all::getUrl(22);
		$page->officeUrl = all::getUrl(30);
		$page->dealersUrl = all::getUrl(31);

		$page->active = $control->cid;
		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>