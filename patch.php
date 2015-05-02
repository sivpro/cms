<?php
	// error_reporting(E_ALL);
	// This file adds the new functionality: show last modified column for all blocks

	include "includes.php";

	$connection = new Sql();
	$connection->connect();


	$query = sql::query("SHOW TABLES LIKE 'it\_b\_%'");

	while($result = sql::fetch_assoc($query)) {
		$far = array_values($result);

		$tableQuery = sql::query("DESCRIBE `$far[0]`");

		while ($tableResult = sql::fetch_assoc($tableQuery)) {
			if ($tableResult['Field'] == "modified") {
				continue 2;
			}
		}

		sql::query("ALTER TABLE $far[0] ADD `modified` timestamp");
	}

	// Добавляем пользователям админки поле timezone
	$tableQuery = sql::query("DESCRIBE it_sadmin");

	$issetTimezoneField = false;
	while ($tableResult = sql::fetch_assoc($tableQuery)) {
		if ($tableResult['Field'] == "timezone") {
			$issetTimezoneField = true;
			break;
		}
	}

	if (!$issetTimezoneField) {
		sql::query("ALTER TABLE prname_sadmin ADD timezone varchar(255)");
		sql::query("UPDATE prname_sadmin SET timezone='Asia/Yekaterinburg'");
	}


?>