<?php

	ini_set('max_user_connections', 2000);
	ini_set('max_connections', 2000);
	define('DB_USER', 'root');
	define('DB_PASS', '');

	define('DB_HOST', 'localhost');
	define('DB_NAME', 'dyno_cycle');

	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Sistem hatası:' . mysqli_connect_error());
	mysqli_query($dbc, "SET NAMES 'utf8'");

?>
