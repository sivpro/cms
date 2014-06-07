<?php
class helper_tours {

	public function tours_formatList($item) {
		foreach ($item as $key => $val) {
			// price formatting
			$val->fullprice = (int)$val->fullprice;
			$val->agentprice = (int)$val->agentprice;
			$difference = $val->fullprice - $val->agentprice;
			$val->fullprice = number_format($val->fullprice, 0, ".", " ");
			$val->agentprice = number_format($val->agentprice, 0, ".", " ");
			$val->difference = number_format($difference, 0, ".", " ");

			// Declination of nights
			$val->nights = all::declOfNum($val->nights, array("ночь", "ночи", "ночей"));

			// Compare
			if (in_array($val->id, $_SESSION['compare']['t'])) {
				$val->inCompare = true;
			}
		}

		return $item;
	}

	public function tours_formatOne($item) {
		global $control;

		foreach ($item as $key => $val) {
			// price formatting
			$val->fullprice = (int)$val->fullprice;
			$val->agentprice = (int)$val->agentprice;
			$difference = $val->fullprice - $val->agentprice;
			$val->buyprice = isset($_SESSION['uid']) ? $val->agentprice : $val->fullprice;
			$val->fullprice = number_format($val->fullprice, 0, ".", " ");
			$val->agentprice = number_format($val->agentprice, 0, ".", " ");
			$val->difference = number_format($difference, 0, ".", " ");

			// values for selects
			$val->cityfrom = all::b_data_all($val->cityfrom_sel, "valuer")->valuer;
			$val->cityto = all::b_data_all($val->cityto_sel, "valuer")->valuer;
			$val->room = all::b_data_all($val->room_sel, "valuer")->valuer;
			$val->food = all::b_data_all($val->food_sel, "valuer")->valuer;

			// TOUR DATES
			$tourdate = explode(";", $val->tourdate);
			$tourdates = array();
			$i = 0;

			foreach ($tourdate as $val2) {
				$date = explode(".", $val2);
				$tourdates[$i]->month = $date[1];
				$tourdates[$i]->year = $date[2];
				$tourdates[$i]->day = $date[0];
				$tourdates[$i]->date = $val2;

				// For calendar
				$tourdates[$i]->isodate = $tourdates[$i]->year."-".$tourdates[$i]->month."-".$tourdates[$i]->day;
				$tourdates[$i]->endisodate = date("Y-m-d", (mktime(0, 0, 0, $tourdates[$i]->month, $tourdates[$i]->day, $tourdates[$i]->year) + 60 * 60 * 24 * $val->nights));
				$i ++;
			}
			$val->tourdates = $tourdates;

			// Сохраняем в control для последующего использования в скриптах (в конце шаблона scriptsblock)
			$control->tourdates = $val->tourdates;



			// EXCURSION DATES
			$i = 0;
			foreach ($val->exc as $ekey => $eval) {
				$excdate = explode(";", $eval->excdate);
				$excdates = array();

				foreach ($excdate as $val2) {
					$date = explode(".", $val2);
					$excdates[$i]->month = $date[1];
					$excdates[$i]->year = $date[2];
					$excdates[$i]->day = $date[0];
					$excdates[$i]->date = $val2;

					// For calendar
					$excdates[$i]->isodate = $excdates[$i]->year."-".$excdates[$i]->month."-".$excdates[$i]->day;
					$excdates[$i]->name = $eval->name;
					$i ++;
				}
			}

			// Сохраняем в control для последующего использования в скриптах (в конце шаблона scriptsblock)
			$control->excdates = $excdates;

		}

		return $item;
	}
}
?>