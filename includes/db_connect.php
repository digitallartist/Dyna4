<?php #Script db_connect.php
	ini_set('max_user_connections', 2000);
	ini_set('max_connections', 2000);
	define('DB_USER', 'yonetici');
	define('DB_PASS', 'Fa1GG0882GdD');


	define('DB_HOST', '178.211.58.78');
	define('DB_PORT', 33006);
	define('DB_NAME', 'dynaWorks');

	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die('Sistem hatası:' . mysqli_connect_error());
	@mysqli_query($dbc, "SET NAMES 'utf8'");
