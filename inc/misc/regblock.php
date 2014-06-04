<?php

class regblock {
	function Make($wrapper) {

		$formName = "formReg";

		$formconfig = array(
			$formName => array(
				'nameReg' => array(
					'caption' => 'ФИО',
					'noempty' => true
				),
				'countryReg' => array(
					'caption' => 'Ваша страна',
					'noempty' => true
				),
				'cityReg' => array(
					'caption' => 'Ваш город',
					'noempty' => true
				),
				'phoneReg' => array(
					'caption' => 'Контактный телефон',
					'noempty' => true
				),
				'emailReg' => array(
					'caption' => 'E-mail',
					'noempty' => true,
					'login' => 'agent_login'
				),
				'passwordReg' => array(
					'caption' => 'Пароль',
					'noempty' => true
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsReg";		//div для показа ошибок
		$validator->highlight = 1;							//подсветка полей
		$validator->lastaction = "callback";				// действие при завершении
		$validator->sendMethod = "ajax";					//метод отправки
		$validator->preloaderId = "#preloaderReg";			//id прелоадера
		$validator->capId = "#captchaReg";					// id каптчи
		$validator->callback = "successSend";				// Функция Callback
		$validator->param = 'Reg';							// Параметр в функцию


		$page->script = $validator->getJsArray();
		$page->cap = $validator->getCaptcha();


		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>