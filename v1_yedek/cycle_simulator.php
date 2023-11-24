<?php

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
require_once("includes/functions3.php");
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



$min_start_rates=array();
$min_start_rates['straight']['normal']=-30;
$min_start_rates['straight']['first_sell']=-30;
$min_start_rates['straight']['first_sell_diff']=0;
$min_start_rates['reverse']['normal']=0;
$min_start_rates['reverse']['first_buy']=0;
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
$market_data[0]['minumum_order']=0.002;
$market_data[0]['user']="mustafa";
$market_data[0]['api_id']=1;


//2. MARKET = BTCTURK
// 2. MARKET PİYASA BTCTRY
$market_data[1]['market']="BtcTurk";
$market_data[1]['coin1']="BTC";
$market_data[1]['coin2']="TRY";
$market_data[1]['pairs']="BTCTRY";
$market_data[1]['minumum_order']=0.002;
$market_data[1]['user']="mustafa";
$market_data[1]['api_id']=3;

$simulation_start_date='';
$simulation_end_date='';

$usd_try=0;
$eur_try=0;
$bas_try_eq=0;
$bas_usd_eq=0;

get_initial_wallets('2019-06-17 14:20:20','0','-1');
echo "<br/>";

//print_r($wallet_params);
$bas_try=$wallet_params['BtcTurk']['TRY']['avaiable_balance'];
$bas_usd=$wallet_params['Kraken']['USD']['avaiable_balance'];
$bas_btc=$wallet_params['BtcTurk']['BTC']['avaiable_balance']+$wallet_params['Kraken']['BTC']['avaiable_balance'];
$bas_eth=$wallet_params['BtcTurk']['ETH']['avaiable_balance']+$wallet_params['Kraken']['ETH']['avaiable_balance'];
$bas_ltc=$wallet_params['BtcTurk']['LTC']['avaiable_balance']+$wallet_params['Kraken']['LTC']['avaiable_balance'];
$bas_xrp=$wallet_params['BtcTurk']['XRP']['avaiable_balance']+$wallet_params['Kraken']['XRP']['avaiable_balance'];
$bas_xlm=$wallet_params['BtcTurk']['XLM']['avaiable_balance']+$wallet_params['Kraken']['XLM']['avaiable_balance'];

echo "<br/>";
echo $simulation_start_date;
echo "<br/>";

start_simulator($min_start_rates['straight']['normal'], $min_start_rates['reverse']['normal'], '2019-06-17 17:52:31', $simulation_end_date);


$bit_try=$wallet_params['BtcTurk']['TRY']['avaiable_balance'];
$bit_usd=$wallet_params['Kraken']['USD']['avaiable_balance'];
$bit_btc=$wallet_params['BtcTurk']['BTC']['avaiable_balance']+$wallet_params['Kraken']['BTC']['avaiable_balance'];
$bit_eth=$wallet_params['BtcTurk']['ETH']['avaiable_balance']+$wallet_params['Kraken']['ETH']['avaiable_balance'];
$bit_ltc=$wallet_params['BtcTurk']['LTC']['avaiable_balance']+$wallet_params['Kraken']['LTC']['avaiable_balance'];
$bit_xrp=$wallet_params['BtcTurk']['XRP']['avaiable_balance']+$wallet_params['Kraken']['XRP']['avaiable_balance'];
$bit_xlm=$wallet_params['BtcTurk']['XLM']['avaiable_balance']+$wallet_params['Kraken']['XLM']['avaiable_balance'];

$bit_try_eq=$wallet_params['BtcTurk']['TRY']['avaiable_balance'] + $wallet_params['Kraken']['USD']['avaiable_balance'] * $usd_try;
$bit_usd_eq=$wallet_params['BtcTurk']['TRY']['avaiable_balance'] / $usd_try +$wallet_params['Kraken']['USD']['avaiable_balance'];

echo "<br/><br/>ÖZET<br/>";

echo $bas_try . " -> " . $bit_try ;
echo "<br/>";
echo $bas_usd . " -> " . $bit_usd;
echo "<br/>";
echo $bas_btc . " -> " . $bit_btc;
echo "<br/>";
echo $bas_eth . " -> " . $bit_eth;
echo "<br/>";
echo $bas_ltc . " -> " . $bit_ltc;
echo "<br/>";
echo $bas_xrp . " -> " . $bit_xrp;
echo "<br/>";
echo $bas_xlm . " -> " . $bit_xlm;
echo "<br/>";

echo $bas_try_eq . " -> " . $bit_try_eq . " => " . print_ytl($bit_try_eq-$bas_try_eq,'2','TL') . " === " . print_ytl((($bit_try_eq / $bas_try_eq)-1)*100,2,'%');
echo "<br/>";
echo $bas_usd_eq . " -> " . $bit_usd_eq. " => " . print_ytl($bit_usd_eq-$bas_usd_eq,'2','TL') . " === " . print_ytl((($bit_usd_eq / $bas_usd_eq)-1)*100,2,'%');
echo "<br/>";

//print_r($wallet_params);
//cycle_simulator();


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
