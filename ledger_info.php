<?php

include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");

include("includes/functions.php");
require_once('vendor/autoload.php');



$secret_params=array();
$secret_params['Kraken']['mustafa'][0]['key']='oi2mpcWuPGUSirj5AdptghaYclwqy3SyKIdtI62j/8UujbkNPJKVaVZI';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][0]['secret']='xYVsE3Nw53u1WpB+VWppKXPRDF5WUPDNcRJIjIGsFfEWMfm0XIMPcWIDdtWaC9Y9+lIcTLoXE5j/z9QkaJaJ3w=='; //sadece wallet
$secret_params['Kraken']['mustafa'][1]['key']='iae++6LG3bWoLuBUME4p8s0dK+8s9+SZYcgU9lygKZ9GrU7hLAUY3ErP';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][1]['secret']='DBod09NBjEM6qcvynNk/kd9EuF2ONV0lNuTbqx9CwqkfEdRD0BtZg31ELcA+++q2MNaZs/ineCG1V/xCtH6EgQ=='; //sadece wallet
$secret_params['Kraken']['mustafa'][2]['key']='qBXQZnZHxOqOU8cMIooOhgJ4ioPBTX7y8Z9a4mekgFsZ6dp0exRpxKmm';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][2]['secret']='oE/4ny5hvxPrlw4B0p8C5RcIZAPy4DQXHbiXdg5cX8h8ZYh+xBG3w7/Jc8N+7jCkFZgUMFg2gD+69lwTcq5VKA=='; //sadece wallet




$market_data=array();

$market_data[0]['market']="Kraken";
$market_data[0]['coin1']="XXBT";
$market_data[0]['coin2']="ZUSD";
$market_data[0]['pairs']="XXBTZUSD";
$market_data[0]['pairs2']="XXBT_ZUSD";
$market_data[0]['minumum_order']=0.002;
$market_data[0]['user']="mustafa";
$market_data[0]['api_id']=2;



list($kraken_key,$kraken_secret)=get_market_api_secret_keys($market_data[0]['market'],$market_data[0]['user'],$market_data[0]['api_id']);
try {
    $kraken_connect = new \HanischIt\KrakenApi\KrakenApi($kraken_key, $kraken_secret);
} catch (Exception $e) {
    echo $e->getMessage();
}
/*
$kraken_balances = $kraken_connect->getAccountBalance();
parse_balances_from_market_data('Kraken',$kraken_balances);
*/


try {
$kraken_withdraw = $kraken_connect->getLedgersInfo(  'currency','all','withdrawal');
print_r($kraken_withdraw);
//$refid=$kraken_withdraw->getRefid();
//echo $refid;
} catch (Exception $e) {
    echo $e->getMessage();
}

/*
//db de cüzdanı günceller
update_market_balances_on_db();

//dbden cüzdanı çeker
get_balances_from_db();
*/

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
