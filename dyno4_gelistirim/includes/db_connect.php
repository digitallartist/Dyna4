<?php

	define('DB_USER', 'boto_erp');
	define('DB_PASS', 'rXC8qlB6xHG7EO2v');

	//if ($_SERVER['HTTP_HOST']=='boto')
	//	define('DB_HOST', '85.159.70.106');
	//else
	define('DB_HOST', 'localhost');	
	define('DB_NAME', 'musterdi_koin');

	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME) or die('Sistem hatası:' . mysqli_connect_error());
	mysqli_query($dbc, "SET NAMES 'utf8'");
	//mysqli_query($dbc, "SET time_zone 'Europe/Istanbul'");
	mysqli_query($dbc, "SET SESSION time_zone = '+03:00'");
	//include('lang.php');
?>
