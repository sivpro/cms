<?php
class payment {

	public function __construct() {
		global $control;
		$this->page = $control->page;
		if (isset($_GET['order'])) {
			$this->printOrder();
		}
		return;
	}


	private function printOrder() {
		global $control;
		$orderid = trim($_GET['order'], "/");
		$orderid = strip_tags($orderid);
		$orderid = addslashes($orderid);
		$orderid = substr($orderid, 0, 32);
		$orderFields = sql::fetch_object(sql::query("SELECT * FROM prname_uniqlinks WHERE link='$orderid'"));

		if (!$orderFields) return;



		$order = all::b_data_all($orderFields->orderid, "tourorder");
		if (!$order) return;
		$page = $order;
		$page->type = $orderFields->type;

		// Скрипт робокассы
		$page->robokassa = $this->getRobokassa($page->price, $page->tour, $page->type, $orderid);


		$this->html['text'] = sprintt($page, 'templates/'.$control->template.'/'.$control->template.'.html');
	}

	private function getRobokassa($summ, $tourname, $tourtype, $orderid) {
		global $control;

		if (!$summ) return;
		if ($orderid < 0) return;

		// Описание платежа
		if ($tourtype == "tour") {
			$desc = 'Оплата тура "'.$tourname.'"';
		}
		else {
			$desc = 'Оплата экскурсии "'.$tourname.'"';
		}

		$page->mrh_login = $mrh_login = "loki123";
		$page->mrh_pass1 = $mrh_pass1 = "1vjklfrysq";

		// номер заказа
		// number of order
		$inv_id = $orderid;

		// описание заказа
		// order description
		$inv_desc = $desc;

		// сумма заказа
		// sum of order
		$out_summ = $price;


		// предлагаемая валюта платежа
		// default payment e-currency
		$in_curr = "";


		// язык
		// language
		$culture = "ru";

		// кодировка
		// encoding
		$encoding = "utf-8";

		// формирование подписи
		// generate signature
		$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1");

		// HTML-страница с кассой
		// ROBOKASSA HTML-page
		$paidScript = "<script language=JavaScript ".
					"src='https://auth.robokassa.ru/Merchant/PaymentForm/FormMS.js?".
					"MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$inv_id&IncCurrLabel=$in_curr".
					"&Desc=$inv_desc&SignatureValue=$crc".
					"&Culture=$culture&Encoding=$encoding'></script>";

		// $page->paidScript = "<script language=JavaScript ".
		// 			"src='http://test.robokassa.ru/Index.aspx?".
		// 			"MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$inv_id&IncCurrLabel=$in_curr".
		// 			"&Desc=$inv_desc&SignatureValue=$crc".
		// 			"&Culture=$culture&Encoding=$encoding'></script>";


		return $paidScript;
	}
}
?>