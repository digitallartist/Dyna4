<?php #Script db_connect.php
	ini_set('max_user_connections', 2000);
	ini_set('max_connections', 2000);
	define('DB_USER', 'sansmetre_db');
	define('DB_PASS', '!1_Iei0YSk]EfS5*');


	define('DB_HOST', 'localhost');
	define('DB_PORT', 3306);
	define('DB_NAME', 'sansmetre_db');

	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT) or die('Sistem hatası:' . mysqli_connect_error());
	@mysqli_query($dbc, "SET NAMES 'utf8'");
