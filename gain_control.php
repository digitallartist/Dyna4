<?php

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include("includes/db_connect.php");
require_once("btcturk-master/BtcTurkAPIClient.php");
require_once("btcturk-pro/btcturkpro.class.php");
require_once("coin_src/KrakenAPIClient.php");
require_once("includes/functions2.php");
require_once("includes/s_params.php");
require_once("includes/send_slack_message.php");
require_once('vendor/autoload.php');
putenv("TZ=Europe/Istanbul");
date_default_timezone_set('Europe/Istanbul');
function acilma_suresi()
{
    $time = explode(" ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}
$saymaya_basla = acilma_suresi();
  $term=1;
  $check_term=1;

  $qq="SELECT fld_active FROM gain_control ORDER BY fld_active DESC LIMIT 1 ";
  $rq = @mysqli_query($dbc, $qq);

  if($row = mysqli_fetch_array ($rq, MYSQLI_ASSOC)) {
    $check_term=$row['fld_active'];
  }


if(isset($_GET['term'])) {

  $term=$_GET['term'];
}

else {

  $term=$check_term;

}

//******************************************************//
//    AŞAMA  0
// DEĞİŞKENLER, PARAMETRELER, MARKET ve COIN ataması






$market_data[0]['market']="Kraken";
$market_data[0]['coin1']="XXLM";
$market_data[0]['coin2']="ZEUR";
$market_data[0]['pairs']="XXLMZEUR";
$market_data[0]['minumum_order']=30;
$market_data[0]['user']="mustafa";
$market_data[0]['api_id']=0;

//2. MARKET = BTCTURK
// 2. MARKET PİYASA BTCTRY
$market_data[1]['market']="BtcTurk";
$market_data[1]['coin1']="XLM";
$market_data[1]['coin2']="TRY";
$market_data[1]['pairs']="XLMTRY";
$market_data[1]['minumum_order']=30;
$market_data[1]['user']="mustafa";
$market_data[1]['api_id']=3;

$wallet_params=array();

$cur_rates=array();
get_cur_date_from_db();
//print_r($cur_rates);
$now=date('Y-m-d H:i:s');

$control_time=date('Y-m-d H:i:s',strtotime('+1 minute',strtotime($cur_rates['CURTIME'])));



//******************************************************//
// AŞAMA 1
// BAĞLANTILARI OLUŞTUR ve GLOBAL olarak kullan



//1 MARKET
//KRAKEN BAĞLANTISI OLUŞTUR
list($kraken_key,$kraken_secret)=get_market_api_secret_keys($market_data[0]['market'],$market_data[0]['user'],$market_data[0]['api_id']);
try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
  //    echo "KRAcon";
} catch (Exception $e) {
  //  echo $e->getMessage();


  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
//  send_slack_message($e->getMessage(), 'info', 'info' );
  $json_return_params["status"]="Temporary Problem";
  $json_return_params["result"]="false";
  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']=$e->getMessage();
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;

  print_json_results($basla);

  exit();
}

//2.  MARKET
//BTCturk bağlantı oluştur
list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys($market_data[1]['market'],$market_data[1]['user'],$market_data[1]['api_id']);
try {
$btcturk_connect =  new BtcTurkPRO ($btcturk_key, $btcturk_secret);
//  echo "BTCcon";
} catch (Exception $e) {
  //  echo $e->getMessage();

  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
//  send_slack_message("BTCTURK CONNECT API PROBLEM", 'info', 'info' );
  $json_return_params["status"]="Temporary Problem";
  $json_return_params["result"]="false";
  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="M2 CONNECT API PROBLEM";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;

  print_json_results($basla);

}






$wallet_get=0;
try {
  $kraken_balances = $kraken_connect->getAccountBalance();
  

  $wallet_get++;
//  echo "Krawal";
} catch (Exception $e) {
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
  $json_return_params["status"]="Temporary Problem";
  $json_return_params["result"]="false";
  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="Account Balance Connection Problem M1";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;
  echo $e->getMessage();
  print_json_results($basla);
    //$wallet_get++;
}

if($wallet_get==1)
parse_balances_from_market_data($market_data[0]['market'],$kraken_balances);



try {
$btcturk_balances=$btcturk_connect->getBalances();


  $wallet_get++;
} catch (Exception $e) {
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
  $json_return_params["status"]="Temporary Problem";
  $json_return_params["result"]="false";
  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="Account Balance Connection Problem M2";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;
    print_json_results($basla);
}
if($wallet_get==2) {
parse_balances_from_market_data($market_data[1]['market'],$btcturk_balances);


update_market_balances_on_db();



try {
  $query=$btcturk_connect->getAllTickers();
  
  //var_dump( $query);

  foreach ($query as $key => $value) {

          $btcturk_prices[$value->pair]=$value->last;

  }
} catch (Exception $e) {
  //  echo $e->getMessage();
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
  $json_return_params["status"]= "Temporary Problem";
  $json_return_params["result"]="false";
  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="M2 Prices Get Problem";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;

  print_json_results($basla);

}
//print_r($btcturk_prices);

$coin_totals=array();

update_gain_control();




//print_r($wallet_params);
}


$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
//print_json_results($basla);

?>

<style type="text/css">
    .Table
    {
        display: table;
    }
    .Title
    {
        display: table-caption;
        text-align: center;
        font-weight: bold;
        font-size: larger;
    }
    .Heading
    {
        display: table-row;
        font-weight: bold;
        text-align: center;
    }
    .Row
    {
        display: table-row;
    }
    .Cell
    {
        display: table-cell;
        border: 1px solid black;
        padding-left: 5px;
        padding-right: 5px;
        padding: 10px;
        min-width: 40px;
        text-align:right;
    }
    .flo{

        background-color: #FFF8DC;
    }

    .gho{

        background-color: #F8F8FF;
    }
</style>
