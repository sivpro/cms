<?php
require_once(DOC_ROOT."/inc/helpers/helper_tours.php");
class tours extends helper_tours {

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

				$orderId = all::insert_block("tourorder", 35, $array, 1);


				// отправка уведомления пользователю (с генерацией уникальной ссылки и записью ее в таблицу)
					$email = $array['email'];
					$sitename = $control->settings->sitename;

					$mailpage->theme = "Заказ тура";


					// Урл сайта
					$mailpage->siteUrl = "http://".$_SERVER['HTTP_HOST'];

					// Уникальная ссылка
					$md5 = md5($array['name'].$array['email'].time());
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
							'tour'
							)");


					$msg = sprintt($mailpage, 'mailtemplates/touser/userorder.html');


					all::send_mail($email, $mailpage->theme, $msg, false, false, "$sitename robot");

				// отправка уведомления админу
					$email = $control->settings->email;
					$mailpage->theme = "Заказ тура на сайте Olli Tours";
					$mailpage->tourname = $array['tour'];
					$msg = sprintt($mailpage, 'mailtemplates/toadmin/order.html');

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

				$orderId = all::insert_block("tourorder", 35, $array, 1);


				// отправка уведомления агенту
					$email = $array['email'];
					$sitename = $control->settings->sitename;

					$mailpage->theme = "Заказ тура";

					// Урл сайта
					$mailpage->siteUrl = "http://".$_SERVER['HTTP_HOST'];

					// Обращение
					$mailpage->name = $array['name'];
					// Номер заказа
					$mailpage->orderid = $orderId;

					$msg = sprintt($mailpage, 'mailtemplates/touser/agentorder.html');


					all::send_mail($email, $mailpage->theme, $msg, false, false, "$sitename robot");


				// отправка уведомления админу
					$email = $control->settings->email;
					$mailpage->theme = "Заказ тура на сайте Olli Tours";
					$mailpage->tourname = $array['tour'];
					$msg = sprintt($mailpage, 'mailtemplates/toadmin/order.html');

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

		// Тур
		$list = new Listing($control->module_wrap, 'blocks', "all", "id=$bid AND ");
		$list->getList();
		$list->getItem();
		$page->item = $list->item;

		$page->item = $this->tours_formatOne($page->item);


		// Фотографии тура
		$list = new Listing("phototour", "blocks", 27, "tour=$bid AND ");
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

		$page->toursUrl = all::getUrl(20);

		$page->back = all::getUrl($control->module_parent).all::addUrl($this->page);
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'_one.html');
	}

	private function printList($cid) {
		global $control;

		// Фильтры
		$cri = "";

		// Город вылета
		if ($_GET['cityfrom'] != "") {
			$cityfrom = trim($_GET['cityfrom'], "/");
			$cri .= "cityfrom='$cityfrom' AND ";
		}

		// Город прилета
		if ($_GET['cityto'] != "") {
			$cityto = trim($_GET['cityto'], "/");
			$cri .= "cityto='$cityto' AND ";
		}

		// Ночей
		if ($_GET['nights'] != "") {
			$nights = explode(";", trim($_GET['nights'], "/"));
			$minnights = $nights[0];
			$maxnights = $nights[1];
			$cri .= "nights + 1 - 1 >= $minnights AND nights + 1 - 1 <= $maxnights AND ";
		}

		// Звезд не менее
		if ($_GET['stars'] != "") {
			$stars = trim($_GET['stars'], "/");
			$cri .= "stars + 1 - 1 >= $stars AND ";
		}

		// Тип номера
		if ($_GET['room'] != "") {
			$room = trim($_GET['room'], "/");
			$cri .= "room='$room' AND ";
		}

		// Стоимость
		if ($_GET['price'] != "") {
			$price = explode(";", trim($_GET['price'], "/"));
			$minprice = $price[0];
			$maxprice = $price[1];
			$cri .= "fullprice + 1 - 1 >= $minprice AND fullprice + 1 - 1 <= $maxprice AND ";
		}

		// Питание
		if (isset($_GET['food'])) {
			$getfood = $_GET['food'];
			foreach ($getfood as $val) {
				$val = trim($val, "/");
			}
			if (count($getfood) > 0) {
				$foodcri = "(";
			}
			foreach ($getfood as $val) {
				$foodcri .= "food = '$val' OR ";
			}
			if ($foodcri != "(") {
				$foodcri = trim($foodcri, " OR ").") AND ";
				$cri .= $foodcri;
			}
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
		$page->containerSelector = "#tours-list";
		$page->url_last = $list->url_last;
		$page->url_p = $list->url_p;
		$page->url_n = $list->url_n;
		$page->url_next = $list->url_next;

		$page->item = $this->tours_formatList($page->item);

		if (isset($_SESSION['uid'])) {
			$page->auth = true;
		}


		$page->name = $control->name;
		$page->pages_down = sprintt($page, 'templates/temps/pages_down.html');
		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}
}
?>