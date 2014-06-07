<?php
class cabinet {

	public function __construct() {
		global $control;
		$this->page = $control->page;

		if (isset($_POST) && count($_POST) > 0) {
			if (isset($_POST['nameContacts'])) {
				return $this->printContacts();
			}
			if (isset($_POST['companyReq'])) {
				return $this->printReq();
			}
			if (isset($_POST['serialPassport'])) {
				return $this->printPassport();
			}
		}
		if ($control->oper == 'view') {
			$this->printOne($control->bid);
		}
		else {
			$this->printList($control->module_parent);
		}
	}

	private function printContacts() {
		if (!isset($_SESSION['uid'])) {
			die();
		}
		$uid = $_SESSION['uid'];
		$formName = "formContacts";

		$formconfig = array(
			$formName => array(
				'nameContacts' => array(
					'caption' => 'ФИО',
					'noempty' => true
				),
				'countryContacts' => array(
					'caption' => 'Страна',
					'noempty' => true
				),
				'cityContacts' => array(
					'caption' => 'Город',
					'noempty' => true
				),
				'phoneContacts' => array(
					'caption' => 'Телефон',
					'noempty' => true
				),
				'emailContacts' => array(
					'caption' => 'E-mail',
					'noempty' => true
				),
				'skypeContacts' => array(
					'caption' => 'Skype'
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsContacts";		// div для показа ошибок
		$validator->highlight = 1;									// подсветка полей
		$validator->lastaction = "callback";						// действие при завершении
		$validator->sendMethod = "ajax";							// метод отправки
		$validator->preloaderId = "#preloaderContacts";				// id прелоадера
		$validator->capId = "#captchaContacts";						// id каптчи
		$validator->callback = "successSend";						// Функция Callback
		$validator->param = 'Contacts';								// Параметр в функцию

		if (isset($_POST) && count($_POST) > 0) {
			$validator->checkFields($this, $page);

			if ($validator->success) {
				$array = array();
				$array['name'] = $validator->post['nameContacts'];
				$array['country'] = $validator->post['countryContacts'];
				$array['city'] = $validator->post['cityContacts'];
				$array['phone'] = $validator->post['phoneContacts'];
				$array['email'] = $validator->post['emailContacts'];
				$array['skype'] = $validator->post['skypeContacts'];

				// if ($validator->post['passAccountFirm'] != "") {
				// 	$array['password'] = md5($validator->post['passAccountFirm'].$config['md5']);
				// }


				$updateStr = "UPDATE prname_b_agent SET ";

				foreach ($array as $key=>$val) {
					$updateStr .= "`".$key."`='".$val."',";
				}

				$updateStr = substr($updateStr, 0, strlen($updateStr)-1);
				$updateStr .= " WHERE id=$uid";

				sql::query($updateStr);

				// // отправка уведомления пользователю
				// if ($array['password'] != "") {
				// 	$firm = all::b_data_all($_SESSION['uid'], $_SESSION['utype']);
				// 	$email = $firm->login;
				// 	$sitename = $control->settings->sitename;

				// 	$theme = "Смена пароля";

				// 	// Урл сайта
				// 	$mailpage->siteUrl = "http://".$_SERVER['HTTP_HOST'];
				// 	// Реквизиты входа
				// 	$mailpage->email = $firm->login;
				// 	$mailpage->password = $validator->post['passAccountFirm'];
				// 	// Номер горячей линии
				// 	$mailpage->phone = $control->settings->phone;
				// 	// Меню
				// 	$mailpage->menu = tree::tree_all()->item;

				// 	$msg = sprintt($mailpage, 'mailtemplates/password_change_firm.html');


				// 	all::send_mail($email, $theme, $msg, false, false, "$sitename robot");
				// }

				die();

			}
		}

		$scriptContacts = $validator->getJsArray();
		return $scriptContacts;
	}

	private function printPassport() {
		if (!isset($_SESSION['uid'])) {
			die();
		}
		$uid = $_SESSION['uid'];

		$formName = "formPassport";

		$formconfig = array(
			$formName => array(
				'serialPassport' => array(
					'caption' => 'Серия паспорта',
					'noempty' => true
				),
				'numberPassport' => array(
					'caption' => 'Номер',
					'noempty' => true
				),
				'bywhoPassport' => array(
					'caption' => 'Кем выдан',
					'noempty' => true
				),
				'whenPassport' => array(
					'caption' => 'Когда выдан',
					'noempty' => true
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsPassport";		// div для показа ошибок
		$validator->highlight = 1;									// подсветка полей
		$validator->lastaction = "callback";						// действие при завершении
		$validator->sendMethod = "ajax";							// метод отправки
		$validator->preloaderId = "#preloaderPassport";				// id прелоадера
		$validator->capId = "#captchaPassport";						// id каптчи
		$validator->callback = "successSend";						// Функция Callback
		$validator->param = 'Contacts';								// Параметр в функцию

		if (isset($_POST) && count($_POST) > 0) {
			$validator->checkFields($this, $page);

			if ($validator->success) {
				$array = array();
				$array['serial'] = $validator->post['serialPassport'];
				$array['number'] = $validator->post['numberPassport'];
				$array['bywho'] = $validator->post['bywhoPassport'];
				$array['when'] = $validator->post['whenPassport'];


				$updateStr = "UPDATE prname_b_agent SET ";

				foreach ($array as $key=>$val) {
					$updateStr .= "`".$key."`='".$val."',";
				}

				$updateStr = substr($updateStr, 0, strlen($updateStr)-1);
				$updateStr .= " WHERE id=$uid";

				sql::query($updateStr);

				die();

			}
		}

		$scriptPassport = $validator->getJsArray();
		return $scriptPassport;
	}

	private function printReq() {
		if (!isset($_SESSION['uid'])) {
			die();
		}
		$uid = $_SESSION['uid'];

		$formName = "formReq";

		$formconfig = array(
			$formName => array(
				'companyReq' => array(
					'caption' => 'Наименование организации',
					'noempty' => true
				),
				'innReq' => array(
					'caption' => 'ИНН',
					'noempty' => true
				),
				'kppReq' => array(
					'caption' => 'КПП',
				),
				'rsReq' => array(
					'caption' => 'Р/с',
				),
				'ksReq' => array(
					'caption' => 'К/с',
				),
				'bankReq' => array(
					'caption' => 'Банк',
				),
				'bikReq' => array(
					'caption' => 'Бик',
				),
				'postReq' => array(
					'caption' => 'Должность',
					'noempty' => true
				),
				'osReq' => array(
					'caption' => 'Действует на основании',
					'noempty' => true
				),
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsReq";		// div для показа ошибок
		$validator->highlight = 1;									// подсветка полей
		$validator->lastaction = "callback";						// действие при завершении
		$validator->sendMethod = "ajax";							// метод отправки
		$validator->preloaderId = "#preloaderReq";				// id прелоадера
		$validator->capId = "#captchaReq";						// id каптчи
		$validator->callback = "successSend";						// Функция Callback
		$validator->param = 'Contacts';								// Параметр в функцию

		if (isset($_POST) && count($_POST) > 0) {
			$validator->checkFields($this, $page);

			if ($validator->success) {
				$array = array();
				$array['company'] = $validator->post['companyReq'];
				$array['inn'] = $validator->post['innReq'];
				$array['kpp'] = $validator->post['kppReq'];
				$array['rs'] = $validator->post['rsReq'];
				$array['ks'] = $validator->post['ksReq'];
				$array['bank'] = $validator->post['bankReq'];
				$array['bik'] = $validator->post['bikReq'];
				$array['post'] = $validator->post['postReq'];
				$array['os'] = $validator->post['osReq'];


				$updateStr = "UPDATE prname_b_agent SET ";

				foreach ($array as $key=>$val) {
					$updateStr .= "`".$key."`='".$val."',";
				}

				$updateStr = substr($updateStr, 0, strlen($updateStr)-1);
				$updateStr .= " WHERE id=$uid";

				sql::query($updateStr);

				die();

			}
		}

		$scriptReq = $validator->getJsArray();
		return $scriptReq;
	}

	private function printList($cid) {
		global $control;

		if (!isset($_SESSION['uid'])) {
			return;
		}

		$uid = $_SESSION['uid'];
		$agent = all::b_data_all($uid, "agent");
		$page = $agent;

		$page->text = all::c_data_all($cid, $control->template)->text;

		// Форма контактов
		$page->scriptContacts = $this->printContacts();

		if ($page->agency) {
			// Форма реквизитов
			$page->scriptReq = $this->printReq();
		}
		else {
			// Форма паспортных данных
			$page->scriptPassport = $this->printPassport();
		}

		$page->pname = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>