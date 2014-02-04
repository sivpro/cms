<?php
ob_start();
ini_set("display_errors", "1");
mb_internal_encoding("UTF-8");

session_start();

// Раскомментировать и скоректровать при наличии языковых версий
// if (strpos($_SERVER['REQUEST_URI'], '/manage') > -1) {
// 	if ($_SESSION['lang'] == "") {
// 		$_SESSION['lang'] = "cz";
// 	}
// }

// else {
// 	$lang = explode(".", $_SERVER['SERVER_NAME']);

// 	if ($lang[0] == "www") {
// 		array_shift($lang);
// 	}

// 	if (count($lang) == 2) {
// 		$_SESSION['lang'] = "cz";
// 	}
// 	else {
// 		if ($lang[0] == "en") {
// 			$_SESSION['lang'] = "en";
// 		}
// 		elseif ($lang[0] == "ru") {
// 			$_SESSION['lang'] = "ru";
// 		}
// 		elseif ($lang[0] == "de") {
// 			$_SESSION['lang'] = "de";
// 		}
// 	}

// }


$user_agent = $_SERVER['HTTP_USER_AGENT'];
if (stripos($user_agent, 'MSIE 6.0') !== false || stripos($user_agent, 'MSIE 7.0') !== false) {
	header ("Location: /ie6/ie6.html");
}


$url = $_SERVER['REQUEST_URI'];

$lastS = substr($url, -1, 1);
if ($lastS != "/") {
	$url .= "/";
	header("Location: $url", true, 301);
}


include "includes.php";

	$sql = new Sql();
	$sql->connect();
	$control = new Controller($url);

	if (defined('ENVIRONMENT') && ENVIRONMENT == 'development') {

	}

	if (isset($control->error)) {
		$control = new Controller("/".$control->error);
	}
	$control->make();
	$sql->close();

ob_get_contents();

?>