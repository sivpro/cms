<?php

class authblock {

	function Make($wrapper) {

		if (isset($_SESSION['uid'])) {
			$page->auth = true;
			$page->cabinetUrl = all::getUrl(34);
		}
		else {
			$page->auth = false;
		}

		$formName = "formRem";

		$formconfig = array(
			$formName => array(
				'emailRem' => array(
					'caption' => 'E-mail',
					'noempty' => true
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsRem";		//div для показа ошибок
		$validator->highlight = 1;							//подсветка полей
		$validator->lastaction = "callback";				// действие при завершении
		$validator->sendMethod = "ajax";					//метод отправки
		$validator->preloaderId = "#preloaderRem";			//id прелоадера
		$validator->capId = "#captchaRem";					// id каптчи
		$validator->callback = "successSend";				// Функция Callback
		$validator->param = 'Rem';							// Параметр в функцию


		$page->script = $validator->getJsArray();
		$page->cap = $validator->getCaptcha();


		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}

}
?>