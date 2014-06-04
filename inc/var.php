<?php
error_reporting(0);

	//Род падеж месяцев
	$ar_mon[1] = 'января';
	$ar_mon[2] = 'февраля';
	$ar_mon[3] = 'марта';
	$ar_mon[4] = 'апреля';
	$ar_mon[5] = 'мая';
	$ar_mon[6] = 'июня';
	$ar_mon[7] = 'июля';
	$ar_mon[8] = 'августа';
	$ar_mon[9] = 'сентября';
	$ar_mon[10] = 'октября';
	$ar_mon[11] = 'ноября';
	$ar_mon[12] = 'декабря';

	//Количество дней в месяцах
	$ar_mon_count[1] = 31;
	$ar_mon_count[2] = 28;
	$ar_mon_count[3] = 31;
	$ar_mon_count[4] = 30;
	$ar_mon_count[5] = 31;
	$ar_mon_count[6] = 30;
	$ar_mon_count[7] = 31;
	$ar_mon_count[8] = 31;
	$ar_mon_count[9] = 30;
	$ar_mon_count[10] = 31;
	$ar_mon_count[11] = 30;
	$ar_mon_count[12] = 31;
	$substep=5;

	//Префикс базы данных
	$prname = "it";
	$config = array();

	//hosting settings
	$config['dbhost'] = 'localhost';
	$config['dbname'] = 'u24286';
	$config['dbuser'] = 'root';
	$config['dbpass'] = 'zenuz8madob';

	//local settings
	$config['dbhost'] = 'localhost';
	$config['dbname'] = 'newadmin';
	$config['dbuser'] = 'root';
	$config['dbpass'] = '';

	// Раскомментировать и скорректировать при наличии языковых версий
	// if ($_SESSION['lang'] != 'cz') {
	// 	$config['dbname'] = 'u32748'.'_'.$_SESSION['lang'];
	// }

	$config['site_name'] = '';


	$config['md5'] = 'siteactiv';
	$config['server_url'] = 'http://'.$_SERVER['SERVER_NAME'].'/';

	$texts['sql_connection_error'] = 'Невозможно подключиться к серверу баз данных.';
	$texts['sql_db_selection_error'] = 'Невозможно выбрать базу данных.';




	$docroot = $_SERVER['DOCUMENT_ROOT'];
	if (substr($docroot, -1) == "/") {
		$docroot = substr($docroot, 0, strlen($docroot)-1);
	}
	define("DOC_ROOT", $docroot);
?>