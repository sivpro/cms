<?php

class seoblock {

	function Make($wrapper) {

		$page = all::c_data_all(1, "first");

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>