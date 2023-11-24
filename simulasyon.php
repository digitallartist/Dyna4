<?php

error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);

 ini_set('memory_limit', '2048M'); // or you could use 1G
 ini_set('max_execution_time', '500'); // or you could use 1G


include("includes/db_connect.php");

/*
$cycle_pairs=array();
$cycle_pairs[0]='ETHTRY';
$cycle_pairs[1]='BTCTRY';
$cycle_pairs[2]='LTCTRY';
$cycle_pairs[3]='XRPTRY';
$cycle_pairs[4]='XLMTRY';



$market_wallets=array();
$market_wallets['Kraken']['FEE']=0.0026;

$market_wallets['Kraken']['USD']=3278;
$market_wallets['Kraken']['BTC']=0.24;
$market_wallets['Kraken']['ETH']=3.5;
$market_wallets['Kraken']['LTC']=4.4;
$market_wallets['Kraken']['XRP']=3600;
$market_wallets['Kraken']['XLM']=2000;


$market_wallets['BtcTurk']['FEE']=0.002;

$market_wallets['BtcTurk']['TRY']=17910;
$market_wallets['BtcTurk']['BTC']=0.2443;
$market_wallets['BtcTurk']['ETH']=3.579;
$market_wallets['BtcTurk']['LTC']=4.38;
$market_wallets['BtcTurk']['XRP']=3747;
$market_wallets['BtcTurk']['XLM']=1942;


*/

$cycle_pairs=array();
$cycle_pairs['BTCTRY']=0;
$cycle_pairs['ETHTRY']=1;
$cycle_pairs['LTCTRY']=1;
$cycle_pairs['XRPTRY']=0;
$cycle_pairs['XLMTRY']=0;




$minimum_order=array();
$minimum_order['BTC']=0.001;
$minimum_order['ETH']=0.02;
$minimum_order['LTC']=0.1;
$minimum_order['XRP']=20;
$minimum_order['XLM']=20;


$withdraw_fees=array();
$withdraw_fees['Kraken']['BTC']=0.0005;
$withdraw_fees['Kraken']['ETH']=0.005;
$withdraw_fees['Kraken']['LTC']=0.001;
$withdraw_fees['Kraken']['XRP']=0.02;
$withdraw_fees['Kraken']['XLM']=0.00002;
$withdraw_fees['BtcTurk']['BTC']=0;
$withdraw_fees['BtcTurk']['ETH']=0;
$withdraw_fees['BtcTurk']['LTC']=0;
$withdraw_fees['BtcTurk']['XRP']=0;
$withdraw_fees['BtcTurk']['XLM']=0;


$cycle_volumes=array();
$cycle_volumes['STRAIGHT']['DONE']=0;
$cycle_volumes['STRAIGHT']['MISSED']=0;
$cycle_volumes['REVERSE']['DONE']=0;
$cycle_volumes['REVERSE']['MISSED']=0;

$withdraw_count=array();
$withdraw_count['STRAIGHT']['BTC']=0;
$withdraw_count['STRAIGHT']['ETH']=0;
$withdraw_count['STRAIGHT']['LTC']=0;
$withdraw_count['STRAIGHT']['XRP']=0;
$withdraw_count['STRAIGHT']['XLM']=0;
$withdraw_count['REVERSE']['BTC']=0;
$withdraw_count['REVERSE']['ETH']=0;
$withdraw_count['REVERSE']['LTC']=0;
$withdraw_count['REVERSE']['XRP']=0;
$withdraw_count['REVERSE']['XLM']=0;


$market_wallets=array();
$market_wallets['Kraken']['FEE']=0.0026;
$market_wallets['Kraken']['USD']=6822.33*2;
$market_wallets['Kraken']['BTC']=0;
$market_wallets['Kraken']['ETH']=3.156566*2;
$market_wallets['Kraken']['LTC']=6.325591*2;
$market_wallets['Kraken']['XRP']=0;
$market_wallets['Kraken']['XLM']=0;

$market_wallets['BtcTurk']['FEE']=0.002;
$market_wallets['BtcTurk']['TRY']=40000*2;
$market_wallets['BtcTurk']['BTC']=0;
$market_wallets['BtcTurk']['ETH']=3.156566*2;
$market_wallets['BtcTurk']['LTC']=6.325591*2;
$market_wallets['BtcTurk']['XRP']=0;
$market_wallets['BtcTurk']['XLM']=0;


$market_starting_wallets=array();

$market_starting_wallets['Kraken']['USD']=$market_wallets['Kraken']['USD'];
$market_starting_wallets['Kraken']['BTC']=$market_wallets['Kraken']['BTC'];
$market_starting_wallets['Kraken']['ETH']=$market_wallets['Kraken']['ETH'];
$market_starting_wallets['Kraken']['LTC']=$market_wallets['Kraken']['LTC'];
$market_starting_wallets['Kraken']['XRP']=$market_wallets['Kraken']['XRP'];
$market_starting_wallets['Kraken']['XLM']=$market_wallets['Kraken']['XLM'];

$market_starting_wallets['BtcTurk']['TRY']=$market_wallets['BtcTurk']['TRY'];
$market_starting_wallets['BtcTurk']['BTC']=$market_wallets['BtcTurk']['BTC'];
$market_starting_wallets['BtcTurk']['ETH']=$market_wallets['BtcTurk']['ETH'];
$market_starting_wallets['BtcTurk']['LTC']=$market_wallets['BtcTurk']['LTC'];
$market_starting_wallets['BtcTurk']['XRP']=$market_wallets['BtcTurk']['XRP'];
$market_starting_wallets['BtcTurk']['XLM']=$market_wallets['BtcTurk']['XLM'];

$withdraw_time_rate=1/20;

$withdraw_time=array();
$withdraw_time['BTC']=($market_wallets['Kraken']['BTC']+$market_wallets['BtcTurk']['BTC'])*$withdraw_time_rate;
$withdraw_time['ETH']=($market_wallets['Kraken']['ETH']+$market_wallets['BtcTurk']['ETH'])*$withdraw_time_rate;
$withdraw_time['LTC']=($market_wallets['Kraken']['LTC']+$market_wallets['BtcTurk']['LTC'])*$withdraw_time_rate;
$withdraw_time['XRP']=($market_wallets['Kraken']['XRP']+$market_wallets['BtcTurk']['XRP'])*$withdraw_time_rate;
$withdraw_time['XLM']=($market_wallets['Kraken']['XLM']+$market_wallets['BtcTurk']['XLM'])*$withdraw_time_rate;
$withdraw_time['USD']=$market_wallets['Kraken']['USD']*$withdraw_time_rate;
$withdraw_time['TRY']=$market_wallets['BtcTurk']['TRY']*$withdraw_time_rate;
$target_rate=2;
$rev_target_rate=1;

$sql_limit_parameters=" LIMIT 3000000";
$usd_tl=5.863100;




$usd_eq=$market_wallets['Kraken']['USD'] + ($market_wallets['BtcTurk']['TRY'] / $usd_tl);
$tl_eq=($market_wallets['Kraken']['USD'] * $usd_tl ) + $market_wallets['BtcTurk']['TRY'] ;

echo "<br/> BAŞLANGIÇ : " . $market_wallets['Kraken']['USD'] . " USD  +  " . $market_wallets['BtcTurk']['TRY'] . " TL  = " . $usd_eq . " USD = " . $tl_eq . " TL";
echo "<hr />";
echo "

</br/>";


$q="SELECT * FROM cycle_results
WHERE cur_time>'2019-06-17 17:50:44'
ORDER BY id ASC " . $sql_limit_parameters ;
$r = @mysqli_query ($dbc, $q); // Run the query.
$i=0;




  while($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
    $i++;
    $current_volume=0;
    $current_buy_price=0;
    $current_sell_price=0;
    $current_usd_volume=0;
    $current_try_volume=0;

    $usd_tl=$row['usd_tl'];
    $current_pairs=$row['pairs2'];
    $current_rate=$row['rate'];
    $current_rev_rate=$row['rev_rate'];
    $cycle_direction='';
    $current_cypto='';

    if($cycle_pairs[$current_pairs]==1) {

      $current_cypto = substr($current_pairs, 0, 3);

        if($current_rate>$target_rate) {
              $cycle_direction='STRAIGHT';

              $current_buy_price=$row['market1_ask_price'];
              $current_sell_price=$row['market2_bid_price'];
              $current_m1_vol=($market_wallets['Kraken']['USD']*(1-$market_wallets['Kraken']['FEE']))/$current_buy_price;
              $current_m2_vol=$market_wallets['BtcTurk'][$current_cypto];

              $min_market_volume=min($row['market1_ask_volume'],$row['market2_bid_volume']);
              $current_volume=min($row['market1_ask_volume'],$row['market2_bid_volume'],$current_m1_vol,$current_m2_vol);

              $act_usd_volume=$min_market_volume*$current_buy_price;

              if($current_volume>=$minimum_order[$current_cypto]){
                $current_usd_volume=-1*($current_volume*$current_buy_price*(1+$market_wallets['Kraken']['FEE']));
                $current_try_volume=$current_volume*$current_sell_price*(1-$market_wallets['BtcTurk']['FEE']);
              } else {
                $current_volume=0;
              }



        } else if($current_rev_rate>$rev_target_rate) {
              $cycle_direction='REVERSE';

              $current_buy_price=$row['market2_ask_price'];
              $current_sell_price=$row['market1_bid_price'];
              $current_m1_vol=$market_wallets['Kraken'][$current_cypto];
              $current_m2_vol=($market_wallets['BtcTurk']['TRY']*(1-$market_wallets['BtcTurk']['FEE']))/$current_buy_price;

              $min_market_volume=min($row['market2_ask_volume'],$row['market1_bid_volume']);
              $current_volume=min($row['market2_ask_volume'],$row['market1_bid_volume'],$current_m1_vol,$current_m2_vol);

              $act_usd_volume=$min_market_volume*$current_sell_price;

              if($current_volume>=$minimum_order[$current_cypto]){
                $current_usd_volume=$current_volume*$current_sell_price*(1-$market_wallets['Kraken']['FEE']);
                $current_try_volume=-1*($current_volume*$current_buy_price*(1+$market_wallets['BtcTurk']['FEE']));



              } else {
                $current_volume=0;
              }

        }



      $market_wallets['Kraken']['USD']+=$current_usd_volume;
      $market_wallets['BtcTurk']['TRY']+=$current_try_volume;
        if($cycle_direction=="STRAIGHT"){
          $market_wallets['Kraken'][$current_cypto]+=$current_volume;
          $market_wallets['BtcTurk'][$current_cypto]-=$current_volume;
        } else if($cycle_direction=="REVERSE") {
          $market_wallets['Kraken'][$current_cypto]-=$current_volume;
          $market_wallets['BtcTurk'][$current_cypto]+=$current_volume;
        }






        if($current_volume>0) {
          $cycle_volumes[$cycle_direction]['DONE']+=abs($current_usd_volume);
          $cycle_volumes[$cycle_direction]['MISSED']+=abs($act_usd_volume)-abs($current_usd_volume);
        }

        else if($cycle_direction!="")
            $cycle_volumes[$cycle_direction]['MISSED']+=abs($act_usd_volume);


            if($current_cypto!="" && $cycle_direction!="" && $current_volume>0) {
              $total_cry= $market_wallets['Kraken'][$current_cypto]+  $market_wallets['BtcTurk'][$current_cypto];
              $usd_eq=$market_wallets['Kraken']['USD'] + ($market_wallets['BtcTurk']['TRY'] / $usd_tl);
              $tl_eq=($market_wallets['Kraken']['USD'] * $usd_tl ) + $market_wallets['BtcTurk']['TRY'] ;

              echo "<br/> <br/>" . $i . "-) " . $current_rate . " / " . $current_rev_rate  . " -- KUR : " . $usd_tl . "-- " . $current_volume  .  " Tarih: " . $row['entry'] . "<br/> " . $market_wallets['Kraken']['USD'] . " USD  +  " . $market_wallets['BtcTurk']['TRY'] . " TL  = " . $usd_eq . " USD = " . $tl_eq . " TL";
              echo "<br/> " .  $market_wallets['Kraken'][$current_cypto]  . " + " . $market_wallets['BtcTurk'][$current_cypto]  . " = " . $total_cry . " " . $current_cypto;

            }


            if($market_wallets['Kraken'][$current_cypto]<=$withdraw_time[$current_cypto] || $market_wallets['BtcTurk'][$current_cypto]<=$withdraw_time[$current_cypto] ){

              if($market_wallets['BtcTurk'][$current_cypto]<=$withdraw_time[$current_cypto] ){
                $market_wallets['Kraken']['USD']-=$withdraw_fees['Kraken']['BTC']*$row['market1_ask_price'];
                $withdraw_count['STRAIGHT'][$current_cypto]+=1;
              } else {
                  $market_wallets['BtcTurk']['TRY']-=$withdraw_fees['BtcTurk']['BTC']*$row['market2_ask_price'];
                  $withdraw_count['REVERSE'][$current_cypto]+=1;
              }

              $market_wallets['Kraken'][$current_cypto]=$market_starting_wallets['Kraken'][$current_cypto];
              $market_wallets['BtcTurk'][$current_cypto]=$market_starting_wallets['BtcTurk'][$current_cypto];

              echo "<br/>" . $current_cypto . "
               eşitleme yapılacak. " .   $market_wallets['Kraken'][$current_cypto] . " ; " .   $market_wallets['BtcTurk'][$current_cypto] . "
              </br/>";

            }

            if($market_wallets['Kraken']['USD']<=$withdraw_time['USD'] ||   $market_wallets['BtcTurk']['TRY']<=$withdraw_time['TRY'] ){

              if($market_wallets['Kraken']['USD']<=$withdraw_time['USD']  ){
                $fark=$market_starting_wallets['Kraken']['USD']-$market_wallets['Kraken']['USD'];
                $fark_tl=($fark*$usd_tl);
                  $ek=mt_rand($fark_tl*0.0046, $fark_tl*0.01);
                $fark_tl+=$ek;
                $market_wallets['Kraken']['USD']=$market_starting_wallets['Kraken']['USD'];
                $market_wallets['BtcTurk']['TRY']-=$fark_tl;
                $withdraw_count['REVERSE']['USD']+=1;
              } else {
                $fark=$market_wallets['Kraken']['USD']-$market_starting_wallets['Kraken']['USD'];
                $fark_tl=($fark*$usd_tl);
                $ek=mt_rand(-1*$fark_tl*0.0046, $fark_tl*0.01);
                $fark_tl+=$ek;

                $market_wallets['BtcTurk']['TRY']+=$fark_tl;
                $market_wallets['Kraken']['USD']=$market_starting_wallets['Kraken']['USD'];
                $withdraw_count['STRAIGHT']['TRY']+=1;
              }



              echo "<br/> USD -TRY
               dengeleme yapıldı. " .   $market_wallets['Kraken']['USD'] . " ; " .    $market_wallets['BtcTurk']['TRY']. "
              </br/>";

            }


    }









}

echo "<br/>

</br/>";

print_r($cycle_volumes);
