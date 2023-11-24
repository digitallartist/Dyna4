
<?php
/*
error_reporting(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
@ini_set('display_errors', 0);
*/


include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");
//include ("includes/main_lib.php");
include ("includes/functions.php");

function acilma_suresi()
{
    $time = explode(" ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}
$saymaya_basla = acilma_suresi();



get_api_quotas('1Forge');

$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
echo '{"time" : "'.date('Y-m-d H:i:s').'", "duration": "'.$basla.'"}';


?>
