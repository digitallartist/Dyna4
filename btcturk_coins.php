
<?php
/*
error_reporting(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
@ini_set('display_errors', 0);
*/

include ("includes/main_lib.php");
include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");
 



function btcturk_coins(){
  
  //echo "here";
      global $dbc;

      $url="https://api.btcturk.com/api/v2/server/exchangeinfo";
      $json = file_get_contents($url);
      $json_data = json_decode($json, true);

        for($i=0;$i<count($json_data['data']['symbols']);$i++) {
  
            //  echo "". $json_data['data']['symbols'][$i]['numerator']."<br/>";
		  // echo "<br/>".date('Y-m-d H:i:s', $a_timestamp);
      
      
          if($json_data['data']['symbols'][$i]['denominator']=="TRY")
              kraken_coins($json_data['data']['symbols'][$i]['numerator']);
        }

    

}


function kraken_coins($name){
  

      $url="https://api.kraken.com/0/public/AssetPairs?pair=" . $name . "USD";
      $json = file_get_contents($url);
      $json_data = json_decode($json, true);

  //var_dump($json_data);
              if($json_data['error'][0]=="") {
                
                echo $name."<br/>";
                $urlm = "http://www.sansmetre.com/dyno4/cycle_all.php?m1=" . $name;
                
                
                $ch = curl_init();
      $timeout = 5;

      curl_setopt($ch, CURLOPT_URL, $urlm);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

      $data = curl_exec($ch);

      curl_close($ch);

      echo $data;
                
                
                echo $name." bitti<br/></br>";
                
              }
                  
		

    

}


//kraken_coins('ADA');
btcturk_coins();



?>
