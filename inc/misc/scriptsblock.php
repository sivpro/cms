<?php

class scriptsblock {

	function Make($wrapper) {
		global $control;

		// Тур
		if ($control->cid == 20 && $control->oper == "view") {
			$page->photo = true;
			$page->tour = true;

			$page->tourdates = $control->tourdates;
			$page->excdates = $control->excdates;
		}

		// Экскурсия
		if ($control->cid == 21 && $control->oper == "view") {
			$page->photo = true;
			$page->exc = true;
			$page->excdates = $control->excdates;
		}

		// Офисы
		if ($control->cid == 30) {
			$page->office = true;
			$page->coords = $control->coords;
		}

		// Дилеры
		if ($control->cid == 31) {
			$page->dealer = true;
			$page->coords = $control->coords;
		}

		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>