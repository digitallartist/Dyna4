
<?php

@error_reporting(E_ALL & ~E_NOTICE);
  @ini_set('error_reporting', E_ALL & ~E_NOTICE);
  @ini_set('display_errors', '1');
  @ini_set('display_startup_errors', '1');
  @ini_set('ignore_repeated_errors', '1');


  
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


$i_s=0;
$i_e=2000;
if(isset($_GET['s']))
  $i_s=$_GET['s'];
if(isset($_GET['e']))
  $i_e=$_GET['e'];
  
$mantik=0;
if(isset($_GET['mantik']))
$mantik=$_GET['mantik'];


function btcturk_coins($i_s,$i_e,$mantik){
//  echo "here -> ";
      global $dbc;
      
$mantikli1=array('ATOM','EOS','FTM','FET','XRP','SOL','XLM','LUNA','LUNC','BTC','AVAX','TRX','ADA','FIL','DOT','USDT','ALGO','XTZ');
$mantikli2=array('APT','APE','GALA','AAVE','ANKR','APE','AUDIO','AXS','BNT','BAT','LINK','CHZ','CVC','COMP','CRV','MANA','ENJ','ETH','ENS','IMX','LPT','LRC','MKR','OMG','PAXG','PLA','MATIC','QNT','SHIB','SPELL','STORJ','SNX','GRT','SAND','UNI','UMA','USDC');
$mantikli3=array('ETHW','ETC','RLC','RNDR','LTC','DOGE','DASH');

$mantikli_dizi=array();
if($mantik==1)
  $mantikli_dizi=array_merge($mantikli1);
else if($mantik==2)
  $mantikli_dizi=array_merge($mantikli1,$mantikli2);
  else if($mantik==3)
    $mantikli_dizi=array_merge($mantikli1,$mantikli2,$mantikli3);
    
    
    
    if(count($mantikli_dizi)>0) {
      $jkl=0;
        for($i=0;$i<count($mantikli_dizi);$i++) {
          $jkl++;
          kraken_coins($mantikli_dizi[$i],$jkl,$i_e,$mantik);
        }
    } else {

      //$url="https://api.btcturk.com/api/v2/server/exchangeinfo";
      //$json = file_get_contents($url);
        //var_dump($json);
        //  $json_data = json_decode($json, true);
  
  
        $urlm = "https://api.btcturk.com/api/v2/server/exchangeinfo";
        /*
        $ch = curl_init();
        $timeout = 5;

        curl_setopt($ch, CURLOPT_URL, $urlm);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $data = curl_exec($ch);
        //$datam=substr($data , 1, -1);
        //$datam=substr($datam, 2);
        $json_data=json_decode($data, TRUE);
        echo "mamaam".$json_data;
        curl_close($ch);
        */
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlm);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
        $response=curl_exec($ch);
          $json_data=json_decode($response, TRUE);
      //  echo $response; //
        curl_close($ch);
        
  
    
      $i=0;
      $jkl=0;
      for($i=0;$i<count($json_data['data']['symbols']);$i++) {
    //  echo $json_data['data']['symbols'][$i]['denominator'] . " " . $json_data['data']['symbols'][$i]['numerator'];
    //  echo "<br/>";
        if($json_data['data']['symbols'][$i]['denominator']=="TRY"){
          $jkl++;
        
          //  echo "". $json_data['data']['symbols'][$i]['numerator']."<br/>";
            //  echo "<br/>".date('Y-m-d H:i:s', $a_timestamp);
            if($jkl>=$i_s && $jkl<=$i_e)
            kraken_coins($json_data['data']['symbols'][$i]['numerator'],$jkl,$i_e,$mantik);
          }
          
      }
    }
}

$say=0;
function kraken_coins($name,$jkl,$i_e,$mantik){
    global $max_gain;
    global $say;
    global $dbc;
    
    $say++;
    
    //  if($say<2){
    //  usleep(300000); // sleep 0,3 seconds
    $name_control=$name;
    
    if($name=='LUNA')
    $name_control='LUNA2';
    
    if($name=='LUNC')
    $name_control='LUNA';
    
      $url="https://api.kraken.com/0/public/AssetPairs?pair=" . $name_control . "USD";
      $json = file_get_contents($url);
      $json_data = json_decode($json, true);

      //var_dump($json_data['error']);
      if(count($json_data['error'])==0) {

      $urlm = "https://www.sansmetre.com/dyno4/cycle_all.php?m1=".$name;
      //  $urlm = "https://www.google.com";

      $curl = curl_init();


      curl_setopt_array($curl, array(
        CURLOPT_URL => $urlm,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_HTTPHEADER => array(
          "Cache-Control: no-cache"
        ),
      ));

      $data = curl_exec($curl);
      $err = curl_error($curl);

      curl_close($curl);

      
    //  echo $data;
      $datam=substr($data , 1, -1);
      $datam=substr($datam, 2);
      $datam2=json_decode($datam, TRUE);
    //  echo "<br/>";
    

  
    //  var_dump($datam2);
    
    echo $q5u;
   
    $q5u="UPDATE withdraw_durations SET current_price='" .  $datam2['straight']['price'] . "', withdraw_fee_usd=withdraw_fee*" .  $datam2['straight']['price'] . " WHERE name='" . $name . "'";
    echo $q5u;
    $r5u = @mysqli_query ($dbc, $q5u); // Run the query.



      echo  $name ;
  
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
      if(($mantik<=1 && $name=="XTZ") || ($mantik==2 && $name=="USDC") ||  ($mantik==3 && $name=="DASH") ||  $jkl==$i_e ){
        
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
        
        var_dump($max_gain);

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
  

                
            
    $urlm = "https://www.sansmetre.com/dyno4/fiat_cur_get.php";
      
      $ch = curl_init();
      $timeout = 5;

      curl_setopt($ch, CURLOPT_URL, $urlm);

      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          
      
        curl_setopt($ch, CURLOPT_ENCODING, "");
          curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
              curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  "Cache-Control: no-cache"
                ));


      $data = curl_exec($ch);

      curl_close($ch);

}


//kraken_coins('ADA');

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



get_cur();

btcturk_coins($i_s,$i_e,$mantik);
echo "<br /><br />";
$saymayi_bitir = acilma_suresi();
$basla = $saymayi_bitir - $saymaya_basla;
echo $basla;

?>
