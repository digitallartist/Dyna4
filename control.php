
<?php

error_reporting(0);
ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
@ini_set('display_errors', 0);




include ("includes/main_lib.php");
include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");
 
$max_gain=array();
$max_gain['straight']['coin']='';
$max_gain['straight']['rate']=-100;
$max_gain['straight']['price1']=0;
$max_gain['straight']['price2']=0;
$max_gain['reverse']['coin']='';
$max_gain['reverse']['rate']=-100;
$max_gain['reverse']['price1']=0;
$max_gain['reverse']['price2']=0;

function btcturk_coins(){
//  echo "here -> ";
      global $dbc;
      //$url="https://api.btcturk.com/api/v2/server/exchangeinfo";
      //$json = file_get_contents($url);
        //var_dump($json);
        //  $json_data = json_decode($json, true);
  
  
        $urlm = "https://api.btcturk.com/api/v2/server/exchangeinfo";
    
        $ch = curl_init();
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $urlm);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $data = curl_exec($ch);
      //  $datam=substr($data , 1, -1);
      //  $datam=substr($datam, 2);
      //  var_dump($data);
        $json_data=json_decode($data, TRUE);

        curl_close($ch);
        
        $a_timestamp=substr($json_data['data']['serverTime'], 0,10);
      //  echo   $a_timestamp ."<br/>";
        /*
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlm);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
        $response=curl_exec($ch);
        echo $response; // Google's HTML source will be printed
        curl_close($ch);
        */
        
  
    
        echo  date('Y-m-d H:i:s', $a_timestamp) . "<br/>" ;
        
      for($i=0;$i<count($json_data['data']['symbols']);$i++) {
            //  echo "". $json_data['data']['symbols'][$i]['numerator']."<br/>";
		         
  
          if($json_data['data']['symbols'][$i]['denominator']=="TRY"){
            kraken_coins($json_data['data']['symbols'][$i]['numerator']);
            
            //echo $json_data['data']['symbols'][$i]['numerator'] . "<br/>";
          }
          
      }
}

$say=0;
function kraken_coins($name){
    global $max_gain;
    global $say;
    global $dbc;
    usleep(200000);
    $say++;
    
    //  if($say<2){

      $url="https://api.kraken.com/0/public/AssetPairs?pair=" . $name . "USD";
      $json = file_get_contents($url);
      $json_data = json_decode($json, true);

  //var_dump($json_data);
      if($json_data['error'][0]=="") {
      
      $urlm = "http://www.atlasport.net/dyno4/cycle_all.php?m1=" . $name;
      
      $ch = curl_init();
      $timeout = 5;

      curl_setopt($ch, CURLOPT_URL, $urlm);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

      $data = curl_exec($ch);
      $datam=substr($data , 1, -1);
      $datam=substr($datam, 2);
      $datam2=json_decode($datam, TRUE);
      curl_close($ch);
      
      echo " " . $name;
  
      echo " -> " .  print_ytl($datam2['straight']['price'],4,'USD');
      echo "<br/>";

      $bb1="";
      $bb2="";
      if($datam2['straight']['normal']>2){
        $bb1="<b>";
        $bb2="</b>";
      }
      echo $bb1. $datam2['straight']['normal'] . $bb2 . "<br/>";

      $bb1="";
      $bb2="";

      if($datam2['reverse']['normal']>2){
        $bb1="<b>";
        $bb2="</b>";
      }

      echo $bb1. $datam2['reverse']['normal'] . $bb2 . "<br/>";
      echo " // " . $name. " -> " .  print_ytl($datam2['reverse']['price'],4,'TL') ."<br/></br>";
      

      if($datam2['straight']['normal']>$max_gain['straight']['rate']) {
        $max_gain['straight']['coin_sec']=$max_gain['straight']['coin'];
        $max_gain['straight']['rate_sec']=$max_gain['straight']['rate'];
        $max_gain['straight']['price1_sec']=$max_gain['straight']['price1'];
        $max_gain['straight']['price2_sec']=$max_gain['straight']['price2'];
        
        $max_gain['straight']['coin']=$name;
        $max_gain['straight']['rate']=$datam2['straight']['normal'];
        $max_gain['straight']['price1']=$datam2['straight']['price'];
        $max_gain['straight']['price2']=$datam2['reverse']['price'];
      }
      if($datam2['reverse']['normal']>$max_gain['reverse']['rate']) {
        $max_gain['reverse']['coin']=$name;
        $max_gain['reverse']['rate']=$datam2['reverse']['normal'];
        $max_gain['reverse']['price1']=$datam2['straight']['price'];
        $max_gain['reverse']['price2']=$datam2['reverse']['price'];
      }
      if($name=="XTZ"){
        
        $q5="SELECT * FROM withdraw_data";
        $r5 = @mysqli_query ($dbc, $q5); // Run the query.
        $wd_duration="";
        $wd_comfirmation="";
        $wd_cost_usd="";
        $wd_cost="";
        $wd_duration_2="";
        $wd_comfirmation_2="";
        $wd_cost_usd_2="";
        $wd_cost_2="";

          while($row_wd = mysqli_fetch_array ($r5, MYSQLI_ASSOC)){
              if($row_wd['name']==$max_gain['straight']['coin']){
              $wd_duration=$row_wd['duration_minutes'];
              $wd_comfirmation=$row_wd['duration_comfirmations'];
              $wd_cost=$row_wd['withdraw_fee'];
              $wd_cost_usd=$row_wd['withdraw_fee'] * $max_gain['straight']['price1'];
              }
              
              if($row_wd['name']==$max_gain['straight']['coin_sec']){
              $wd_duration_2=$row_wd['duration_minutes'];
              $wd_comfirmation_2=$row_wd['duration_comfirmations'];
              $wd_cost_2=$row_wd['withdraw_fee'];
              $wd_cost_usd_2=$row_wd['withdraw_fee'] * $max_gain['straight']['price1_sec'];
              }
          }
      
          echo "<br/>";
          echo "USD/TRY : <b>" .$datam2['straight']['usdtry'] ."</b>";
          echo "<br/><br/>";
          
          echo "Düz: <b>" . $max_gain['straight']['coin'] . "</b> Oran : <b>" . $max_gain['straight']['rate'] ."</b> AL @  <b>" .   print_ytl($max_gain['straight']['price1'],4,'USD') ." </b> --> SAT @  <b>" .   print_ytl($max_gain['straight']['price2'],4,'TL') ."</b>";
          echo "<br/>";
          echo "Masraf : " .print_ytl($wd_cost,6,$max_gain['straight']['coin']) . " =>  <b>". print_ytl($wd_cost_usd,2,'USD') ."</b>";
          echo "<br/>";
          echo "Süre : <b>" .$wd_duration . " dakika -> </b>" . $wd_comfirmation . " doğrulama" ;
          echo "<br/><br/>";
          
          echo "Düz 2: <b>" . $max_gain['straight']['coin_sec'] . "</b> Oran : <b>" . $max_gain['straight']['rate_sec'] ."</b> AL @  <b>" .   print_ytl($max_gain['straight']['price1_sec'],4,'USD') ." </b> --> SAT @  <b>" .   print_ytl($max_gain['straight']['price2_sec'],4,'TL') ."</b>";
          echo "<br/>";
          echo "Masraf 2 : " .print_ytl($wd_cost_2,6,$max_gain['straight']['coin_sec']) . " =>  <b>". print_ytl($wd_cost_usd_2,2,'USD') ."</b>";
          echo "<br/>";
          echo "Süre 2 : <b>" .$wd_duration_2 . " dakika -> </b>" . $wd_comfirmation_2 . " doğrulama" ;
    
        echo "<br/><br/>";
        echo "Ters: <b>" . $max_gain['reverse']['coin'] . "</b> Oran: <b>" . $max_gain['reverse']['rate']."</b> AL @ <b>" .   print_ytl($max_gain['reverse']['price2'],4,'TL')."</b> ---> SAT @ <b>" .   print_ytl($max_gain['reverse']['price1'],4,'USD') ."</b>";
      
      
      
      
      
      } 
  //  }            
    }
                  
}


function get_cur(){
  

                
            
    $urlm = "http://www.atlasport.net/dyno4/fiat_cur_get.php";
      
      $ch = curl_init();
      $timeout = 5;

      curl_setopt($ch, CURLOPT_URL, $urlm);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

      $data = curl_exec($ch);

      curl_close($ch);

}


//kraken_coins('ADA');
get_cur();

btcturk_coins();



?>
