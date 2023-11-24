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


  function calculate_wallet_rates_and_make_warnings($m1_cypto_moneys,$m2_cypto_moneys,$m1_fiat_money='ZUSD',$m2_fiat_money='TRY',$m1='Kraken',$m2='BtcTurk'){

        global $dbc;
		
		

  $attachments=array();
  $timestamp = time();


  $attachments[0]["ts"]=$timestamp;
  $attachments[0]["color"]="#009900";
  $attachments[0]["text"]="SYSTEM WORKING";
  $attachments[0]["footer"]="Dyna Cycle API";
   
	  
	      $q="SELECT *, SUM(IF(rate>1.2,1,0)) duz_num, SUM(IF(rate>2.5,1,0)) duz_super_num, SUM(IF(rev_rate>-0.25,1,0)) ters_num, SUM(IF(rev_rate>0.25,1,0)) ters_super_num,   FROM cycle_results WHERE rev_rate<>0  ";
      $r = @mysqli_query ($dbc, $q); // Run the query.
        while($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
		}			

    
    $attachments[0]["fields"][0]["title"]="MISSING FIAT - CHECK THE SYSTEM";
    $attachments[0]["fields"][0]["value"]=$m1 ." -> ". $m1_fiat_money ." -> " . print_ytl($fiat_money_status[$m1]['percent'],2,'%');
    $attachments[0]["fields"][0]["short"]=true;
  send_slack_message(' ' , 'DYNA_WARNINGS', $attachments);
  


 
  echo "<br/>";

}




calculate_wallet_rates_and_make_warnings($m1_cypto_moneys,$m2_cypto_moneys);


$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
print_json_results($basla);







?>
