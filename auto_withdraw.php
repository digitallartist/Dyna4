<?php
/*
$date = new DateTime();
$timeZone = $date->getTimezone();
echo $timeZone->getName();
echo "<br/>";

echo date("Y-m-d H:i:s");
*/
include("includes/db_connect.php");
require_once("btcturk-pro/btcturkpro.class.php");
putenv("TZ=Europe/Istanbul");

include("includes/functions.php");
require_once("includes/s_params.php");
require_once('vendor/autoload.php');

function acilma_suresi()
{
    $time = explode(" ", microtime());
    $usec = (double)$time[0];
    $sec = (double)$time[1];
    return $sec + $usec;
}
$saymaya_basla = acilma_suresi();



$market_data=array();

//1. MARKET = KRAKEN
// 1 MARKET PİYASA XXBTZUSD yani BTCEUR

$market_data[0]['market']="Kraken";
$market_data[0]['coin1']="XXBT";
$market_data[0]['coin2']="ZUSD";
$market_data[0]['pairs']="XXBTZUSD";
$market_data[0]['pairs2']="XXBT_ZUSD";
$market_data[0]['minumum_order']=0.002;
$market_data[0]['user']="mustafa";
$market_data[0]['api_id']=0;


//2. MARKET = BTCTURK
// 2. MARKET PİYASA BTCTRY
$market_data[1]['market']="BtcTurk";
$market_data[1]['coin1']="BTC";
$market_data[1]['coin2']="TRY";
$market_data[1]['pairs']="BTCTRY";
$market_data[1]['pairs2']="BTC_TRY";
$market_data[1]['minumum_order']=0.002;
$market_data[1]['user']="mustafa";
$market_data[1]['api_id']=3;



list($kraken_key,$kraken_secret)=get_market_api_secret_keys($market_data[0]['market'],$market_data[0]['user'],$market_data[0]['api_id']);
try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
} catch (Exception $e) {
    echo $e->getMessage();
}

list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys($market_data[1]['market'],$market_data[1]['user'],$market_data[1]['api_id']);
try {

$btcturk_connect =  new BtcTurkPRO ($btcturk_key, $btcturk_secret);
} catch (Exception $e) {
   echo $e->getMessage();

}


$all_wallet_work=0;

$wallet_get=0;
try {
  $kraken_balances = $kraken_connect->getAccountBalance();
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
  $json_return_params['explanation']="Account Balance Connection Problem M1";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;
}

if($wallet_get==1 && $kraken_balances!=NULL ){
      $all_wallet_work++;
  parse_balances_from_market_data($market_data[0]['market'],$kraken_balances);
} else {
  $q ="UPDATE markets
  SET try_num=try_num+1, last_try_time='" . date("Y-m-d H:i:s") . "' 
  WHERE id='1'";
  //  echo $q;
  $r = mysqli_query ($dbc, $q); // Run the query.

}


$wallet_get=0;



  try {
  $btcturk_balances=$btcturk_connect->getBalances();
    $wallet_get++;
  } catch (Exception $e) {
    
    print_array($e);
    $saymayi_bitir = acilma_suresi();
    $basla = $saymayi_bitir - $saymaya_basla;
    $json_return_params["status"]="Temporary Problem";
    $json_return_params["result"]="false";
    //$wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
    //$wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
    $gain_rates['straight']['normal']="";
    $gain_rates['reverse']['normal'] ="";
    $json_return_params['act_rate']="";
    $json_return_params['explanation']="Account Balance Connection Problem M2";
    $json_return_params['color']="#FF8000";
    $json_return_params['slack_status_api']="CYCLE_WARNINGS";
    $json_return_params['slack_status']=0;
      print_json_results($basla);
    }
    
    
      
    if($wallet_get==1 && $btcturk_balances!=NULL ){
      $all_wallet_work++;
  parse_balances_from_market_data($market_data[1]['market'],$btcturk_balances);
  }
  else {

    $q ="UPDATE markets
    SET try_num=try_num+1, last_try_time='" . date("Y-m-d H:i:s") . "' 
    WHERE id='2'";
    //  echo $q;
    $r = mysqli_query ($dbc, $q); // Run the query.
  }

      
        if($all_wallet_work>0)
          update_market_balances_on_db();

        get_start_withdraw_value_from_db();






		 $now=date('Y-m-d H:i:s');
		 $q="SELECT NOW() suan";
		$r = @mysqli_query ($dbc, $q); // Run the query.
		if($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
			$now=$row['suan'];
		}

 $current_date = strtotime($now);
$control_time = date('Y-m-d H:i:s', strtotime('-1 minute', $current_date));

//check_status_controls('Kraken', 'DASH','DASHUSD',$control_time);
$withdraw_on=1;
if($withdraw_on==1){
/*
check_status_controls('Kraken', 'XXBT','XXBTZUSD',$control_time);
check_status_controls('Kraken', 'XETH','XETHZUSD',$control_time);
check_status_controls('Kraken', 'XLTC','XLTCZUSD',$control_time);
check_status_controls('Kraken', 'XXRP','XXRPZUSD',$control_time);
check_status_controls('Kraken', 'XXLM','XXLMZUSD',$control_time);
check_status_controls('Kraken', 'DASH','DASHUSD',$control_time);
*/
check_status_controls('Kraken', 'DOT','DOTUSD',$control_time);
check_status_controls('Kraken', 'ADA','ADAUSD',$control_time);
check_status_controls('Kraken', 'XXBT','XXBTZUSD',$control_time);
}



get_api_quotas('1Forge');

$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
echo '{"time" : "'.date('Y-m-d H:i:s').'", "duration": "'.$basla.'"}';



//make_auto_withdraw();

//print_r($wallet_params);
/*
try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
    $orderBookResponse = $kraken_connect->getOrderBook("XXBTZEUR", 1);


} catch (Exception $e) {
    echo $e->getMessage();
}

list($kraken_bid_price,$kraken_bid_volume,$kraken_bid_timestamp,$kraken_ask_price,$kraken_ask_volume,$kraken_ask_timestamp, $kraken_price_status)=parse_prices_and_volume_from_market_data('Kraken',$orderBookResponse,'BTC','EUR');


echo "bu <br/>";
echo "BTCTurk<br/>";
echo "Bid Price: " .$btcturk_bid_price ."<br/>";
echo "Bid Volume: " .$btcturk_bid_volume ."<br/>";
echo "Ask Price: " .$btcturk_ask_price ."<br/>";
echo "Ask Volume: " .$btcturk_ask_volume ."<br/>";
echo "KRAKEN<br/>";
echo "Bid Price: " . $kraken_bid_price ."<br/>";
echo "Bid Volume: " . $kraken_bid_volume ."<br/>";
echo "Ask Price: " .$kraken_ask_price ."<br/>";
echo "Ask Volume: " .$kraken_ask_volume ."<br/>";
*/
/*
var_dump($btcturk_balances);

var_dump($kraken_balances);
*/

//echo json_encode($btcturk_balances);
/*
foreach($kraken_balances as $balance){
  echo "<br/>

  </br/>";
  echo $balance->currency;
}*/

//echo "\n\n";
