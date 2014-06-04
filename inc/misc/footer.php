<?php

class footer {

	function Make($wrapper) {
		global $control;

		$page = $control->settings;

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>