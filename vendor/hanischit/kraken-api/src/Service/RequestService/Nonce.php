<?php
/**
 * @author fabian.hanisch
 * @since  2017-07-17
 */

namespace HanischIt\KrakenApi\Service\RequestService;

/**
 * Class Nonce
 *
 * @package HanischIt\KrakenApi\Service\RequestService
 */

 function KgetServerTime(){
   //  Initiate curl
  $ch = curl_init();
  // Will return the response, if false it print the response
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // Set the url
  curl_setopt($ch, CURLOPT_URL,'https://api.kraken.com/0/public/Time');
  // Execute
  $result=curl_exec($ch);
  // Closing
  curl_close($ch);

  // Will dump a beauty json :3
 $serverTime=json_decode($result, true);

 return $serverTime;

 }


 //1 MARKET

class Nonce
{
    public function generate()
    {


/*
        $nonce = explode(' ', microtime());
        $nonce = $nonce[1] . str_pad(substr($nonce[0], 2, 6), 6, '0');
        echo "1nonce: " . $nonce. "<br/>

        </br/>";*/

$kserver=KgetServerTime();

$nonce=$kserver['result']['unixtime'] ;
$nonce=$nonce."000000";

        return $nonce;
    }
}
