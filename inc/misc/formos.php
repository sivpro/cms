<?php

class formos {
	function Make($wrapper) {

		$formName = "formOs";

		$config = array(
			$formName => array(
				'nameOs' => array(
					'caption' => 'Ваше имя',
					'noempty' => true
				),
				'phoneOs' => array(
					'caption' => 'Телефон'
				),
				'emailOs' => array(
					'caption' => 'E-mail',
					'noempty' => true
				),
				'textOs' => array(
					'caption' => 'Сообщение',
					'noempty' => true
				),
				'capOs' => array(
					'caption' => 'Введите цифры',
					'noempty' => true,
					'captcha' => true
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($config);
		$validator->showErrorMethod = "#showErrorsOs";  //div для показа ошибок
		$validator->highlight = 1;  //подсветка полей
		$validator->lastaction = "callback";  // действие при завершении
		$validator->sendMethod = "ajax";  //метод отправки
		$validator->preloaderId = "#preloaderOs"; //id прелоадера
		$validator->capId = "#captchaOs"; // id каптчи
		$validator->callback = "someFuntion";
		$validator->param = '1';


		$page->script = $validator->getJsArray();
		$page->cap = $validator->getCaptcha();


		$text = sprintt($page, 'templates/misc/'.$wrapper);
		return $text;
	}
}
?>