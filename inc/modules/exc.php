<?php
require_once(DOC_ROOT."/inc/helpers/helper_exc.php");
class exc extends helper_exc {

	public function __construct() {
		global $control;
		$this->page = $control->page;

		if (isset($_POST['priceBuy'])) {
			if (isset($_SESSION['uid'])) {
				return $this->printBuyAuth();
			}
			else {
				return $this->printBuy();
			}
		}
		if ($control->oper == 'view') {
			$this->printOne($control->bid);
		}
		else {
			$this->printList($control->module_parent);
		}
	}

	private function printBuy() {
		global $control;

		$formName = "formBuy";

		$formconfig = array(
			$formName => array(
				'dateBuy' => array(
					'caption' => 'Дата тура',
					'noempty' => true
				),
				'nameBuy' => array(
					'caption' => 'ФИО',
					'noempty' => true
				),
				'phoneBuy' => array(
					'caption' => 'Телефон',
					'noempty' => true
				),
				'emailBuy' => array(
					'caption' => 'E-mail',
					'noempty' => true
				),
				'priceBuy' => array(
					'caption' => 'Цена',
					'noempty' => true
				),
				'tourBuy' => array(
					'caption' => 'Тур',
					'noempty' => true
				),
				'touridBuy' => array(
					'caption' => 'Тур',
					'noempty' => true
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsBuy";		// div для показа ошибок
		$validator->highlight = 1;							// подсветка полей
		$validator->lastaction = "callback";				// действие при завершении
		$validator->sendMethod = "ajax";					// метод отправки
		$validator->preloaderId = "#preloaderBuy";			// id прелоадера
		$validator->capId = "#captchaBuy";					// id каптчи
		$validator->callback = "successSend";				// Функция Callback
		$validator->param = 'Buy';							// Параметр в функцию

		if (isset($_POST) && count($_POST) > 0) {
			$validator->checkFields($this, $page);

			if ($validator->success) {
				$array = array();
				$array['name'] = $validator->post['nameBuy'];
				$array['date'] = $validator->post['dateBuy'];
				$array['phone'] = $validator->post['phoneBuy'];
				$array['email'] = $validator->post['emailBuy'];
				$array['tour'] = $validator->post['tourBuy'];
				$array['tourid'] = $validator->post['touridBuy'];
				$array['price'] = $validator->post['priceBuy'];
				$array['pay'] = 0;
				$array['agent'] = 0;

				// Туристы
				if (isset($_POST['ageBuy'])) {
					if (count($_POST['ageBuy']) > 1) {
						$tourist = "<p>Взрослые</p><table border=1 cellpadding=10 cellspacing=1>
							<tr>
								<th>Пол</th>
								<th>Возраст</th>
								<th>Рост</th>
								<th>И.Ф.</th>
								<th>Паспорт</th>
								<th>Действителен по</th>
								<th>Виза</th>
							</tr>";

						foreach ($_POST['ageBuy'] as $key => $val) {
							if ($key == 0) continue;
							$tourist .= "<tr>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['genderBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['ageBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['heightBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['innameBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['passportBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['dateToBuy'][$key]))."</td>";
							if ($_POST['visaBuyh'][$key] == 1) {
								$tourist .= "<td>Да</td>";
							}
							else {
								$tourist .= "<td>Нет</td>";
							}
							$tourist .= "</tr>";
						}

						$tourist .= "</table>";
					}
				}

				// Дети
				if (isset($_POST['ageChildBuy'])) {
					if (count($_POST['ageChildBuy']) > 1) {
						$tourist .= "<p>Дети</p><table border=1 cellpadding=10 cellspacing=1>
							<tr>
								<th>Пол</th>
								<th>Возраст</th>
								<th>Рост</th>
							</tr>";

						foreach ($_POST['ageBuy'] as $key => $val) {
							if ($key == 0) continue;
							$tourist .= "<tr>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['genderChildBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['ageChildBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['heightChildBuy'][$key]))."</td>";
							$tourist .= "</tr>";
						}

						$tourist .= "</table>";
					}
				}

				$array['tourist'] = $tourist;

				$orderId = all::insert_block("tourorder", 37, $array, 1);


				// отправка уведомления пользователю (с генерацией уникальной ссылки и записью ее в таблицу)
					$email = $array['email'];
					$sitename = $control->settings->sitename;

					$mailpage->theme = "Заказ экскурсии";

					// Урл сайта
					$mailpage->siteUrl = "http://".$_SERVER['HTTP_HOST'];

					// Уникальная ссылка
					$md5 = md5($array['name'].$array['email']."exc".time());
					$mailpage->uniqlink = $mailpage->siteUrl."/payment/?order=$md5/";

					// Обращение
					$mailpage->name = $array['name'];
					// Номер заказа
					$mailpage->orderid = $orderId;

					sql::query("INSERT INTO prname_uniqlinks
						(name, email, phone, link, orderid, type)
						VALUES(
							'".$array['name']."',
							'".$array['email']."',
							'".$array['phone']."',
							'".$md5."',
							'".$orderId."',
							'exc'
							)");


					$msg = sprintt($mailpage, 'mailtemplates/touser/userexcorder.html');


					all::send_mail($email, $mailpage->theme, $msg, false, false, "$sitename robot");

				// отправка уведомления админу
					$email = $control->settings->email;
					$mailpage->theme = "Заказ экскурсии на сайте Olli Tours";
					$mailpage->excname = $array['tour'];
					$msg = sprintt($mailpage, 'mailtemplates/toadmin/excorder.html');

					all::send_mail($email, $mailpage->theme, $msg, false, false, "$sitename robot");

				die();

			}
		}

		$scriptByu = $validator->getJsArray();
		return $scriptByu;
	}

	private function printBuyAuth() {
		global $control;

		$uid = $_SESSION['uid'];

		$formName = "formBuy";

		$formconfig = array(
			$formName => array(
				'dateBuy' => array(
					'caption' => 'Дата тура',
					'noempty' => true
				),
				'priceBuy' => array(
					'caption' => 'Цена',
					'noempty' => true
				),
				'tourBuy' => array(
					'caption' => 'Тур',
					'noempty' => true
				),
				'touridBuy' => array(
					'caption' => 'Тур',
					'noempty' => true
				)
			)
		);


		include_once("libs/formvalidator.php");$_SESSION['langs'] = 'ru';
		$validator = new formvalidator($formconfig);
		$validator->showErrorMethod = "#showErrorsBuy";		// div для показа ошибок
		$validator->highlight = 1;							// подсветка полей
		$validator->lastaction = "callback";				// действие при завершении
		$validator->sendMethod = "ajax";					// метод отправки
		$validator->preloaderId = "#preloaderBuy";			// id прелоадера
		$validator->capId = "#captchaBuy";					// id каптчи
		$validator->callback = "successSend";				// Функция Callback
		$validator->param = 'BuyAuth';							// Параметр в функцию

		if (isset($_POST) && count($_POST) > 0) {
			$validator->checkFields($this, $page);

			if ($validator->success) {
				$array = array();
				$agent = all::b_data_all($uid, "agent");
				$array['name'] = $agent->name;
				$array['date'] = $validator->post['dateBuy'];
				$array['phone'] = $agent->phone;
				$array['email'] = $agent->email;
				$array['tour'] = $validator->post['tourBuy'];
				$array['tourid'] = $validator->post['touridBuy'];
				$array['price'] = $validator->post['priceBuy'];
				$array['pay'] = 0;
				$array['agent'] = 1;

				// Туристы
				if (isset($_POST['ageBuy'])) {
					if (count($_POST['ageBuy']) > 1) {
						$tourist = "<p>Взрослые</p><table border=1 cellpadding=10 cellspacing=1>
							<tr>
								<th>Пол</th>
								<th>Возраст</th>
								<th>Рост</th>
								<th>И.Ф.</th>
								<th>Паспорт</th>
								<th>Действителен по</th>
								<th>Виза</th>
							</tr>";

						foreach ($_POST['ageBuy'] as $key => $val) {
							if ($key == 0) continue;
							$tourist .= "<tr>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['genderBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['ageBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['heightBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['innameBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['passportBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['dateToBuy'][$key]))."</td>";
							if ($_POST['visaBuyh'][$key] == 1) {
								$tourist .= "<td>Да</td>";
							}
							else {
								$tourist .= "<td>Нет</td>";
							}
							$tourist .= "</tr>";
						}

						$tourist .= "</table>";
					}
				}

				// Дети
				if (isset($_POST['ageChildBuy'])) {
					if (count($_POST['ageChildBuy']) > 1) {
						$tourist .= "<p>Дети</p><table border=1 cellpadding=10 cellspacing=1>
							<tr>
								<th>Пол</th>
								<th>Возраст</th>
								<th>Рост</th>
							</tr>";

						foreach ($_POST['ageBuy'] as $key => $val) {
							if ($key == 0) continue;
							$tourist .= "<tr>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['genderChildBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['ageChildBuy'][$key]))."</td>";
							$tourist .= "<td>".htmlspecialchars(trim($_POST['heightChildBuy'][$key]))."</td>";
							$tourist .= "</tr>";
						}

						$tourist .= "</table>";
					}
				}

				$array['tourist'] = $tourist;

				$orderId = all::insert_block("tourorder", 37, $array, 1);


				// отправка уведомления агенту
					$email = $array['email'];
					$sitename = $control->settings->sitename;

					$mailpage->theme = "Заказ экскурсии";

					// Урл сайта
					$mailpage->siteUrl = "http://".$_SERVER['HTTP_HOST'];

					// Обращение
					$mailpage->name = $array['name'];
					// Номер заказа
					$mailpage->orderid = $orderId;

					$msg = sprintt($mailpage, 'mailtemplates/touser/agentexcorder.html');


					all::send_mail($email, $mailpage->theme, $msg, false, false, "$sitename robot");


				// отправка уведомления админу
					$email = $control->settings->email;
					$mailpage->theme = "Заказ экскурсии на сайте Olli Tours";
					$mailpage->excname = $array['tour'];
					$msg = sprintt($mailpage, 'mailtemplates/toadmin/excorder.html');

					all::send_mail($email, $mailpage->theme, $msg, false, false, "$sitename robot");


				die();

			}
		}

		$scriptBuy = $validator->getJsArray();
		return $scriptBuy;
	}

	private function printOne($bid) {
		global $control;
		$bid = $bid + 1 - 1;

		// Экскурсия
		$list = new Listing($control->module_wrap, 'blocks', "all", "id=$bid AND ");
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$page->item = $this->exc_formatOne($page->item);


		// Фотографии тура
		$list = new Listing("photoexc", "blocks", 28, "exc=$bid AND ");
		$list->getList();
		$list->getItem();
		$page->image = $list->item;

		foreach ($page->image as $key => $val) {
			$val->size = all::getRandom(1,2);
		}

		if (isset($_SESSION['uid'])) {
			$page->auth = true;
			$page->scriptBuy = $this->printBuyAuth();
		}
		else {
			$page->scriptBuy = $this->printBuy();
		}

		$page->excUrl = all::getUrl(21);

		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;

		// Фильтры
		$cri = "";

		// Нагрузка
		if ($_GET['charge'] != "") {
			$charge = trim($_GET['charge'], "/");
			$cri .= "charge='$charge' AND ";
		}

		// Активность
		if ($_GET['activity'] != "") {
			$activity = trim($_GET['activity'], "/");

			// Активности
			$activityArray = array("Велосипедный маршрут", "Кросс-маршрут", "Скандинавская ходьба", "Серфинг", "Лыжный маршрут");
			$cri .= "activity LIKE '%".$activityArray[$activity]."%' AND ";
		}

		// Стоимость
		if ($_GET['price'] != "") {
			$price = explode(";", trim($_GET['price'], "/"));
			$minprice = $price[0];
			$maxprice = $price[1];
			$cri .= "fullprice + 1 - 1 >= $minprice AND fullprice + 1 - 1 <= $maxprice AND ";
		}

		// Протяженность
		if ($_GET['length'] != "") {
			$length = explode(";", trim($_GET['length'], "/"));
			$minlength = $length[0];
			$maxlength = $length[1];
			$cri .= "length + 1 - 1 >= $minlength AND length + 1 - 1 <= $maxlength AND ";
		}

		// Продолжительность
		if ($_GET['duration'] != "") {
			$duration = explode(";", trim($_GET['duration'], "/"));
			$minduration = $duration[0];
			$maxduration = $duration[1];
			$cri .= "duration + 1 - 1 >= $minduration AND duration + 1 - 1 <= $maxduration AND ";
		}

		// Калории
		if ($_GET['calories'] != "") {
			$calories = explode(";", trim($_GET['calories'], "/"));
			$mincalories = $calories[0];
			$maxcalories = $calories[1];
			$cri .= "calories + 1 - 1 >= $mincalories AND calories + 1 - 1 <= $maxcalories AND ";
		}

		$list = new Listing($control->module_wrap, 'blocks', $cid, $cri);
		$list->limit = 6;
		$list->page = $control->page;
		$list->tmp_url = all::getUrl($control->module_parent);
		$list->getList();
		$list->getItem();
		$list->getPage();

		$page->item = $list->item;
		$page->page = $list->navigation;
		$page->itemSelector = ".item-wrapper";
		$page->containerSelector = "#exc-list";
		$page->url_last = $list->url_last;
		$page->url_p = $list->url_p;
		$page->url_n = $list->url_n;
		$page->url_next = $list->url_next;

		$page->item = $this->exc_formatList($page->item);

		if (isset($_SESSION['uid'])) {
			$page->auth = true;
		}


		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>