<?php
/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
*/
require_once("btcturkpro.class.php");

$secret_params=array();
$secret_params['Kraken']['mustafa'][0]['key']='oi2mpcWuPGUSirj5AdptghaYclwqy3SyKIdtI62j/8UujbkNPJKVaVZI';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][0]['secret']='xYVsE3Nw53u1WpB+VWppKXPRDF5WUPDNcRJIjIGsFfEWMfm0XIMPcWIDdtWaC9Y9+lIcTLoXE5j/z9QkaJaJ3w=='; //sadece wallet
$secret_params['Kraken']['mustafa'][1]['key']='iae++6LG3bWoLuBUME4p8s0dK+8s9+SZYcgU9lygKZ9GrU7hLAUY3ErP';//sadece wallet, herşey
$secret_params['Kraken']['mustafa'][1]['secret']='DBod09NBjEM6qcvynNk/kd9EuF2ONV0lNuTbqx9CwqkfEdRD0BtZg31ELcA+++q2MNaZs/ineCG1V/xCtH6EgQ=='; //sadece wallet

$secret_params['BtcTurk']['mustafa'][0]['key']='a6388f08-9e10-4917-ad61-a40743eeb97a'; //sadece wallet
$secret_params['BtcTurk']['mustafa'][0]['secret']='22QreSdVLfVEny3zCw/qwxUO18RfuksZ';//sadece wallet
$secret_params['BtcTurk']['mustafa'][1]['key']='63f9bea0-dad2-46b1-bc47-1f99a46b1510'; //açık emirler
$secret_params['BtcTurk']['mustafa'][1]['secret']='9yZ/yTMq5AJzp5B4Ndl9ZXsv4U3eR3nC';//açık emirler
$secret_params['BtcTurk']['mustafa'][2]['key']='247bd944-8606-445f-966e-abfb39c431dd'; //sipraiş
$secret_params['BtcTurk']['mustafa'][2]['secret']='BfWfxqzWxhTt+oOsquxmHwf3ETIOHS0O';//sipariş
$secret_params['BtcTurk']['mustafa'][3]['key']='15c1a1c0-a5db-4e2c-8ea9-578d8ae0b6fa'; //herşey
$secret_params['BtcTurk']['mustafa'][3]['secret']='hZmSsgtwxz+aGt3EweJpnzE1qrVYbd42';//herşey




// API anahtarını https://pro.btcturk.com/hesap-ayarlari/api adresinden alabilirsiniz
// You can get your api key at https://pro.btcturk.com/hesap-ayarlari/api
$apiKey = $secret_params['BtcTurk']['mustafa'][2]['key'];
$apiSecret = $secret_params['BtcTurk']['mustafa'][2]['secret'];

$request = new BtcTurkPRO ($apiKey, $apiSecret);

// Bakiyeleri listeleme
// get balances
//echo "<br/><br/><br/><b>Bu request ile balance çağırıyorum</b> print_r( $request->getBalances()) ... bunun sonucu boş.... <br/>";
//print_r( $request->getBalances() );


// Gerçekleşen Emirleri listeleme
// list successful trades
//print_r( $request->getTrades("DOT_TRY", 50) );

// Açık Emirleri Listeleme
// List open orders
//echo "<br/><br/><b>AÇIK DOT SİPARİŞLER  : </b><br/>";
//print_r ( $request->getOpenorders("DOT_TRY") );

// 40.000TL fiyat ile 0.001 BTC'lik LIMIT Emir
// Limit order
//
// 40.000TL fiyat, 39.800TL tetik fiyatı ile 0.001 BTC'lik LIMIT Emir
// Limir order with stop price
//print_r ( $request->placeOrder("BTC_TRY", "limit", "buy", "40000", "0.001", "39800") );

// Emir iptali
// Cancel order
//print_r ( $request->CancelOrder(1234567890) );

?>
