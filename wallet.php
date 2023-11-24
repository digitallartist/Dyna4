<?php
echo "baştannn   ";
//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include("includes/db_connect.php");
require_once("btcturk-master/BtcTurkAPIClient.php");
require_once("btcturk-pro/btcturkpro.class.php");
require_once("coin_src/KrakenAPIClient.php");
require_once("includes/functions.php");
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


//******************************************************//
//    AŞAMA  0
// DEĞİŞKENLER, PARAMETRELER, MARKET ve COIN ataması


// secret parameters
$json_return_params=array();
$json_return_params["status"]="";
$json_return_params["result"]="";
$json_return_params["tx_id1"]="";
$json_return_params["tx_id2"]="";
$json_return_params["act_volume"]="";

$secret_params=array();
$secret_params['Kraken']['mustafa'][0]['key']='e/1M0NLHyhBBSjDqpRMIYdTSNDUGHxpYl6dJNDMWYpPJtjP0ILCo1RRi';//api-key-1617277241790 wallet, order, withdraw 
$secret_params['Kraken']['mustafa'][0]['secret']='I2XglMnHtm1wcyVO6uZxNcPjn+yUkcoVrJUiuwWcNPf+CKER8svPCMjtj/zDcUsebk44rmvcq1U1+8kGF7TC4Q=='; 
$secret_params['Kraken']['deniz'][0]['key']='Qd56NSdl18RmiYpP4ULlE6PeV3JMoWLN0RK5uQHJgMGDEv91BwZ7jCdD';//sadece wallet, herşey
$secret_params['Kraken']['deniz'][0]['secret']='o7h93vf1sQnWBqIBCqVTMe50X36erYMO/T/G6r2LO9DQgJ+iP8aReqWH7Rntq7uK/t/dw0EP2wJR4NejwV+iRQ=='; //sadece wallet


$secret_params['BtcTurk']['mustafa'][0]['key']='a6388f08-9e10-4917-ad61-a40743eeb97a'; //sadece wallet
$secret_params['BtcTurk']['mustafa'][0]['secret']='22QreSdVLfVEny3zCw/qwxUO18RfuksZ';//sadece wallet
$secret_params['BtcTurk']['mustafa'][1]['key']='63f9bea0-dad2-46b1-bc47-1f99a46b1510'; //açık emirler
$secret_params['BtcTurk']['mustafa'][1]['secret']='9yZ/yTMq5AJzp5B4Ndl9ZXsv4U3eR3nC';//açık emirler
$secret_params['BtcTurk']['mustafa'][2]['key']='247bd944-8606-445f-966e-abfb39c431dd'; //sipraiş
$secret_params['BtcTurk']['mustafa'][2]['secret']='BfWfxqzWxhTt+oOsquxmHwf3ETIOHS0O';//sipariş
$secret_params['BtcTurk']['mustafa'][3]['key']='cd90241e-6e55-4753-a615-94d8d0236f10'; //herşey
$secret_params['BtcTurk']['mustafa'][3]['secret']='3kpPKkmLIY0ep0+vvbsQjC2AXHtqthsa';//herşey



$min_start_rates=array();
$min_start_rates['straight']['normal']=1.2;
$min_start_rates['straight']['first_sell']=1.2;
$min_start_rates['straight']['first_sell_diff']=0;
$min_start_rates['reverse']['normal']=0.5;
$min_start_rates['reverse']['first_buy']=0.5;;
$min_start_rates['reverse']['first_buy_diff']=0;

$wallet_params=array();
$gain_rates=array();
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

//******************************************************//
// AŞAMA 0.1
//güncel EUR ve USD kurunu database üzerinden al ****DataBase eğer zaman aşımı varsa programdan çık

$cur_rates=array();
get_cur_date_from_db();
//print_r($cur_rates);
$now=date('Y-m-d H:i:s');

$control_time=date('Y-m-d H:i:s',strtotime('+10 minute',strtotime($cur_rates['CURTIME'])));
/*if($control_time<$now){

//  send_slack_message('Kur zaman aşımı', 'info', 'info' );
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;

  $json_return_params['status']="NOT WORKING";
  $json_return_params['result']="false";
  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="CUR RATE TIMEOUT YOU MUST RUN IT!";
  $json_return_params['color']="#FF0000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=1;

  print_json_results($basla);

}*/


//******************************************************//
// AŞAMA 1
// BAĞLANTILARI OLUŞTUR ve GLOBAL olarak kullan



//1 MARKET
//KRAKEN BAĞLANTISI OLUŞTUR
list($kraken_key,$kraken_secret)=get_market_api_secret_keys($market_data[0]['market'],$market_data[0]['user'],$market_data[0]['api_id']);
try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
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
//  $btcturk_connect = new Client ($btcturk_key, $btcturk_secret);
$btcturk_connect =  new BtcTurkPRO ($btcturk_key, $btcturk_secret);
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






//******************************************************//
// AŞAMA 2
// DB ÜZERİNDE İŞLEM BEKLEMESİ OLUP OLMADIĞINI ÖĞREN, İŞLEM VARSA
// IF
// AŞAMA 2.1 AÇIK SİPARİŞLERİ KONTROL EDEN FONKSİYONU ÇALIŞTIR. BU FONKSİYON AÇIK SİPARİŞLERİ VE İLİŞKİLİ SİPARİŞLERİ KONTROL EDER. BİTENLERİ DB ÜZERİNDE KAPATIR.BİTMEYENLERİ GĞNCELLER
// AŞAMA 2.2 CÜZDANLARI ÇEKEN VE DB DE GÜNCELLEYEN FONKSİYONU ÇALIŞTIR.
/*
AŞAMA 2.2
*/

//CÜZDANLARI MARKET ÜZERİNDEN ÇEK VE AYARLA ****Market

$wallet_get=0;
try {
  $kraken_balances = $kraken_connect->getAccountBalance();

  echo "<br/>

  </br/>Kraken balance <br/>

  </br/>";
  var_dump($kraken_balances);
  $wallet_get++;
} catch (Exception $e) {
	echo "<br/>
KRAK error
</br/>";

var_dump($e);
	echo "<br/>

</br/>";
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
    print_json_results($basla);
}
if($wallet_get==1)
  parse_balances_from_market_data($market_data[0]['market'],$kraken_balances);

try {
$btcturk_balances=$btcturk_connect->getBalances();

echo "<br/>

</br/>BtcTurk balance <br/>

</br/>";
var_dump($btcturk_balances);

  $wallet_get++;
} catch (Exception $e) {
	echo "<br/>
BTC error
</br/>";

var_dump($e);
	echo "<br/>

</br/>";
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
if($wallet_get==2)
    parse_balances_from_market_data($market_data[1]['market'],$btcturk_balances);





/*
*/
// AŞAMA 2.3
// ELSE
//EĞER AÇIK SİPARİŞ YOK İSE CÜZDAN BİLGİLERİNİ DB'dEN ÇEK
//AÇIK SİPARİŞLERİ ÇEK VE MARKET BALANCE değerlerini güncelle
/*
$open_orders=array();

$open_order_get=0;
try {
  $orderBookResponse = $kraken_connect ->getOpenOrders();

  $open_order_get++;

} catch (Exception $e) {
    //echo $e->getMessage();
}

if($open_order_get==1)
  parse_open_orders_from_market_data($market_data[0]['market'],$orderBookResponse);
*/
/*
$open_order_get=0;

try {
//  $btcturk_open_orders=$btcturk_connect->getOpenOrders('XLMTRY');
  $btcturk_open_orders=get_any_open_orders_from_btcturk($btcturk_connect);
  $open_order_get++;
} catch (Exception $e) {
  //  echo $e->getMessage();


}

if($open_order_get==1)
  parse_open_orders_from_market_data($market_data[1]['market'],$btcturk_open_orders);
*/





$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
//print_json_results($basla);







?>
