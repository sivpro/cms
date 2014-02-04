<?php
class os {

	public function __construct() {
		global $control;
		$this->printList($control->module_parent);
	}



	private function printList($cid) {
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


		if (isset($_POST) && count($_POST) > 0) {

			$validator->checkFields($this, $page);
			if($validator->success) {
				header("Content-type: text/html; charset=utf-8");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);


				$fields = array();
				$fields['name'] = "Контактное лицо";
				$fields['email'] = "E-mail";
				$fields['phone'] = "Телефон";
				$fields['text'] = "Текст сообщения";

				$array = array();


				$array['name'] = $validator->post['nameOs'];
				$array['email'] = $validator->post['emailOs'];
				$array['phone'] = $validator->post['phoneOs'];
				$array['text'] = $validator->post['textOs'];
				$array['date'] = date("Y-m-d");

				all::insert_block('os', 14, $array, 0);


				/*отправка на почту уведомления*/
				$msg = "";

				foreach ($fields as $key => $value) {
					$msg .= "".$value." - ";
					$msg .= $array[$key]."<br/>";
				}


				$email = $control->settings->email;
				$sitename = $control->settings->sitename;
				$email_sub = "Поступил вопрос с сайта";
				all::send_mail($email, $email_sub, $msg, false, false, "$sitename robot");

				die();
			}

		}
		$page->script = $validator->getJsArray();
		$page->cap = $validator->getCaptcha();

		$page->name = $control->name;
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>