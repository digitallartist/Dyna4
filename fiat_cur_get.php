
<?php
/*
error_reporting(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
@ini_set('display_errors', 0);
*/

include ("includes/main_lib.php");
include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");

function acilma_suresi()
{
    $time = explode(" ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}
$saymaya_basla = acilma_suresi();
function get_api_data($publisher=''){
  global $dbc;
  $q_ek="";
  if($publisher!="")
    $q_ek="fera.publisher='" .$publisher . "' AND ";

  $q="SELECT fera.publisher, fera.account_key, fep.get_rates_url  
  FROM fiat_exchange_rate_apis fera
  LEFT JOIN fiat_exchange_publisher fep ON fep.publisher LIKE fera.publisher
  WHERE " . $q_ek . " fera.fld_active='1' AND fera.quota_remaining>0  ORDER BY fep.update_freq_by_seconds ASC, fera.quota_remaining DESC LIMIT 1";
  $r = @mysqli_query ($dbc, $q); // Run the query.
  
  //echo $q;

  if($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)) {
    get_cur_data($row['publisher'], $row['account_key'], $row['get_rates_url']);
  }
}

function get_cur_data($publisher, $account_key, $get_rates_url){
  
  //echo "here";
      global $dbc;

      $url=$get_rates_url . $account_key;
      $inputJSON = file_get_contents($url);
      $inputs= json_decode( $inputJSON, TRUE ); //convert JSON into array
	     print_r($inputs);
  //echo "ebnd";
      if($publisher=='1Forge') {
        $EURUSD=0;
        $EURTRY=0;
        $USDTRY=0;
        $a_timestamp='';
        for($i=0;$i<count($inputs);$i++) {
           $symbol= str_replace("/","",$inputs[$i]['s']);
           $$symbol=$inputs[$i]['a'];
           $a_timestamp=substr($inputs[$i]['t'],0,10);
		  // echo "<br/>".date('Y-m-d H:i:s', $a_timestamp);
        }

        if($EURUSD>0) {
          $cur_time=date('Y-m-d H:i:s', @$atimestamp);
          $q="INSERT INTO `cur_rates`
          (api_source, action_date, atimestamp, eur_tl, usd_tl)
          VALUES ('".$publisher."','"  . date("Y-m-d H:i:s") ."', '" . date('Y-m-d H:i:s', $a_timestamp) . "', '" . $EURTRY . "', '" . $USDTRY . "');";
          $r = @mysqli_query ($dbc, $q); // Run the query.
//echo $q;
          $q="UPDATE fiat_exchange_rate_apis SET quota_used=quota_used+1, quota_remaining=quota_remaining-1 WHERE account_key='" .$account_key . "'";
          $r = @mysqli_query ($dbc, $q); // Run the query.
          ///echo $q;
        }

      }

}

get_api_data();

$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
echo '{"time" : "'.date('Y-m-d H:i:s').'", "duration": "'.$basla.'"}';


?>
