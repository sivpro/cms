<?php
class helper_exc {

	public function exc_formatList($item) {
		foreach ($item as $key => $val) {
			// price formatting
			$val->fullprice = (int)$val->fullprice;
			$val->agentprice = (int)$val->agentprice;
			$difference = $val->fullprice - $val->agentprice;
			$val->fullprice = number_format($val->fullprice, 0, ".", " ");
			$val->agentprice = number_format($val->agentprice, 0, ".", " ");
			$val->difference = number_format($difference, 0, ".", " ");

			// Compare
			if (in_array($val->id, $_SESSION['compare']['e'])) {
				$val->inCompare = true;
			}
		}

		return $item;
	}

	public function exc_formatOne($item) {
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

			// TOUR DATES
			$excdate = explode(";", $val->excdate);
			$excdates = array();
			$i = 0;

			foreach ($excdate as $val2) {
				$date = explode(".", $val2);
				$excdates[$i]->month = $date[1];
				$excdates[$i]->year = $date[2];
				$excdates[$i]->day = $date[0];
				$excdates[$i]->date = $val2;

				// For calendar
				$excdates[$i]->isodate = $excdates[$i]->year."-".$excdates[$i]->month."-".$excdates[$i]->day;
				$excdates[$i]->name = $val->name;
				$i ++;
			}
			$val->excdates = $excdates;

			// Сохраняем в control для последующего использования в скриптах (в конце шаблона scriptsblock)
			$control->excdates = $val->excdates;

			// Google Map
			$googlemap = $val->googlemap;
			preg_match("/src=\"([^\"]+)\"/Uis", $googlemap, $source);
			$val->googlemap = $source[1];
		}

		return $item;
	}
}
?>