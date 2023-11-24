<?php

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
include("includes/db_connect.php");
require_once("btcturk-master/BtcTurkAPIClient.php");
require_once("btcturk-pro/btcturkpro.class.php");
require_once("coin_src/KrakenAPIClient.php");
require_once("includes/functions_v4.php");
require_once("includes/send_slack_message_v2.php");
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

//require_once('includes/secret_params.php');
$secret_params=array();
$secret_params['Kraken']['mustafa'][0]['key']='oi2mpcWuPGUSirj5AdptghaYclwqy3SyKIdtI62j/8UujbkNPJKVaVZI';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][0]['secret']='xYVsE3Nw53u1WpB+VWppKXPRDF5WUPDNcRJIjIGsFfEWMfm0XIMPcWIDdtWaC9Y9+lIcTLoXE5j/z9QkaJaJ3w=='; //sadece wallet
$secret_params['Kraken']['mustafa'][1]['key']='iae++6LG3bWoLuBUME4p8s0dK+8s9+SZYcgU9lygKZ9GrU7hLAUY3ErP';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][1]['secret']='DBod09NBjEM6qcvynNk/kd9EuF2ONV0lNuTbqx9CwqkfEdRD0BtZg31ELcA+++q2MNaZs/ineCG1V/xCtH6EgQ=='; //sadece wallet
$secret_params['BtcTurk']['mustafa'][0]['key']='a6388f08-9e10-4917-ad61-a40743eeb97a'; //sadece wallet
$secret_params['BtcTurk']['mustafa'][0]['secret']='22QreSdVLfVEny3zCw/qwxUO18RfuksZ';//sadece wallet
$secret_params['BtcTurk']['mustafa'][1]['key']='63f9bea0-dad2-46b1-bc47-1f99a46b1510'; //açık emirler
$secret_params['BtcTurk']['mustafa'][1]['secret']='9yZ/yTMq5AJzp5B4Ndl9ZXsv4U3eR3nC';//açık emirler
$secret_params['BtcTurk']['mustafa'][2]['key']='cf561189-5562-41dc-a4c5-22a9e34c42f9'; //sipraiş
$secret_params['BtcTurk']['mustafa'][2]['secret']='K1o5xCr4M1T3r5XlPWtUdI6SI7FIMzWh';//sipariş
$secret_params['BtcTurk']['mustafa'][3]['key']='ab7bdb15-fab0-49f8-bfd2-fec633a91065'; //herşey
$secret_params['BtcTurk']['mustafa'][3]['secret']='zAmXHMO5MO/5X0dGZpn9JWVk8SLNGCgZ';//herşey



//require_once('includes/rate_params.php');
$min_start_rates=array();
$min_start_rates['straight']['normal']=1.2;
$min_start_rates['straight']['first_sell']=1.2;
$min_start_rates['straight']['first_sell_diff']=0;
$min_start_rates['reverse']['normal']=0.5;
$min_start_rates['reverse']['first_buy']=0.5;;
$min_start_rates['reverse']['first_buy_diff']=0;

$starting_rates=array();
$starting_rates['Kraken']['ZUSD']=54;
$starting_rates['Kraken']['ZEUR']=0;
$starting_rates['Kraken']['XXBT']=1;
$starting_rates['Kraken']['XETH']=1;
$starting_rates['Kraken']['XLTC']=1;
$starting_rates['Kraken']['XXRP']=1;
$starting_rates['Kraken']['XXLM']=1;
$starting_rates['Kraken']['EOS']=1;
$starting_rates['Kraken']['DASH']=1;
$starting_rates['Kraken']['All']=61;

$starting_rates['BtcTurk']['TRY']=18;
$starting_rates['BtcTurk']['BTC']=3;
$starting_rates['BtcTurk']['ETH']=3;
$starting_rates['BtcTurk']['LTC']=3;
$starting_rates['BtcTurk']['XRP']=3;
$starting_rates['BtcTurk']['XLM']=3;
$starting_rates['BtcTurk']['EOS']=3;
$starting_rates['BtcTurk']['DASH']=3;
$starting_rates['BtcTurk']['All']=39;
$starting_rates['All']['FIAT']=$starting_rates['BtcTurk']['TRY']+$starting_rates['KRAKEN']['ZUSD']+$starting_rates['KRAKEN']['ZEUR'];
$starting_rates['All']['CRYPTO']=$starting_rates['BtcTurk']['BTC']+$starting_rates['KRAKEN']['XXBT'];

$starting_rates['All']['Total']=100;

$alarm_rates=array();
$alarm_rates['BtcTurk']['FIAT']['warning']=(3/$starting_rates['All']['FIAT']);
$alarm_rates['BtcTurk']['FIAT']['alarm']=(1/$starting_rates['All']['FIAT']);
$alarm_rates['Kraken']['FIAT']['warning']=(3/$starting_rates['All']['FIAT']);
$alarm_rates['Kraken']['FIAT']['alarm']=(1/$starting_rates['All']['FIAT']);

$alarm_rates['BtcTurk']['CYPTO']['warning']=(3/(10*$starting_rates['All']['CRYPTO']))*100;
$alarm_rates['BtcTurk']['CYPTO']['alarm']=(1/(10*$starting_rates['All']['CRYPTO']))*100;
$alarm_rates['Kraken']['CYPTO']['warning']=(3/(10*$starting_rates['All']['CRYPTO']))*100;
$alarm_rates['Kraken']['CYPTO']['alarm']=(1/(10*$starting_rates['All']['CRYPTO']))*100;

$starting_values=array();
$starting_values['BtcTurk']['TRY']=32946.6023;
$starting_values['BtcTurk']['BTC']=0.01180000;
$starting_values['BtcTurk']['ETH']=0.63170000;
$starting_values['BtcTurk']['LTC']=8.72781627;
$starting_values['BtcTurk']['XRP']=7296.11641988;
$starting_values['BtcTurk']['XLM']=2.62862000;
$starting_values['BtcTurk']['EOS']=0;
$starting_values['BtcTurk']['DASH']=4.96730000;

$starting_values['Kraken']['ZUSD']=0.9927;
$starting_values['Kraken']['XXBT']=0.27250607;
$starting_values['Kraken']['XETH']=5.44753372;
$starting_values['Kraken']['XLTC']=0.05638373;
$starting_values['Kraken']['XXRP']=51.55050127;
$starting_values['Kraken']['XXLM']=3939.78110000;
$starting_values['Kraken']['EOS']=0;
$starting_values['Kraken']['DASH']=5.03270080;

$starting_values['CUR_RATE']['USDTRY']=5.90;

$starting_values['All']['TRY']=$starting_values['BtcTurk']['TRY']+$starting_values['Kraken']['ZUSD']*$starting_values['CUR_RATE']['USDTRY'];

$starting_values['All']['BTC']=$starting_values['BtcTurk']['BTC']+$starting_values['Kraken']['XXBT'];
$starting_values['All']['ETH']=$starting_values['BtcTurk']['ETH']+$starting_values['Kraken']['XETH'];
$starting_values['All']['LTC']=$starting_values['BtcTurk']['LTC']+$starting_values['Kraken']['XLTC'];
$starting_values['All']['XRP']=$starting_values['BtcTurk']['XRP']+$starting_values['Kraken']['XXRP'];
$starting_values['All']['XLM']=$starting_values['BtcTurk']['XLM']+$starting_values['Kraken']['XXLM'];
$starting_values['All']['EOS']=$starting_values['BtcTurk']['EOS']+$starting_values['Kraken']['EOS'];
$starting_values['All']['DASH']=$starting_values['BtcTurk']['DASH']+$starting_values['Kraken']['DASH'];

$starting_values['All']['XXBT']=$starting_values['BtcTurk']['BTC']+$starting_values['Kraken']['XXBT'];
$starting_values['All']['XETH']=$starting_values['BtcTurk']['ETH']+$starting_values['Kraken']['XETH'];
$starting_values['All']['XLTC']=$starting_values['BtcTurk']['LTC']+$starting_values['Kraken']['XLTC'];
$starting_values['All']['XXRP']=$starting_values['BtcTurk']['XRP']+$starting_values['Kraken']['XXRP'];
$starting_values['All']['XXLM']=$starting_values['BtcTurk']['XLM']+$starting_values['Kraken']['XXLM'];


$wallet_params=array();
$gain_rates=array();
$market_data=array();

//1. MARKET = KRAKEN
// 1 MARKET PİYASA XXBTZUSD yani BTCEUR
$market_data[0]['market']="Kraken";
$market_data[0]['user']="mustafa";
$market_data[0]['api_id']=1;

//2. MARKET = BTCTURK
// 2. MARKET PİYASA BTCTRY
$market_data[1]['market']="BtcTurk";
$market_data[1]['user']="mustafa";
$market_data[1]['api_id']=3;

//******************************************************//
// AŞAMA 0.1
//güncel EUR ve USD kurunu database üzerinden al ****DataBase eğer zaman aşımı varsa programdan çık

$cur_rates=array();
get_cur_date_from_db();
//print_r($cur_rates);
$now=date('Y-m-d H:i:s');


//******************************************************//
// AŞAMA 1
// BAĞLANTILARI OLUŞTUR ve GLOBAL olarak kullan

//1 MARKET
//KRAKEN BAĞLANTISI OLUŞTUR,

$error_status=array();

list($kraken_key,$kraken_secret)=get_market_api_secret_keys($market_data[0]['market'],$market_data[0]['user'],$market_data[0]['api_id']);
try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
} catch (Exception $e) {
  //  echo $e->getMessage();
  array_push($error_status, $e->getMessage());


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
    //$btcturk_connect_old = new Client ($btcturk_key, $btcturk_secret);
$btcturk_connect =  new BtcTurkPRO ($btcturk_key, $btcturk_secret);
} catch (Exception $e) {
  //  echo $e->getMessage();
  array_push($error_status, "M2 CONNECT API PROBLEM");

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
    print_json_results($basla);
}
if($wallet_get==1)
  parse_balances_from_market_data($market_data[0]['market'],$kraken_balances);

try {
$btcturk_balances=$btcturk_connect->getBalances();
//var_dump($btcturk_balances);
//echo "BT çalıştı----";
  $wallet_get++;
} catch (Exception $e) {
  echo "BT nononono";
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


update_market_balances_on_db();
$m1_cypto_moneys=['XXBT','XETH','XLTC','XXRP','XXLM','EOS','DASH','USDT'];
$m2_cypto_moneys=['BTC','ETH','LTC','XRP','XLM','EOS','DASH','USDT'];
calculate_wallet_rates_and_make_warnings($m1_cypto_moneys,$m2_cypto_moneys);


$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
print_json_results($basla);







?>
