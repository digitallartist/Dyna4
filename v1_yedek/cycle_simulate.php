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



make_sample_wallet('BtcTurk','BTC','0.25')


var_dumb($wallet_params);
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
