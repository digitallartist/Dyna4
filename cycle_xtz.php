<?php

//error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

include("includes/db_connect.php");
require_once("btcturk-master/BtcTurkAPIClient.php");
require_once("btcturk-pro/btcturkpro.class.php");
require_once("coin_src/KrakenAPIClient.php");
require_once("includes/functions.php");
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




$min_start_rates=array();
$min_start_rates['straight']['normal']=2.4;
$min_start_rates['straight']['first_sell']=2.4;
$min_start_rates['straight']['first_sell_diff']=0;
$min_start_rates['reverse']['normal']=2;
$min_start_rates['reverse']['first_buy']=2;
$min_start_rates['reverse']['first_buy_diff']=0;

$wallet_params=array();
$gain_rates=array();
$market_data=array();

//1. MARKET = KRAKEN
// 1 MARKET PİYASA XTZZUSD yani BTCEUR

$market_data[0]['market']="Kraken";
$market_data[0]['coin1']="XTZ";
$market_data[0]['coin2']="USD";
$market_data[0]['pairs']="XTZUSD";
$market_data[0]['pairs2']="XTZ_USD";
$market_data[0]['minumum_order']=1;
$market_data[0]['user']="mustafa";
$market_data[0]['api_id']=0;


//2. MARKET = BTCTURK
// 2. MARKET PİYASA BTCTRY
$market_data[1]['market']="BtcTurk";
$market_data[1]['coin1']="XTZ";
$market_data[1]['coin2']="TRY";
$market_data[1]['pairs']="XTZTRY";
$market_data[1]['pairs2']="XTZ_TRY";
$market_data[1]['minumum_order']=1;
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
if($control_time<$now){

//  send_slack_message('Kur zaman aşımı', 'info', 'info' );
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;

  $json_return_params['status']="NOT WORKING";
  $json_return_params['result']="false";
//  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
//  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="CUR RATE TIMEOUT YOU MUST RUN IT!";
  $json_return_params['color']="#FF0000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=1;

  print_json_results($basla);

}


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
//  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
//  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
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
  //$wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  //$wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
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



$q="SELECT * FROM markets";
$r = @mysqli_query ($dbc, $q); // Run the query.
  $m1_update=0;
  $m2_update=0;
  while($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
    $id=$row['id'];
    $need_balance_update=$row['need_balance_update'];
    $need_market=$row['name'];
    
    if($id==1 && $need_balance_update==1)
      $m1_update=1;
      
      if($id==2 && $need_balance_update==1)
        $m2_update=1;
  }
  
  

$wallet_get=0;
$wallet_update_it=0;
if($m1_update==1){
    try {
      $kraken_balances = $kraken_connect->getAccountBalance();
      $wallet_get++;
    } catch (Exception $e) {
      $saymayi_bitir = acilma_suresi();
      $basla = $saymayi_bitir - $saymaya_basla;
      $json_return_params["status"]="Temporary Problem";
      $json_return_params["result"]="false";
    //  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
    //  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
      $gain_rates['straight']['normal']="";
      $gain_rates['reverse']['normal'] ="";
      $json_return_params['act_rate']="";
      $json_return_params['explanation']="Account Balance Connection Problem M1";
      $json_return_params['color']="#FF8000";
      $json_return_params['slack_status_api']="CYCLE_WARNINGS";
      $json_return_params['slack_status']=0;
       // print_json_results($basla);
    }

    if($wallet_get==1 && $kraken_balances!=NULL){
      parse_balances_from_market_data($market_data[0]['market'],$kraken_balances);
      $wallet_update_it++;
      
    }
    else {
      
      $saymayi_bitir = acilma_suresi();
      $basla = $saymayi_bitir - $saymaya_basla;
      $json_return_params["status"]="Temporary Problem";
      $json_return_params["result"]="false";
    //  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
    //  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
      $gain_rates['straight']['normal']="";
      $gain_rates['reverse']['normal'] ="";
      $json_return_params['act_rate']="";
      $json_return_params['explanation']="Account Balance Connection Problem M1 --- DURUM";
      $json_return_params['color']="#FF8000";
      $json_return_params['slack_status_api']="CYCLE_WARNINGS";
      $json_return_params['slack_status']=0;
      
    } 
} else {
  
get_balances_from_db($market_data[0]['market'],'');
}
  
  
  $wallet_get=0;

if($m2_update==1){
    try {
    $btcturk_balances=$btcturk_connect->getBalances();
      $wallet_get++;
    } catch (Exception $e) {
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
      if($wallet_get==1 && $btcturk_balances!=NULL){
    parse_balances_from_market_data($market_data[1]['market'],$btcturk_balances);
      $wallet_update_it++;
    }
    else {
      $saymayi_bitir = acilma_suresi();
      $basla = $saymayi_bitir - $saymaya_basla;
      $json_return_params["status"]="Temporary Problem";
      $json_return_params["result"]="false";
      //$wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
      //$wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
      $gain_rates['straight']['normal']="";
      $gain_rates['reverse']['normal'] ="";
      $json_return_params['act_rate']="";
      $json_return_params['explanation']="Account Balance Connection Problem M2 --- DURUM";
      $json_return_params['color']="#FF8000";
      $json_return_params['slack_status_api']="CYCLE_WARNINGS";
      $json_return_params['slack_status']=0;
        print_json_results($basla);

    }
} else {
  
get_balances_from_db($market_data[1]['market'],'');

}

if($wallet_update_it>0)
update_market_balances_on_db();

  
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





//******************************************************//
// AŞAMA 4
//FİYATLARI MARKETTEN ÇEK VE DİZİYE ATA

/*
print_r($wallet_params);
//exit();

echo "<br/>
bitti
</br/>";
*/
$price_get=0;
try {
  $kraken_prices = $kraken_connect->getOrderBook($market_data[0]['pairs'], 1);
  $price_get++;
} catch (Exception $e) {
  //  echo $e->getMessage();
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
  $json_return_params["status"]="Temporary Problem";
  $json_return_params["result"]="false";
//  $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
//  $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="M1 Prices Get Problem";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;

  print_json_results($basla);

}

if($price_get==1)
  list($kraken_bid_price,$kraken_bid_volume,$kraken_bid_timestamp,$kraken_ask_price,$kraken_ask_volume,$kraken_ask_timestamp, $kraken_price_status)=parse_prices_and_volume_from_market_data($market_data[0]['market'],$kraken_prices,$market_data[0]['coin1'],$market_data[0]['coin2']);



try {

  $btcturk_price_first=$btcturk_connect->getOrderBook($market_data[1]['pairs2']);

  $btcturk_prices=$btcturk_price_first->data;
  $price_get++;
} catch (Exception $e) {
  //  echo $e->getMessage();
  $saymayi_bitir = acilma_suresi();
  $basla = $saymayi_bitir - $saymaya_basla;
  $json_return_params["status"]= "Temporary Problem";
  $json_return_params["result"]="false";
  //$wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ="";
  //$wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance']="";
  $gain_rates['straight']['normal']="";
  $gain_rates['reverse']['normal'] ="";
  $json_return_params['act_rate']="";
  $json_return_params['explanation']="M2 Prices Get Problem";
  $json_return_params['color']="#FF8000";
  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
  $json_return_params['slack_status']=0;

  print_json_results($basla);

}

if($price_get==2)
list($btcturk_bid_price,$btcturk_bid_volume,$btcturk_bid_timestamp,$btcturk_ask_price,$btcturk_ask_volume,$btcturk_ask_timestamp, $btcturk_price_status)=parse_prices_and_volume_from_market_data($market_data[1]['market'],$btcturk_prices,$market_data[1]['coin1'],$market_data[1]['coin2']);

/*

echo "FİYATLAR ÇEKİLDİ ----- <br/>"."<br/>";
echo $market_data[0]['market']."<br/>";
echo "Bid Price: " . $kraken_bid_price ."<br/>";
echo "Bid Volume: " . $kraken_bid_volume ."<br/>";
echo "Ask Price: " .$kraken_ask_price ."<br/>";
echo "Ask Volume: " .$kraken_ask_volume ."<br/>"."<br/>";

echo $market_data[1]['market']."<br/>";
echo "Bid Price: " .$btcturk_bid_price ."<br/>";
echo "Bid Volume: " .$btcturk_bid_volume ."<br/>";
echo "Ask Price: " .$btcturk_ask_price ."<br/>";
echo "Ask Volume: " .$btcturk_ask_volume ."<br/>"."<br/>";

print_r($price_params);

*/
//******************************************************//
// AŞAMA 5
//FİYATLAR FARKLILIKLARINI HESAPLA ve SİPARİŞ EMRİ VER

//

if($price_get>1)
calculate_rates_and_make_a_cycle($market_data[0]['market'],$market_data[0]['coin1'],$market_data[0]['coin2'], $market_data[1]['market'],$market_data[1]['coin1'],$market_data[1]['coin2']);

/*
echo "<br/>

</br/>";

print_r($wallet_params);

echo "<br/>

</br/>";
*/

$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
print_json_results($basla);







?>
