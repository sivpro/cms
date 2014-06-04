<?php

class phoneblock {

	function Make($wrapper) {
		global $control;
		$page = $control->settings;

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>