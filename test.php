<?php

include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");
include("btcturk-master/BtcTurkAPIClient.php");
include("coin_src/KrakenAPIClient.php");
include("includes/functions.php");

require_once('vendor/autoload.php');


//BTCturk bağlantı oluştur
list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys('BtcTurk','mustafa',0);
$btcturk_connect = new Client ($btcturk_key, $btcturk_secret);

$btcturk_prices=$btcturk_connect->getOrderBook('BTCTRY');

//var_dump($btcturk_prices);

list($btcturk_bid_price,$btcturk_bid_volume,$btcturk_bid_timestamp,$btcturk_ask_price,$btcturk_ask_volume,$btcturk_ask_timestamp, $btcturk_price_status)=parse_prices_and_volume_from_market_data('BtcTurk',$btcturk_prices,'BTC','TRY');


list($kraken_key,$kraken_secret)=get_market_api_secret_keys('Kraken','mustafa',0);

try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
    $orderBookResponse = $kraken_connect->getOrderBook("XXBTZUSD", 1);

} catch (Exception $e) {
    echo $e->getMessage();
}

list($kraken_bid_price,$kraken_bid_volume,$kraken_bid_timestamp,$kraken_ask_price,$kraken_ask_volume,$kraken_ask_timestamp, $kraken_price_status)=parse_prices_and_volume_from_market_data('Kraken',$orderBookResponse,'XXBT','ZUSD');

//$price_params=[];
//$price_params['Kraken']['mustafa'][0]['key'];

echo "bu <br/>";

echo "BTCTurk<br/>";
echo "Bid Price: " .$btcturk_bid_price ."<br/>";
echo "Bid Price: " .$price_params['BtcTurk']['BTCTRY']['bid']['price'] ."<br/>";

echo "Bid Volume: " .$btcturk_bid_volume ."<br/>";
echo "Ask Price: " .$btcturk_ask_price ."<br/>";
echo "Ask Volume: " .$btcturk_ask_volume ."<br/>";
echo "KRAKEN<br/>";
echo "Bid Price: " . $kraken_bid_price ."<br/>";
echo "Bid Volume: " . $kraken_bid_volume ."<br/>";
echo "Ask Price: " .$kraken_ask_price ."<br/>";
echo "Ask Volume: " .$kraken_ask_volume ."<br/>";

$cur_rates=array();
get_cur_date_from_db();
calculate_rates('Kraken','XXBT','ZUSD', 'BtcTurk','BTC','TRY');

/*
//BTCturk bağlantı oluştur
list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys('BtcTurk','mustafa',2);
$btcturk_connect = new Client ($btcturk_key, $btcturk_secret);

$btcturk_order_response=$btcturk_connect->getLimitSell('XRPTRY','10',"0","2","20");
print_r($btcturk_order_response);
echo "<br/>";echo "<br/>";
$btcturk_order_response=$btcturk_connect->getLimitSell('XRPTRY','11',"0","2","30");
print_r($btcturk_order_response);
echo "<br/>";echo "<br/>";
//BTCturk bağlantı oluştur
list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys('BtcTurk','mustafa',1);
$btcturk_connect = new Client ($btcturk_key, $btcturk_secret);
$btcturk_open_order_response=$btcturk_connect->getOpenOrders("XRPTRY");
print_r($btcturk_open_order_response);
*/
 echo "bbb";

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
