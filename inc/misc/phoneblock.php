<?php

class phoneblock {

	function Make($wrapper) {
		$page = all::c_data_all(291, "phoneblock");

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>