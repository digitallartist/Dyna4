<?php
/*
@error_reporting(E_ALL & ~E_NOTICE);
@ini_set('error_reporting', E_ALL & ~E_NOTICE);
@ini_set('display_errors', '1');
@ini_set('display_startup_errors', '1');
@ini_set('ignore_repeated_errors', '1');
*/

require_once("send_slack_message.php");
    function print_ytl($amount, $decimal=2, $cur_symbol='', $bNull=false) {
        if (is_null($amount) && $bNull)
            return '';
        if ($cur_symbol=='') {
            return @number_format($amount, $decimal, ",", ".");
        } else {
            return @number_format($amount, $decimal, ",", ".") . " " . $cur_symbol;
        }
    }

    //Veritabanındaki USD/TL ve EUR/TL güncel kur bilgisini çeker
    function get_cur_date_from_db(){
      global $cur_rates;
      global $dbc;

      $q="SELECT * FROM cur_rates ORDER BY atimestamp DESC LIMIT 1";
      $r = @mysqli_query ($dbc, $q); // Run the query.
        if($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
          $cur_rates['EURTRY']=$row['eur_tl'];
          $cur_rates['USDTRY']=$row['usd_tl'];
          $cur_rates['CURTIME']=$row['atimestamp'];
          $cur_rates['APISOURCE']=$row['api_source'];
        }
        return $cur_rates;

    }


  //Market api bilgileri veritabanından çeker
  function get_market_api_secret_keys($exchange='Kraken',$user='mustafa', $order='0'){
    global $secret_params;
      $arr[0]=$secret_params[$exchange][$user][$order]['key'];
      $arr[1]=$secret_params[$exchange][$user][$order]['secret'];
      return $arr;
  }

//Marketten çekilmiş cüzdan bilgilerini parçalar ve diziye atar
  function parse_balances_from_market_data($exchange,$datas){
    global $wallet_params;
    /*
    $wallet_params[$exchange][$data->currency]['whole_balance']=0;
    $wallet_params[$exchange][$data->currency]['avaiable_balance']=0;
    $wallet_params[$exchange][$data->currency]['reserved_balance']=0;
    $wallet_params[$exchange][$data->currency]['taker_fee']=0;
    $wallet_params[$exchange][$data->currency]['maker_fee']=0;
    $wallet_params[$exchange][$data->currency]['act_balance']=0;
*/
    if($exchange=='BtcTurk') {
    foreach( @$datas as $data){
         $wallet_params[$exchange][$data->asset]['whole_balance']=$data->balance;
        $wallet_params[$exchange][$data->asset]['avaiable_balance']=$data->free;
        $wallet_params[$exchange][$data->asset]['reserved_balance']=$data->locked;
        $wallet_params[$exchange][$data->asset]['taker_fee']=0.002;
        $wallet_params[$exchange][$data->asset]['maker_fee']=0.002;
      }
    } else if($exchange=='Kraken') {
      foreach (@$datas->getBalanceModels() as $balanceModel) {
          //echo $balanceModel->getAssetName() . ": " . $balanceModel->getBalance() . "\n";
          $wallet_params[$exchange][$balanceModel->getAssetName()]['whole_balance']=$balanceModel->getBalance();
          $wallet_params[$exchange][$balanceModel->getAssetName()]['avaiable_balance']=$balanceModel->getBalance();
          $wallet_params[$exchange][$balanceModel->getAssetName()]['reserved_balance']=0;
          $wallet_params[$exchange][$balanceModel->getAssetName()]['taker_fee']=0.0026;
          $wallet_params[$exchange][$balanceModel->getAssetName()]['maker_fee']=0.0026;
      }
    }
    /*else if($exchange=='Kraken') {
      $res=$datas['result'];
    foreach($res as $key => $data){
        $wallet_params[$exchange][$key]['whole_balance']=$data;
        $wallet_params[$exchange][$key]['avaiable_balance']=$data;
        $wallet_params[$exchange][$key]['reserved_balance']=$data;
        $wallet_params[$exchange][$key]['taker_fee']=0.0026;
        $wallet_params[$exchange][$key]['maker_fee']=0.0026;
      }
    }*/
  }

//BTCCturk için genel olarak açık siparişleri çekme işi yok. Bu özel foksiyon o işe yarar

function get_any_open_orders_from_btcturk($btcturk_connect){

$all_open_btc=array();
$i=-1;
    try {
      $btcturk_open_orders=$btcturk_connect->getOpenOrders('BTCTRY');

      foreach( @$btcturk_open_orders as $order){
        if($order->id>0){
          $i++;
          $all_open_btc[$i]['id']=$order->id;
          $all_open_btc[$i]['datetime']=$order->datetime;
          $all_open_btc[$i]['type']=$order->type;
          $all_open_btc[$i]['price']=$order->price;
          $all_open_btc[$i]['amount']=$order->amount;
          $all_open_btc[$i]['pairsymbol']=$order->pairsymbol;

        }

      }
    } catch (Exception $e) {


    }

    try {
      $btcturk_open_orders=$btcturk_connect->getOpenOrders('ETHTRY');

      foreach( @$btcturk_open_orders as $order){
        if($order->id>0){
          $i++;
          $all_open_btc[$i]['id']=$order->id;
          $all_open_btc[$i]['datetime']=$order->datetime;
          $all_open_btc[$i]['type']=$order->type;
          $all_open_btc[$i]['price']=$order->price;
          $all_open_btc[$i]['amount']=$order->amount;
          $all_open_btc[$i]['pairsymbol']=$order->pairsymbol;

        }

      }
    } catch (Exception $e) {


    }

    try {
      $btcturk_open_orders=$btcturk_connect->getOpenOrders('LTCTRY');

      foreach( @$btcturk_open_orders as $order){
        if($order->id>0){
          $i++;
          $all_open_btc[$i]['id']=$order->id;
          $all_open_btc[$i]['datetime']=$order->datetime;
          $all_open_btc[$i]['type']=$order->type;
          $all_open_btc[$i]['price']=$order->price;
          $all_open_btc[$i]['amount']=$order->amount;
          $all_open_btc[$i]['pairsymbol']=$order->pairsymbol;

        }

      }
    } catch (Exception $e) {


    }

    try {
      $btcturk_open_orders=$btcturk_connect->getOpenOrders('XRPTRY');

      foreach( @$btcturk_open_orders as $order){
        if($order->id>0){

          $i++;
          $all_open_btc[$i]['id']=$order->id;
          $all_open_btc[$i]['datetime']=$order->datetime;
          $all_open_btc[$i]['type']=$order->type;
          $all_open_btc[$i]['price']=$order->price;
          $all_open_btc[$i]['amount']=$order->amount;
          $all_open_btc[$i]['pairsymbol']=$order->pairsymbol;
        }

      }
    } catch (Exception $e) {


    }

    try {
      $btcturk_open_orders=$btcturk_connect->getOpenOrders('XLMTRY');

      foreach( @$btcturk_open_orders as $order){
        if($order->id>0){

          $i++;

          $all_open_btc[$i]['id']=$order->id;
          $all_open_btc[$i]['datetime']=$order->datetime;
          $all_open_btc[$i]['type']=$order->type;
          $all_open_btc[$i]['price']=$order->price;
          $all_open_btc[$i]['amount']=$order->amount;
          $all_open_btc[$i]['pairsymbol']=$order->pairsymbol;
        }

      }
    } catch (Exception $e) {


    }

    try {
      $btcturk_open_orders=$btcturk_connect->getOpenOrders('USDTTRY');

      foreach( @$btcturk_open_orders as $order){
        if($order->id>0){

          $i++;
          $all_open_btc[$i]['id']=$order->id;
          $all_open_btc[$i]['datetime']=$order->datetime;
          $all_open_btc[$i]['type']=$order->type;
          $all_open_btc[$i]['price']=$order->price;
          $all_open_btc[$i]['amount']=$order->amount;
          $all_open_btc[$i]['pairsymbol']=$order->pairsymbol;
        }

      }
    } catch (Exception $e) {


    }

    return $all_open_btc;

}

  //Marketten çekilmiş açık siparişleri parçalar ve diziye atar
    function parse_open_orders_from_market_data($exchange,$datas){
      global $open_orders;
      global $wallet_params;


      if($exchange=='BtcTurk') {
        $i=-1;
      foreach( @$datas as $order){
        //print_r($order);
    //   echo($order['id']);
           if($order['id']>0) {
             $i++;
             $open_orders[$exchange][$i]['TxId']=$order['id'];
             $open_orders[$exchange][$i]['volume']=$order['amount'];
             $open_orders[$exchange][$i]['volume_act']=0;
             $open_orders[$exchange][$i]['status']='open';
             $open_orders[$exchange][$i]['fee']=0;
             $open_orders[$exchange][$i]['price']=$order['price'];
             $open_orders[$exchange][$i]['pair']=$order['pairsymbol'];
             $open_orders[$exchange][$i]['type']=strtolower(substr($order['type'], 0, -3));
             $open_orders[$exchange][$i]['order_type']='limit';
             $open_orders[$exchange][$i]['related_TxId']='';
             $open_orders[$exchange][$i]['datetime']=$order['datetime'];

             $pre_coin=substr($order['pairsymbol'], 0, -3);
             $last_coin=substr($order['pairsymbol'], -3);

             /*
             if($open_orders[$exchange][$i]['type']=="sell"){
                $wallet_params[$exchange][$pre_coin]['avaiable_balance']-=$order['amount'];
                $wallet_params[$exchange][$pre_coin]['reserved_balance']+=$order['amount'];
             } else {
                $wallet_params[$exchange][$last_coin]['avaiable_balance']-=$order['amount'];
                $wallet_params[$exchange][$last_coin]['reserved_balance']+=$order['amount'];
             }
             */


           }

        }
      } else if($exchange=='Kraken') {
          $i=-1;
		  //var_dump(@$datas->getOrders());
        foreach (@$datas->getOrders() as $order) {
                 $i++;
				 
            //echo $balanceModel->getAssetName() . ": " . $balanceModel->getBalance() . "\n";

              $OrderDetails =$order->getOrderDetails();
          //    $open_orders[$exchange][$order->getTxId()]['market']=$exchange;

              $open_orders[$exchange][$i]['TxId']=$i;
              $open_orders[$exchange][$i]['volume']=$order->getVol();
            $open_orders[$exchange][$i]['volume_act']=$order->getVolExec();
            $open_orders[$exchange][$i]['status']=$order->getStatus();
            $open_orders[$exchange][$i]['fee']=$order->getFee();
            $open_orders[$exchange][$i]['price']=$OrderDetails->getPrice();
            $open_orders[$exchange][$i]['pair']=$OrderDetails->getPair();
           $open_orders[$exchange][$i]['type']=$OrderDetails->getType();
           $open_orders[$exchange][$i]['order_type']=$OrderDetails->getOrderType();
           $open_orders[$exchange][$i]['related_TxId']='';
           $open_orders[$exchange][$i]['datetime']='';

      
		   
		    $pre_coin=substr($OrderDetails->getPair(), 0, -3);
             $last_coin=substr($OrderDetails->getPair(), -3);
			 
			 if($pre_coin=='XBT' || $pre_coin=='ETH' || $pre_coin=='LTC' || $pre_coin=='XRP' || $pre_coin=='XLM' )
				$pre_coin="X".$pre_coin;
			if($last_coin=="USD" || $last_coin=="EUR")
				$last_coin="Z".$last_coin;
			

		  // echo  $pre_coin . " --- ". $last_coin . " ..... ";

           if($open_orders[$exchange][$i]['type']=="sell"){
              $wallet_params[$exchange][$pre_coin]['avaiable_balance']-=($order->getVol-$order()->getVolExec());
              $wallet_params[$exchange][$pre_coin]['reserved_balance']+=($order->getVol-$order()->getVolExec());
           } else {
			 $wallet_params[$exchange][$last_coin]['avaiable_balance']-=($order->getVol()-$order->getVolExec())*$OrderDetails->getPrice();
              $wallet_params[$exchange][$last_coin]['reserved_balance']+=($order->getVol()-$order->getVolExec())*$OrderDetails->getPrice();
           }

			//echo $order->getVol() . " - " .$order->getVolExec()." aaa";
        }
      }
      /*else if($exchange=='Kraken') {
        $res=$datas['result'];
      foreach($res as $key => $data){
          $wallet_params[$exchange][$key]['whole_balance']=$data;
          $wallet_params[$exchange][$key]['avaiable_balance']=$data;
          $wallet_params[$exchange][$key]['reserved_balance']=$data;
          $wallet_params[$exchange][$key]['taker_fee']=0.0026;
          $wallet_params[$exchange][$key]['maker_fee']=0.0026;
        }
      }*/
    }


  //Dizilere atılmış güncel cüzdan bilgilerini veritabanında günceller
    function update_market_balances_on_db($exchange='', $currency=''){
      global $wallet_params;
      global $dbc;

      if($exchange=='')
        $arr=$wallet_params;
      else
        $arr=$wallet_params[$exchange];

        $q="";

        foreach($wallet_params as $key => $wallet_param)
        {
          if($exchange=='' || $key==$exchange)
          {
              foreach($wallet_param as $key2 => $cur_param)
              {
                  if($currency=='' || $key2==$currency){
                        $q .="UPDATE market_balances SET
                        whole_balance='" . $cur_param['whole_balance'] . "',
                        avaiable_balance='" . $cur_param['avaiable_balance'] . "',
                        reserved_balance='" . $cur_param['reserved_balance'] . "',
                        taker_fee='" . $cur_param['taker_fee']. "', maker_fee='" . $cur_param['maker_fee'] . "'
                        WHERE market='" . $key ."' AND (currency='" . $key2 . "' OR global_currency='" . $key2 . "');";
                  }
              }
          }
        }
        $r = @mysqli_multi_query ($dbc, $q); // Run the query.
      //  echo $q;
        $err = mysqli_error($dbc);
      //  while(mysqli_next_result($dbc)){;}

    }

function update_gain_control(){
  global $dbc;
  global $coin_totals;
  global $wallet_params;
  global $cur_rates;
  global $btcturk_prices;
  global $term;
  global $check_term;
/*
  $q="SELECT * FROM cur_rates ORDER BY atimestamp DESC LIMIT 1";
  $r = @mysqli_query ($dbc, $q); // Run the query.
    if($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
      $cur_rates['EURTRY']=$row['eur_tl'];
      $cur_rates['USDTRY']=$row['usd_tl'];
      $cur_rates['CURTIME']=$row['atimestamp'];
      $cur_rates['APISOURCE']=$row['api_source'];
    }
  echo "bakalım <br/>

  </br/>";

  var_dump($cur_rates);*/

 $dbhost = 'localhost';
           $dbuser = 'dynacycle';
           $dbpass = '6p?M01sc';
           $dbname = 'admin_dynacycle';
    
           
           $conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

  $qq="SELECT * FROM gain_control WHERE fld_active='". $term."'";
  $rq = @mysqli_query($conn, $qq);



  $i=0;
  $html='';
  while($row = mysqli_fetch_array ($rq, MYSQLI_ASSOC)) {
  //	while($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){

      $coin_totals[$i]['BTC']['whole_balance']=$row['btc_balance'];
      $coin_totals[$i]['BTC']['avaiable_balance']=$row['btc_balance'];
      $coin_totals[$i]['BTC']['reserved_balance']=0;
      $coin_totals[$i]['BTC']['price']=$row['btc_price'];
      $coin_totals[$i]['BTC']['entry']=$row['record_time'];;

      $coin_totals[$i]['ETH']['whole_balance']=$row['eth_balance'];
      $coin_totals[$i]['ETH']['avaiable_balance']=$row['eth_balance'];
      $coin_totals[$i]['ETH']['reserved_balance']=0;
      $coin_totals[$i]['ETH']['price']=$row['eth_price'];

      $coin_totals[$i]['LTC']['whole_balance']=$row['ltc_balance'];
      $coin_totals[$i]['LTC']['avaiable_balance']=$row['ltc_balance'];
      $coin_totals[$i]['LTC']['reserved_balance']=0;
      $coin_totals[$i]['LTC']['price']=$row['ltc_price'];

      $coin_totals[$i]['XRP']['whole_balance']=$row['xrp_balance'];
      $coin_totals[$i]['XRP']['avaiable_balance']=$row['xrp_balance'];
      $coin_totals[$i]['XRP']['reserved_balance']=0;
      $coin_totals[$i]['XRP']['price']=$row['xrp_price'];

      $coin_totals[$i]['XLM']['whole_balance']=$row['xlm_balance'];
      $coin_totals[$i]['XLM']['avaiable_balance']=$row['xlm_balance'];
      $coin_totals[$i]['XLM']['reserved_balance']=0;
      $coin_totals[$i]['XLM']['price']=$row['xlm_price'];
	  
	  
	   $coin_totals[$i]['DASH']['whole_balance']=$row['dash_balance'];
      $coin_totals[$i]['DASH']['avaiable_balance']=$row['dash_balance'];
      $coin_totals[$i]['DASH']['reserved_balance']=0;
      $coin_totals[$i]['DASH']['price']=$row['dash_price'];
	  
	     $coin_totals[$i]['XTZ']['whole_balance']=$row['xtz_balance'];
      $coin_totals[$i]['XTZ']['avaiable_balance']=$row['xtz_balance'];
      $coin_totals[$i]['XTZ']['reserved_balance']=0;
      $coin_totals[$i]['XTZ']['price']=$row['xtz_price'];


      $coin_totals[$i]['USDT']['whole_balance']=$row['usdt_balance'];
      $coin_totals[$i]['USDT']['avaiable_balance']=$row['usdt_balance'];
      $coin_totals[$i]['USDT']['reserved_balance']=0;
      $coin_totals[$i]['USDT']['price']=$row['usdt_price'];
      
      
      $coin_totals[$i]['ADA']['whole_balance']=$row['ada_balance'];
      $coin_totals[$i]['ADA']['avaiable_balance']=$row['ada_balance'];
      $coin_totals[$i]['ADA']['reserved_balance']=0;
      $coin_totals[$i]['ADA']['price']=$row['ada_price'];
      
      $coin_totals[$i]['DOT']['whole_balance']=$row['dot_balance'];
      $coin_totals[$i]['DOT']['avaiable_balance']=$row['dot_balance'];
      $coin_totals[$i]['DOT']['reserved_balance']=0;
      $coin_totals[$i]['DOT']['price']=$row['dot_price'];
      
      
      
      $coin_totals[$i]['ATOM']['whole_balance']=$row['atom_balance'];
      $coin_totals[$i]['ATOM']['avaiable_balance']=$row['atom_balance'];
      $coin_totals[$i]['ATOM']['reserved_balance']=0;
      $coin_totals[$i]['ATOM']['price']=$row['atom_price'];
      
      $coin_totals[$i]['LINK']['whole_balance']=$row['link_balance'];
      $coin_totals[$i]['LINK']['avaiable_balance']=$row['link_balance'];
      $coin_totals[$i]['LINK']['reserved_balance']=0;
      $coin_totals[$i]['LINK']['price']=$row['link_price'];
      
      $coin_totals[$i]['EOS']['whole_balance']=$row['eos_balance'];
      $coin_totals[$i]['EOS']['avaiable_balance']=$row['eos_balance'];
      $coin_totals[$i]['EOS']['reserved_balance']=0;
      $coin_totals[$i]['EOS']['price']=$row['eos_price'];
      
      
      

      $coin_totals[$i]['BTC']['whole_balance']=$row['btc_balance'];
      $coin_totals[$i]['BTC']['avaiable_balance']=$row['btc_balance'];
      $coin_totals[$i]['BTC']['reserved_balance']=0;
      $coin_totals[$i]['BTC']['price']=$row['btc_price'];



      $coin_totals[$i]['USD']['whole_balance']=$row['usd_cash'];
      $coin_totals[$i]['USD']['avaiable_balance']=$row['usd_cash'];
      $coin_totals[$i]['USD']['reserved_balance']=0;
      $coin_totals[$i]['USD']['price']=$row['usd_price'];
      $coin_totals[$i]['USD']['cash_eq']=$row['usd_cash_eq'];
      $coin_totals[$i]['USD']['coin_cash_eq']=$row['usd_coins_eq'];

      $coin_totals[$i]['EUR']['whole_balance']=$row['eur_cash'];
      $coin_totals[$i]['EUR']['avaiable_balance']=$row['eur_cash'];
      $coin_totals[$i]['EUR']['reserved_balance']=0;
      $coin_totals[$i]['EUR']['price']=$row['eur_price'];
      $coin_totals[$i]['EUR']['cash_eq']=$row['eur_cash_eq'];
      $coin_totals[$i]['EUR']['coin_cash_eq']=$row['eur_coins_eq'];

      $coin_totals[$i]['TRY']['whole_balance']=$row['try_cash'];
      $coin_totals[$i]['TRY']['avaiable_balance']=$row['try_cash'];
      $coin_totals[$i]['TRY']['reserved_balance']=0;
      $coin_totals[$i]['TRY']['price']=1;
      $coin_totals[$i]['TRY']['cash_eq']=$row['try_cash_eq'];
      $coin_totals[$i]['TRY']['coin_cash_eq']=$row['try_coins_eq'];



          $i++;
    }


if($check_term<=$term){


    $coin_totals[$i]['BTC']['whole_balance']=$wallet_params['Kraken']['XXBT']['whole_balance']+$wallet_params['BtcTurk']['BTC']['whole_balance'];
    $coin_totals[$i]['BTC']['avaiable_balance']=$wallet_params['Kraken']['XXBT']['avaiable_balance']+$wallet_params['BtcTurk']['BTC']['avaiable_balance'];
    $coin_totals[$i]['BTC']['reserved_balance']=$wallet_params['Kraken']['XXBT']['reserved_balance']+$wallet_params['BtcTurk']['BTC']['reserved_balance'];
    $coin_totals[$i]['BTC']['price']=$btcturk_prices['BTCTRY'];
    $coin_totals[$i]['BTC']['entry']=date("Y-m-d H:i:s");

    $coin_totals[$i]['ETH']['whole_balance']=$wallet_params['Kraken']['XETH']['whole_balance']+$wallet_params['BtcTurk']['ETH']['whole_balance'];
    $coin_totals[$i]['ETH']['avaiable_balance']=$wallet_params['Kraken']['XETH']['avaiable_balance']+$wallet_params['BtcTurk']['ETH']['avaiable_balance'];
    $coin_totals[$i]['ETH']['reserved_balance']=$wallet_params['Kraken']['XETH']['reserved_balance']+$wallet_params['BtcTurk']['ETH']['reserved_balance'];
    $coin_totals[$i]['ETH']['price']=$btcturk_prices['ETHTRY'];

    $coin_totals[$i]['LTC']['whole_balance']=$wallet_params['Kraken']['XLTC']['whole_balance']+$wallet_params['BtcTurk']['LTC']['whole_balance'];
    $coin_totals[$i]['LTC']['avaiable_balance']=$wallet_params['Kraken']['XLTC']['avaiable_balance']+$wallet_params['BtcTurk']['LTC']['avaiable_balance'];
    $coin_totals[$i]['LTC']['reserved_balance']=$wallet_params['Kraken']['XLTC']['reserved_balance']+$wallet_params['BtcTurk']['LTC']['reserved_balance'];
    $coin_totals[$i]['LTC']['price']=$btcturk_prices['LTCTRY'];

    $coin_totals[$i]['XRP']['whole_balance']=$wallet_params['Kraken']['XXRP']['whole_balance']+$wallet_params['BtcTurk']['XRP']['whole_balance'];
    $coin_totals[$i]['XRP']['avaiable_balance']=$wallet_params['Kraken']['XXRP']['avaiable_balance']+$wallet_params['BtcTurk']['XRP']['avaiable_balance'];
    $coin_totals[$i]['XRP']['reserved_balance']=$wallet_params['Kraken']['XXRP']['reserved_balance']+$wallet_params['BtcTurk']['XRP']['reserved_balance'];
    $coin_totals[$i]['XRP']['price']=$btcturk_prices['XRPTRY'];

    $coin_totals[$i]['XLM']['whole_balance']=$wallet_params['Kraken']['XXLM']['whole_balance']+$wallet_params['BtcTurk']['XLM']['whole_balance'];
    $coin_totals[$i]['XLM']['avaiable_balance']=$wallet_params['Kraken']['XXLM']['avaiable_balance']+$wallet_params['BtcTurk']['XLM']['avaiable_balance'];
    $coin_totals[$i]['XLM']['reserved_balance']=$wallet_params['Kraken']['XXLM']['reserved_balance']+$wallet_params['BtcTurk']['XLM']['reserved_balance'];
    $coin_totals[$i]['XLM']['price']=$btcturk_prices['XLMTRY'];
	
  $coin_totals[$i]['DASH']['whole_balance']=$wallet_params['Kraken']['DASH']['whole_balance']+$wallet_params['BtcTurk']['DASH']['whole_balance'];
    $coin_totals[$i]['DASH']['avaiable_balance']=$wallet_params['Kraken']['DASH']['avaiable_balance']+$wallet_params['BtcTurk']['DASH']['avaiable_balance'];
    $coin_totals[$i]['DASH']['reserved_balance']=$wallet_params['Kraken']['DASH']['reserved_balance']+$wallet_params['BtcTurk']['DASH']['reserved_balance'];
    $coin_totals[$i]['DASH']['price']=$btcturk_prices['DASHTRY'];
	
	 $coin_totals[$i]['XTZ']['whole_balance']=$wallet_params['Kraken']['XTZ']['whole_balance']+$wallet_params['BtcTurk']['XTZ']['whole_balance'];
    $coin_totals[$i]['XTZ']['avaiable_balance']=$wallet_params['Kraken']['XTZ']['avaiable_balance']+$wallet_params['BtcTurk']['XTZ']['avaiable_balance'];
    $coin_totals[$i]['XTZ']['reserved_balance']=$wallet_params['Kraken']['XTZ']['reserved_balance']+$wallet_params['BtcTurk']['XTZ']['reserved_balance'];
    $coin_totals[$i]['XTZ']['price']=$btcturk_prices['XTZTRY'];

    $coin_totals[$i]['ADA']['whole_balance']=$wallet_params['Kraken']['ADA']['whole_balance']+$wallet_params['BtcTurk']['ADA']['whole_balance'];
     $coin_totals[$i]['ADA']['avaiable_balance']=$wallet_params['Kraken']['ADA']['avaiable_balance']+$wallet_params['BtcTurk']['ADA']['avaiable_balance'];
     $coin_totals[$i]['ADA']['reserved_balance']=$wallet_params['Kraken']['ADA']['reserved_balance']+$wallet_params['BtcTurk']['ADA']['reserved_balance'];
     $coin_totals[$i]['ADA']['price']=$btcturk_prices['ADATRY'];
     
     $coin_totals[$i]['DOT']['whole_balance']=$wallet_params['Kraken']['DOT']['whole_balance']+$wallet_params['BtcTurk']['DOT']['whole_balance'];
      $coin_totals[$i]['DOT']['avaiable_balance']=$wallet_params['Kraken']['DOT']['avaiable_balance']+$wallet_params['BtcTurk']['DOT']['avaiable_balance'];
      $coin_totals[$i]['DOT']['reserved_balance']=$wallet_params['Kraken']['DOT']['reserved_balance']+$wallet_params['BtcTurk']['DOT']['reserved_balance'];
      $coin_totals[$i]['DOT']['price']=$btcturk_prices['DOTTRY'];

      $coin_totals[$i]['LINK']['whole_balance']=$wallet_params['Kraken']['LINK']['whole_balance']+$wallet_params['BtcTurk']['LINK']['whole_balance'];
       $coin_totals[$i]['LINK']['avaiable_balance']=$wallet_params['Kraken']['LINK']['avaiable_balance']+$wallet_params['BtcTurk']['LINK']['avaiable_balance'];
       $coin_totals[$i]['LINK']['reserved_balance']=$wallet_params['Kraken']['LINK']['reserved_balance']+$wallet_params['BtcTurk']['LINK']['reserved_balance'];
       $coin_totals[$i]['LINK']['price']=$btcturk_prices['LINKTRY'];

       $coin_totals[$i]['ATOM']['whole_balance']=$wallet_params['Kraken']['ATOM']['whole_balance']+$wallet_params['BtcTurk']['ATOM']['whole_balance'];
        $coin_totals[$i]['ATOM']['avaiable_balance']=$wallet_params['Kraken']['ATOM']['avaiable_balance']+$wallet_params['BtcTurk']['ATOM']['avaiable_balance'];
        $coin_totals[$i]['ATOM']['reserved_balance']=$wallet_params['Kraken']['ATOM']['reserved_balance']+$wallet_params['BtcTurk']['ATOM']['reserved_balance'];
        $coin_totals[$i]['ATOM']['price']=$btcturk_prices['ATOMTRY'];

        $coin_totals[$i]['EOS']['whole_balance']=$wallet_params['Kraken']['EOS']['whole_balance']+$wallet_params['BtcTurk']['EOS']['whole_balance'];
         $coin_totals[$i]['EOS']['avaiable_balance']=$wallet_params['Kraken']['EOS']['avaiable_balance']+$wallet_params['BtcTurk']['EOS']['avaiable_balance'];
         $coin_totals[$i]['EOS']['reserved_balance']=$wallet_params['Kraken']['EOS']['reserved_balance']+$wallet_params['BtcTurk']['EOS']['reserved_balance'];
         $coin_totals[$i]['EOS']['price']=$btcturk_prices['EOSTRY'];




    $coin_totals[$i]['USDT']['whole_balance']=$wallet_params['Kraken']['USDT']['whole_balance']+$wallet_params['BtcTurk']['USDT']['whole_balance'];
    $coin_totals[$i]['USDT']['avaiable_balance']=$wallet_params['Kraken']['USDT']['avaiable_balance']+$wallet_params['BtcTurk']['USDT']['avaiable_balance'];
    $coin_totals[$i]['USDT']['reserved_balance']=$wallet_params['Kraken']['USDT']['reserved_balance']+$wallet_params['BtcTurk']['USDT']['reserved_balance'];
    $coin_totals[$i]['USDT']['price']=$btcturk_prices['USDTTRY'];

    $coin_totals[$i]['USD']['whole_balance']=$wallet_params['Kraken']['ZUSD']['whole_balance'];
    $coin_totals[$i]['USD']['avaiable_balance']=$wallet_params['Kraken']['ZUSD']['avaiable_balance'];
    $coin_totals[$i]['USD']['reserved_balance']=$wallet_params['Kraken']['ZUSD']['reserved_balance'];
    $coin_totals[$i]['USD']['price']=$cur_rates['USDTRY'];
    $coin_totals[$i]['USD']['cash_eq']= $coin_totals[$i]['USDT']['whole_balance']+$wallet_params['Kraken']['ZUSD']['whole_balance']+($wallet_params['BtcTurk']['TRY']['whole_balance']/$cur_rates['USDTRY'])+($wallet_params['Kraken']['ZEUR']['whole_balance']*($cur_rates['EURTRY']/$cur_rates['USDTRY']));
  //  $coin_totals[$i]['USD']['coin']=$row['COINS_USD_EQ'];

    $coin_totals[$i]['EUR']['whole_balance']=$wallet_params['Kraken']['ZEUR']['whole_balance'];
    $coin_totals[$i]['EUR']['avaiable_balance']=$wallet_params['Kraken']['ZEUR']['avaiable_balance'];
    $coin_totals[$i]['EUR']['reserved_balance']=$wallet_params['Kraken']['ZEUR']['reserved_balance'];
    $coin_totals[$i]['EUR']['price']=$cur_rates['EURTRY'];
    $coin_totals[$i]['EUR']['cash_eq']=$wallet_params['Kraken']['ZEUR']['whole_balance']+($wallet_params['BtcTurk']['TRY']['whole_balance']/$cur_rates['EURTRY'])+
	($wallet_params['Kraken']['ZUSD']['whole_balance']*($cur_rates['USDTRY']/$cur_rates['EURTRY']))+($coin_totals[$i]['USDT']['whole_balance']*($cur_rates['USDTRY']/$cur_rates['EURTRY']));




    $coin_totals[$i]['TRY']['whole_balance']=$wallet_params['BtcTurk']['TRY']['whole_balance'];
    $coin_totals[$i]['TRY']['avaiable_balance']=$wallet_params['BtcTurk']['TRY']['avaiable_balance'];
    $coin_totals[$i]['TRY']['reserved_balance']=$wallet_params['BtcTurk']['TRY']['reserved_balance'];
    $coin_totals[$i]['TRY']['price']=1;
    $coin_totals[$i]['TRY']['cash_eq']=$coin_totals[$i]['TRY']['whole_balance'] +
      ($coin_totals[$i]['EUR']['whole_balance'] * $cur_rates['EURTRY']) +
      ($coin_totals[$i]['USD']['whole_balance'] * $cur_rates['USDTRY'])+
	   ($coin_totals[$i]['USDT']['whole_balance'] * $cur_rates['USDTRY']);

    $coin_totals[$i]['TRY']['coin_cash_eq']=$coin_totals[$i]['BTC']['whole_balance'] * $btcturk_prices['BTCTRY'] +
      $coin_totals[$i]['ETH']['whole_balance'] * $btcturk_prices['ETHTRY'] +
      $coin_totals[$i]['LTC']['whole_balance'] * $btcturk_prices['LTCTRY'] +
      $coin_totals[$i]['XRP']['whole_balance'] * $btcturk_prices['XRPTRY'] +
      $coin_totals[$i]['XLM']['whole_balance'] * $btcturk_prices['XLMTRY'] +
	    $coin_totals[$i]['DASH']['whole_balance'] * $btcturk_prices['DASHTRY'] +
		  $coin_totals[$i]['XTZ']['whole_balance'] * $btcturk_prices['XTZTRY'] +
		  $coin_totals[$i]['ADA']['whole_balance'] * $btcturk_prices['ADATRY']+
		  $coin_totals[$i]['DOT']['whole_balance'] * $btcturk_prices['DOTTRY']+
		  $coin_totals[$i]['LINK']['whole_balance'] * $btcturk_prices['LINKTRY']+
		  $coin_totals[$i]['ATOM']['whole_balance'] * $btcturk_prices['ATOMTRY']+
		  $coin_totals[$i]['EOS']['whole_balance'] * $btcturk_prices['EOSTRY'];

    $coin_totals[$i]['EUR']['coin_cash_eq']=$coin_totals[$i]['TRY']['coin'] / $cur_rates['EURTRY'];
    $coin_totals[$i]['USD']['coin_cash_eq']=$coin_totals[$i]['TRY']['coin'] / $cur_rates['USDTRY'];

    //print_r($coin_totals);


  $q ="INSERT INTO gain_control (
    eur_cash,
 usd_cash,
 try_cash,
 eur_price,
 usd_price,
 eur_cash_eq,
 usd_cash_eq,
 try_cash_eq,
 btc_balance,
 eth_balance,
 ltc_balance,
 xrp_balance,
 xlm_balance,
dash_balance,
xtz_balance,
atom_balance,
eos_balance,
link_balance,
ada_balance,
dot_balance,
 usdt_balance,
 btc_price,
 eth_price,
 ltc_price,
 xrp_price,
 xlm_price,
dash_price,
xtz_price,
atom_price,
eos_price,
link_price,
ada_price,
dot_price,
 usdt_price,
 eur_coins_eq,
 usd_coins_eq,
 try_coins_eq,
 record_time,
 fld_active,
 m1_btc,
m1_eth,
m1_ltc,
m1_xrp,
m1_xlm,
m1_dash,
m1_xtz,
m1_atom,
m1_eos,
m1_link,
m1_ada,
m1_dot,
m1_usdt,
m2_btc,
m2_eth,
m2_ltc,
m2_xrp,
m2_xlm,
m2_dash,
m2_xtz,
m2_atom,
m2_eos,
m2_link,
m2_ada,
m2_dot,
m2_usdt
 ) VALUES (

 '".   round($coin_totals[$i]['EUR']['whole_balance'],4) . "',
 '".   round($coin_totals[$i]['USD']['whole_balance'],4) . "',
 '".   round($coin_totals[$i]['TRY']['whole_balance'],4) . "',
 '".   round($coin_totals[$i]['EUR']['price'],6) . "',
 '".   round($coin_totals[$i]['USD']['price'],6) . "',
 '".  round($coin_totals[$i]['EUR']['cash_eq'],2) . "',
 '".  round($coin_totals[$i]['USD']['cash_eq'],2) . "',
 '".  round($coin_totals[$i]['TRY']['cash_eq'],2) . "',
 '".  round($coin_totals[$i]['BTC']['whole_balance'],8) . "',
 '".  round($coin_totals[$i]['ETH']['whole_balance'],8) . "',
 '".  round($coin_totals[$i]['LTC']['whole_balance'],8) . "',
 '".  round($coin_totals[$i]['XRP']['whole_balance'],8) . "',
 '".  round($coin_totals[$i]['XLM']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['DASH']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['XTZ']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['ATOM']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['EOS']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['LINK']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['ADA']['whole_balance'],8) . "',
'".  round($coin_totals[$i]['DOT']['whole_balance'],8) . "',
 '".  round($coin_totals[$i]['USDT']['whole_balance'],8) . "',
 '".  round($coin_totals[$i]['BTC']['price'],2) . "',
 '".  round($coin_totals[$i]['ETH']['price'],2) . "',
 '".  round($coin_totals[$i]['LTC']['price'],2) . "',
 '".  round($coin_totals[$i]['XRP']['price'],4) . "',
 '".  round($coin_totals[$i]['XLM']['price'],4) . "',
'".  round($coin_totals[$i]['DASH']['price'],4) . "',
'".  round($coin_totals[$i]['XTZ']['price'],4) . "',
'".  round($coin_totals[$i]['ATOM']['price'],4) . "',
'".  round($coin_totals[$i]['EOS']['price'],4) . "',
'".  round($coin_totals[$i]['LINK']['price'],4) . "',
'".  round($coin_totals[$i]['ADA']['price'],4) . "',
'".  round($coin_totals[$i]['DOT']['price'],4) . "',
 '".  round($coin_totals[$i]['USDT']['price'],8) . "',
 '".  round($coin_totals[$i]['EUR']['coin_cash_eq'],2) . "',
 '".  round($coin_totals[$i]['USD']['coin_cash_eq'],2) . "',
 '".  round($coin_totals[$i]['TRY']['coin_cash_eq'],2) . "',
 '".  date("Y-m-d H:i:s") . "',
 '".  $term . "',
 
 '".  round($wallet_params['Kraken']['XXBT']['whole_balance'],8) . "',
 '".  round($wallet_params['Kraken']['XETH']['whole_balance'],8) . "',
 '".  round($wallet_params['Kraken']['XLTC']['whole_balance'],8) . "',
 '".  round($wallet_params['Kraken']['XXRP']['whole_balance'],8) . "',
 '".  round($wallet_params['Kraken']['XXLM']['whole_balance'],8) . "',
  '".  round($wallet_params['Kraken']['DASH']['whole_balance'],8) . "',
  '".  round($wallet_params['Kraken']['XTZ']['whole_balance'],8) . "',
'".  round($wallet_params['Kraken']['ATOM']['whole_balance'],8) . "',
'".  round($wallet_params['Kraken']['EOS']['whole_balance'],8) . "',
'".  round($wallet_params['Kraken']['LINK']['whole_balance'],8) . "',
'".  round($wallet_params['Kraken']['ADA']['whole_balance'],8) . "',
'".  round($wallet_params['Kraken']['DOT']['whole_balance'],8) . "',
 '".  round($wallet_params['Kraken']['USDT']['whole_balance'],2) . "',
 '".  round($wallet_params['BtcTurk']['BTC']['whole_balance'],8) . "',
 '".  round($wallet_params['BtcTurk']['ETH']['whole_balance'],8) . "',
 '".  round($wallet_params['BtcTurk']['LTC']['whole_balance'],8) . "',
 '".  round($wallet_params['BtcTurk']['XRP']['whole_balance'],8) . "',
 '".  round($wallet_params['BtcTurk']['XLM']['whole_balance'],8) . "',
  '".  round($wallet_params['BtcTurk']['DASH']['whole_balance'],8) . "',
'".  round($wallet_params['BtcTurk']['XTZ']['whole_balance'],8) . "',
'".  round($wallet_params['BtcTurk']['ATOM']['whole_balance'],8) . "',
'".  round($wallet_params['BtcTurk']['EOS']['whole_balance'],8) . "',
'".  round($wallet_params['BtcTurk']['LINK']['whole_balance'],8) . "',
'".  round($wallet_params['BtcTurk']['ADA']['whole_balance'],8) . "',
'".  round($wallet_params['BtcTurk']['DOT']['whole_balance'],8) . "',
 '".  round($wallet_params['BtcTurk']['USDT']['whole_balance'],2) . "'
 )";
// echo $q;
    $r = @mysqli_query ($conn, $q); // Run the query.



}


row_maker();

}


function row_maker(){
  
  $show_btc=1;
  $show_eth=1;
  $show_ltc=0;
  $show_xrp=0;
  $show_xlm=0;
  $show_dash=0;
  $show_xtz=0;
  $show_atom=0;
  $show_eos=0;
  $show_link=0;
  $show_ada=1;
  $show_dot=1;
	
	$tt ='<div class="Row ' . $rowi_class . '">
            <div class="Cell">
                <p><b>Zaman</b></p>
            </div>

            <div class="Cell">
                  <p><b>EUR</b> </p>
            </div>
			      <div class="Cell">
                  <p><b>USD</b> </p>
            </div>
			      <div class="Cell">
                  <p><b>USDT</b> </p>
            </div>
			      <div class="Cell">
                  <p><b>TRY</b> </p>
            </div>
			
           
		      <div class="Cell">
                  <p><b>EUR eq</b> </p>
            </div>
			      <div class="Cell">
                  <p><b>USD eq</b> </p>
            </div>
			 
			<div class="Cell">
                  <p><b>TRY eq</b> </p>
            </div>
			';
      if($show_btc==1)
      $tt .='<div class="Cell">
                  <p><b>BTC</b> </p>
            </div>';
        if($show_eth==1)
			$tt .='<div class="Cell">
                  <p><b>ETH</b> </p>
            </div>';
            
          if($show_ltc==1)    
			$tt .='<div class="Cell">
                  <p><b>LTC</b> </p>
            </div>';
            
        if($show_xrp==1)      
			$tt .='
			<div class="Cell">
                  <p><b>XRP</b> </p>
            </div>';
    if($show_xlm==1)
			$tt .='
			<div class="Cell">
                  <p><b>XLM</b> </p>
            </div>';
        if($show_dash==1)      
			$tt .='
			<div class="Cell">
                  <p><b>DASH</b> </p>
            </div>';
      if($show_xtz==1)    
			$tt .='
			<div class="Cell">
                  <p><b>XTZ</b> </p>
            </div>';
            
        if($show_atom==1)      
			$tt .='
            <div class="Cell">
                        <p><b>ATOM</b> </p>
                  </div>';
                  
            if($show_eos==1)        
      			$tt .='
                  <div class="Cell">
                              <p><b>EOS</b> </p>
                        </div>';
                          if($show_linkc==1)
            			$tt .='
                        <div class="Cell">
                                    <p><b>LINK</b> </p>
                              </div>';
                                if($show_ada==1)
                  			$tt .='
                              <div class="Cell">
                                          <p><b>ADA</b> </p>
                                    </div>';
                                      if($show_dot==1)
                        			$tt .='
                                    <div class="Cell">
                                                <p><b>DOT</b> </p>
                                          </div>';
                                    
      $tt .='
			<div class="Cell">
                  <p><b>KRİPTO</b> </p>
            </div>
			
			<div class="Cell">
                  <p><b>KRİPTO EQ</b> </p>
            </div>
       


        </div>';
 echo $tt;
 
global $coin_totals;
$sayi=count($coin_totals)-1;
    $html="";
	$buuu=0;
      foreach($coin_totals as $key => $cur_param)
      {
$buuu++;
        $rowi_class="flo";
      if($key%2==0)
        $rowi_class="gho";

        $alt_key=$key-1;
        $eur_eq_diff_0=0;
        $eur_eq_diff_1=0;
        $eur_eq_diff_0_text='<br/>';
        $eur_eq_diff_1_text='<br/>';
        $usd_eq_diff_0=0;
        $usd_eq_diff_1=0;
        $usd_eq_diff_0_text='<br/>';
        $usd_eq_diff_1_text='<br/>';
        $try_eq_diff_0=0;
        $try_eq_diff_1=0;
        $try_eq_diff_0_text='<br/>';
        $try_eq_diff_1_text='<br/>';

        $btc_diff_0=0;
        $btc_diff_0_text='<br/>';
        $btc_diff_1=0;
        $btc_diff_1_text='<br/>';


        $eth_diff_0=0;
        $eth_diff_0_text='<br/>';
        $eth_diff_1=0;
        $eth_diff_1_text='<br/>';

        $ltc_diff_0=0;
        $ltc_diff_0_text='<br/>';
        $ltc_diff_1=0;
        $ltc_diff_1_text='<br/>';

        $xrp_diff_0=0;
        $xrp_diff_0_text='<br/>';
        $xrp_diff_1=0;
        $xrp_diff_1_text='<br/>';

        $xlm_diff_0=0;
        $xlm_diff_0_text='<br/>';
        $xlm_diff_1=0;
        $xlm_diff_1_text='<br/>';
		
		       $dash_diff_0=0;
        $dash_diff_0_text='<br/>';
        $dash_diff_1=0;
        $dash_diff_1_text='<br/>';
		
		       $xtz_diff_0=0;
        $xtz_diff_0_text='<br/>';
        $xtz_diff_1=0;
        $xtz_diff_1_text='<br/>';
        
        
        $atom_diff_0=0;
        $atom_diff_0_text='<br/>';
        $atom_diff_1=0;
        $atom_diff_1_text='<br/>';

        $eos_diff_0=0;
        $eos_diff_0_text='<br/>';
        $eos_diff_1=0;
        $eos_diff_1_text='<br/>';

        $link_diff_0=0;
        $link_diff_0_text='<br/>';
        $link_diff_1=0;
        $link_diff_1_text='<br/>';

        $ada_diff_0=0;
        $ada_diff_0_text='<br/>';
        $ada_diff_1=0;
        $ada_diff_1_text='<br/>';

        $dot_diff_0=0;
        $dot_diff_0_text='<br/>';
        $dot_diff_1=0;
        $dot_diff_1_text='<br/>';

        $usdt_diff_0=0;
        $usdt_diff_0_text='<br/>';
        $usdt_diff_1=0;
        $usdt_diff_1_text='<br/>';

        $coin_cash_diff_0=0;
        $coin_cash_diff_0_text='<br/>';
        $coin_cash_diff_1=0;
        $coin_cash_diff_1_text='<br/>';

        $total_cash_diff_0=0;
        $total_cash_diff_0_text='<br/>';
        $total_cash_diff_1=0;
        $total_cash_diff_1_text='<br/>';


        if($key>0) {

          $eur_eq_diff_0=$coin_totals[$key]['EUR']['cash_eq']-$coin_totals[$alt_key]['EUR']['cash_eq'];
          if($eur_eq_diff_0!=0)
            $eur_eq_diff_0_text='<br/>' . print_ytl($eur_eq_diff_0,2,'') . " | " . print_ytl(($eur_eq_diff_0 / $coin_totals[$alt_key]['EUR']['cash_eq'])*100,2,'%');

          $usd_eq_diff_0=$coin_totals[$key]['USD']['cash_eq']-$coin_totals[$alt_key]['USD']['cash_eq'];
          if($usd_eq_diff_0!=0)
            $usd_eq_diff_0_text='<br/>' . print_ytl($usd_eq_diff_0,2,''). " | " . print_ytl(($usd_eq_diff_0 / $coin_totals[$alt_key]['USD']['cash_eq'])*100,2,'%');

          $try_eq_diff_0=$coin_totals[$key]['TRY']['cash_eq']-$coin_totals[$alt_key]['TRY']['cash_eq'];
          if($try_eq_diff_0!=0)
            $try_eq_diff_0_text='<br/>' . print_ytl($try_eq_diff_0,2,''). " | " . print_ytl(($try_eq_diff_0 / $coin_totals[$alt_key]['TRY']['cash_eq'])*100,2,'%');


      
          $btc_diff_0=$coin_totals[$key]['BTC']['whole_balance']-$coin_totals[$alt_key]['BTC']['whole_balance'];
          if($btc_diff_0!=0)
            $btc_diff_0_text='<br/>' . print_ytl($btc_diff_0,8,'');

            $eth_diff_0=$coin_totals[$key]['ETH']['whole_balance']-$coin_totals[$alt_key]['ETH']['whole_balance'];
            if($eth_diff_0!=0)
              $eth_diff_0_text='<br/>' . print_ytl($eth_diff_0,8,'');

              $ltc_diff_0=$coin_totals[$key]['LTC']['whole_balance']-$coin_totals[$alt_key]['LTC']['whole_balance'];
              if($ltc_diff_0!=0)
                $ltc_diff_0_text='<br/>' . print_ytl($ltc_diff_0,8,'');

                $xrp_diff_0=$coin_totals[$key]['XRP']['whole_balance']-$coin_totals[$alt_key]['XRP']['whole_balance'];
                if($xrp_diff_0!=0)
                  $xrp_diff_0_text='<br/>' . print_ytl($xrp_diff_0,8,'');

                  $xlm_diff_0=$coin_totals[$key]['XLM']['whole_balance']-$coin_totals[$alt_key]['XLM']['whole_balance'];
                  if($xlm_diff_0!=0)
                    $xlm_diff_0_text='<br/>' . print_ytl($xlm_diff_0,8,'');
				
				      $dash_diff_0=$coin_totals[$key]['DASH']['whole_balance']-$coin_totals[$alt_key]['DASH']['whole_balance'];
                  if($dash_diff_0!=0)
                    $dash_diff_0_text='<br/>' . print_ytl($dash_diff_0,8,'');
				
$xtz_diff_0=$coin_totals[$key]['XTZ']['whole_balance']-$coin_totals[$alt_key]['XTZ']['whole_balance'];
if($xtz_diff_0!=0)
  $xtz_diff_0_text='<br/>' . print_ytl($xtz_diff_0,8,'');
  
  $atom_diff_0=$coin_totals[$key]['ATOM']['whole_balance']-$coin_totals[$alt_key]['ATOM']['whole_balance'];
  if($atom_diff_0!=0)
    $atom_diff_0_text='<br/>' . print_ytl($atom_diff_0,8,'');
    
    $eos_diff_0=$coin_totals[$key]['EOS']['whole_balance']-$coin_totals[$alt_key]['EOS']['whole_balance'];
    if($eos_diff_0!=0)
      $eos_diff_0_text='<br/>' . print_ytl($eos_diff_0,8,'');
      
      $link_diff_0=$coin_totals[$key]['LINK']['whole_balance']-$coin_totals[$alt_key]['LINK']['whole_balance'];
      if($link_diff_0!=0)
        $link_diff_0_text='<br/>' . print_ytl($link_diff_0,8,'');
        
        $ada_diff_0=$coin_totals[$key]['ADA']['whole_balance']-$coin_totals[$alt_key]['ADA']['whole_balance'];
        if($ada_diff_0!=0)
          $ada_diff_0_text='<br/>' . print_ytl($ada_diff_0,8,'');
          
          $dot_diff_0=$coin_totals[$key]['DOT']['whole_balance']-$coin_totals[$alt_key]['DOT']['whole_balance'];
          if($dot_diff_0!=0)
            $dot_diff_0_text='<br/>' . print_ytl($dot_diff_0,8,'');

                    $usdt_diff_0=$coin_totals[$key]['USDT']['whole_balance']-$coin_totals[$alt_key]['USDT']['whole_balance'];
                    if($usdt_diff_0!=0)
                      $usdt_diff_0_text='<br/>' . print_ytl($usdt_diff_0,8,'');

                      $coin_cash_diff_0=$coin_totals[$key]['TRY']['coin_cash_eq']-$coin_totals[$alt_key]['TRY']['coin_cash_eq'];
                      if($coin_cash_diff_0!=0)
                        $coin_cash_diff_0_text='<br/>' . print_ytl($coin_cash_diff_0,2,''). ' | '. print_ytl(($coin_cash_diff_0 / $coin_totals[$alt_key]['TRY']['coin_cash_eq'])*100,2,'%');

                        $total_cash_diff_0=$coin_totals[$key]['TRY']['cash_eq']+$coin_totals[$key]['TRY']['coin_cash_eq']-$coin_totals[$alt_key]['TRY']['cash_eq']-$coin_totals[$alt_key]['TRY']['coin_cash_eq'];
                        if($total_cash_diff_0!=0)
                          $total_cash_diff_0_text='<br/>' . print_ytl($total_cash_diff_0,2,''). ' | '. print_ytl(($total_cash_diff_0 / ($coin_totals[$alt_key]['TRY']['cash_eq']+$coin_totals[$alt_key]['TRY']['coin_cash_eq']))*100,2,'%');

          }

        if($key>1) {

          $eur_eq_diff_1=$coin_totals[$key]['EUR']['cash_eq']-$coin_totals[0]['EUR']['cash_eq'];
          if($eur_eq_diff_1!=0)
            $eur_eq_diff_1_text='<br/>' . print_ytl($eur_eq_diff_1,2,''). " | " . print_ytl(($eur_eq_diff_1 / $coin_totals[0]['EUR']['cash_eq'])*100,2,'%');

            $usd_eq_diff_1=$coin_totals[$key]['USD']['cash_eq']-$coin_totals[0]['USD']['cash_eq'];
            if($usd_eq_diff_1!=0)
              $usd_eq_diff_1_text='<br/>' . print_ytl($usd_eq_diff_1,2,''). " | " . print_ytl(($usd_eq_diff_1 / $coin_totals[0]['USD']['cash_eq'])*100,2,'%');

            $try_eq_diff_1=$coin_totals[$key]['TRY']['cash_eq']-$coin_totals[0]['TRY']['cash_eq'];
            if($try_eq_diff_1!=0)
              $try_eq_diff_1_text='<br/>' . print_ytl($try_eq_diff_1,2,''). " | " . print_ytl(($try_eq_diff_1 / $coin_totals[0]['TRY']['cash_eq'])*100,2,'%');

              $btc_diff_1=$coin_totals[$key]['BTC']['whole_balance']-$coin_totals[0]['BTC']['whole_balance'];
              if($btc_diff_1!=0)
                $btc_diff_1_text='<br/>' . print_ytl($btc_diff_1,8,'');


                $eth_diff_1=$coin_totals[$key]['ETH']['whole_balance']-$coin_totals[0]['ETH']['whole_balance'];
                if($eth_diff_1!=0)
                  $eth_diff_1_text='<br/>' . print_ytl($eth_diff_1,8,'');

                  $ltc_diff_1=$coin_totals[$key]['LTC']['whole_balance']-$coin_totals[0]['LTC']['whole_balance'];
                  if($ltc_diff_1!=0)
                    $ltc_diff_1_text='<br/>' . print_ytl($ltc_diff_1,8,'');

                    $xrp_diff_1=$coin_totals[$key]['XRP']['whole_balance']-$coin_totals[0]['XRP']['whole_balance'];
                    if($xrp_diff_1!=0)
                      $xrp_diff_1_text='<br/>' . print_ytl($xrp_diff_1,8,'');

                      $xlm_diff_1=$coin_totals[$key]['XLM']['whole_balance']-$coin_totals[0]['XLM']['whole_balance'];
                      if($xlm_diff_1!=0)
                        $xlm_diff_1_text='<br/>' . print_ytl($xlm_diff_1,8,'');


 $dash_diff_1=$coin_totals[$key]['DASH']['whole_balance']-$coin_totals[0]['DASH']['whole_balance'];
                      if($dash_diff_1!=0)
                        $dash_diff_1_text='<br/>' . print_ytl($dash_diff_1,8,'');
					
$xtz_diff_1=$coin_totals[$key]['XTZ']['whole_balance']-$coin_totals[0]['XTZ']['whole_balance'];
if($xtz_diff_1!=0)
    $xtz_diff_1_text='<br/>' . print_ytl($xtz_diff_1,8,'');
    
    
    $atom_diff_1=$coin_totals[$key]['ATOM']['whole_balance']-$coin_totals[0]['ATOM']['whole_balance'];
    if($atom_diff_1!=0)
        $atom_diff_1_text='<br/>' . print_ytl($atom_diff_1,8,'');
        
        
        $eos_diff_1=$coin_totals[$key]['ESO']['whole_balance']-$coin_totals[0]['EOS']['whole_balance'];
        if($eos_diff_1!=0)
            $eos_diff_1_text='<br/>' . print_ytl($eos_diff_1,8,'');
            
            
            $link_diff_1=$coin_totals[$key]['LINK']['whole_balance']-$coin_totals[0]['LINK']['whole_balance'];
            if($link_diff_1!=0)
                $link_diff_1_text='<br/>' . print_ytl($link_diff_1,8,'');
                
                
                $ada_diff_1=$coin_totals[$key]['ADA']['whole_balance']-$coin_totals[0]['ADA']['whole_balance'];
                if($ada_diff_1!=0)
                    $ada_diff_1_text='<br/>' . print_ytl($ada_diff_1,8,'');
                    
                    
                    $dot_diff_1=$coin_totals[$key]['DOT']['whole_balance']-$coin_totals[0]['DOT']['whole_balance'];
                    if($dot_diff_1!=0)
                        $dot_diff_1_text='<br/>' . print_ytl($dot_diff_1,8,'');
                        
                        
					
					
                        $usdt_diff_1=$coin_totals[$key]['USDT']['whole_balance']-$coin_totals[0]['USDT']['whole_balance'];
                        if($usdt_diff_1!=0)
                          $usdt_diff_1_text='<br/>' . print_ytl($usdt_diff_1,8,'');

                    
                              $coin_cash_diff_1=$coin_totals[$key]['TRY']['coin_cash_eq']-$coin_totals[0]['TRY']['coin_cash_eq'];
                              if($coin_cash_diff_1!=0)
                                $coin_cash_diff_1_text='<br/>' . print_ytl($coin_cash_diff_1,2,''). ' | '. print_ytl(($coin_cash_diff_1 / $coin_totals[0]['TRY']['coin_cash_eq'])*100,2,'%');

                                $total_cash_diff_1=$coin_totals[$key]['TRY']['cash_eq']+$coin_totals[$key]['TRY']['coin_cash_eq']-$coin_totals[0]['TRY']['cash_eq']-$coin_totals[0]['TRY']['coin_cash_eq'];
                                if($total_cash_diff_1!=0)
                                  $total_cash_diff_1_text='<br/>' . print_ytl($total_cash_diff_1,2,''). ' | '. print_ytl(($total_cash_diff_1 / ($coin_totals[0]['TRY']['cash_eq']+$coin_totals[0]['TRY']['coin_cash_eq']))*100,2,'%');


        }



        $html .='<div class="Row ' . $rowi_class . '">
            <div class="Cell">
                <p><b>' . $cur_param['BTC']['entry'] . '</b></p>
            </div>

            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['EUR']['whole_balance'],2,'')  . '</b><br/>' . print_ytl($cur_param['EUR']['reserved_balance'],2) . '<br/><br/>Fyt: '. print_ytl($cur_param['EUR']['price'],2,'').  '</p>
            </div>
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['USD']['whole_balance'] ,2,'') . '</b><br/>' . print_ytl($cur_param['USD']['reserved_balance'],2) . '<br/><br/>Fyt: '. print_ytl($cur_param['USD']['price'],2,'').  '</p>
            </div>
			  <div class="Cell">
                  <p><b>' . print_ytl($cur_param['USDT']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['USDT']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['USDT']['price'],2,''). $usdt_diff_0_text . $usdt_diff_1_text .  '</p>
            </div>
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['TRY']['whole_balance'] ,2,'') . '<br/>' . print_ytl($cur_param['TRY']['reserved_balance'],2) . '<br/></b></p>
            </div>
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['EUR']['cash_eq'] ,2,'') . '</b>' . $eur_eq_diff_0_text . '' . $eur_eq_diff_1_text . '</p>
            </div>
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['USD']['cash_eq'] ,2,'') . '</b>' . $usd_eq_diff_0_text . '' . $usd_eq_diff_1_text . '</p>
            </div>
            <div class="Cell" style="width:120px;">
                  <p><b>' . print_ytl($cur_param['TRY']['cash_eq'],2,'')  . '</b>' . $try_eq_diff_0_text . '' . $try_eq_diff_1_text . '</p>
            </div>';
            
            if($show_btc==1)
            $html .='<div class="Cell">
                  <p><b>' . print_ytl($cur_param['BTC']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['BTC']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['BTC']['price'],2,''). $btc_diff_0_text . $btc_diff_1_text .  '</p>
            </div>';
            
            if($show_eth==1)
            $html .='
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['ETH']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['ETH']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['ETH']['price'],2,''). $eth_diff_0_text . $eth_diff_1_text .  '</p>
            </div>';
            
            if($show_ltc==1)
            $html .='
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['LTC']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['LTC']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['LTC']['price'],2,''). $ltc_diff_0_text . $ltc_diff_1_text .  '</p>
            </div>';
            
            if($show_xrp==1)
            $html .='
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['XRP']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['XRP']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['XRP']['price'],2,''). $xrp_diff_0_text . $xrp_diff_1_text .  '</p>
            </div>';
            
            if($show_xlm==1)
            $html .='
            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['XLM']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['XLM']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['XLM']['price'],2,''). $xlm_diff_0_text . $xlm_diff_1_text .  '</p>
            </div>';
            
            if($show_dash==1)
            $html .='
			     <div class="Cell">
                  <p><b>' . print_ytl($cur_param['DASH']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['DASH']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['DASH']['price'],2,''). $dash_diff_0_text . $dash_diff_1_text .  '</p>
            </div>';
            
            if($show_xtz==1)
            $html .='
          <div class="Cell">
                  <p><b>' . print_ytl($cur_param['XTZ']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['XTZ']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['XTZ']['price'],2,''). $xtz_diff_0_text . $xtz_diff_1_text .  '</p>
            </div>';
            
            if($show_atom==1)
            $html .='
            
                <div class="Cell">
                        <p><b>' . print_ytl($cur_param['ATOM']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['ATOM']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['ATOM']['price'],2,''). $xtz_diff_0_text . $xtz_diff_1_text .  '</p>
                  </div>';
                  
                  if($show_eos==1)
                  $html .='
                      <div class="Cell">
                              <p><b>' . print_ytl($cur_param['EOS']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['EOS']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['EOS']['price'],2,''). $xtz_diff_0_text . $xtz_diff_1_text .  '</p>
                        </div>';
                        
                        if($show_link==1)
                        $html .='
                            <div class="Cell">
                                    <p><b>' . print_ytl($cur_param['LINK']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['LINK']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['LINK']['price'],2,''). $xtz_diff_0_text . $xtz_diff_1_text .  '</p>
                              </div>';
                              
                              if($show_ada==1)
                              $html .='
                          
                                  <div class="Cell">
                                          <p><b>' . print_ytl($cur_param['ADA']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['ADA']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['ADA']['price'],2,''). $xtz_diff_0_text . $xtz_diff_1_text .  '</p>
                                    </div>';
                                    
                                    if($show_dot==1)
                                    $html .='
                                        <div class="Cell">
                                                <p><b>' . print_ytl($cur_param['DOT']['whole_balance'],8,'')  . '</b><br/>' . $cur_param['DOT']['reserved_balance'] . '<br/>Fyt: '. print_ytl($cur_param['DOT']['price'],2,''). $xtz_diff_0_text . $xtz_diff_1_text .  '</p>
                                          </div>';

                    $html .='<div class="Cell">
                  <p><b>' . print_ytl($cur_param['TRY']['coin_cash_eq'],2,'')  . '</b>' .  $coin_cash_diff_0_text . $coin_cash_diff_1_text .  '</p>
            </div>

            <div class="Cell">
                  <p><b>' . print_ytl($cur_param['TRY']['coin_cash_eq']+$cur_param['TRY']['cash_eq'],2,'')  . '</b>' .  $total_cash_diff_0_text . $total_cash_diff_1_text .  '</p>
            </div>

        </div>';


      }
   echo $html;
   
   
 echo $tt;
   
//print_r($coin_totals);


}
//veritabanından cüzdan bilgilerini çeker
function get_balances_from_db($exchange='', $currency=''){
  global $wallet_params;
  global $dbc;


  $wallet_params[$exchange][$currency]['whole_balance']=0;
  $wallet_params[$exchange][$currency]['avaiable_balance']=0;
  $wallet_params[$exchange][$currency]['reserved_balance']=0;
  $wallet_params[$exchange][$currency]['taker_fee']=0;
  $wallet_params[$exchange][$currency]['maker_fee']=0;
  $wallet_params[$exchange][$currency]['act_balance']=0;

  $q_ek=" WHERE 1=1";
  if($exchange!="")
    $q_ek .=" AND market='" . $exchange . "'";
  if($currency!="")
      $q_ek .=" AND (currency='" . $currency . "' OR global_currency='" . $currency . "' )";
  $q="SELECT * FROM market_balances" . $q_ek;
  $r = @mysqli_query ($dbc, $q); // Run the query.
  	while($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
      $wallet_params[$row['market']][$row['currency']]['whole_balance']=$row['whole_balance'];
      $wallet_params[$row['market']][$row['currency']]['avaiable_balance']=$row['avaiable_balance'];
      $wallet_params[$row['market']][$row['currency']]['reserved_balance']=$row['reserved_balance'];
      $wallet_params[$row['market']][$row['currency']]['taker_fee']=$row['taker_fee'];
      $wallet_params[$row['market']][$row['currency']]['maker_fee']=$row['maker_fee'];
    }
}

        //Marketten çekilmiş fiyat bilgileri parçalar ve diziye atar
        function parse_prices_and_volume_from_market_data($exchange,$datas,$pair1,$pair2){
          $bid_timestamp='';
          $bid_price='';
          $bid_volume='';
          $ask_timestamp='';
          $ask_price='';
          $ask_volume='';
          $price_status=false;

          global $price_params;

          if($exchange=='BtcTurk') {
                $market_coef=0.5;
              @$bid_timestamp=$datas->timestamp;
              @$bid_price=$datas->bids[0][0];
              @$bid_volume=$datas->bids[0][1];
              @$ask_timestamp=$datas->timestamp;
              @$ask_price=$datas->asks[0][0];
              @$ask_volume=$datas->asks[0][1];
              @$price_status=true;
          } else if($exchange=='Kraken') {
              $market_coef=0.8;
            foreach ($datas->getAsks() as $ask) {
              $ask_timestamp==$ask->getTimestamp();
              $ask_price=$ask->getPrice();
              $ask_volume=$ask->getVolume();
              $price_status=true;

            }

            foreach ($datas->getBids() as $bids) {
              $bid_timestamp==$bids->getTimestamp();
              $bid_price=$bids->getPrice();
              $bid_volume=$bids->getVolume();
              $price_status=true;
            }

          }

          if($price_status==true) {
            $price_params[$exchange][$pair1.$pair2]['ask']['price']=$ask_price;
            $price_params[$exchange][$pair1.$pair2]['ask']['volume']=$ask_volume*$market_coef;
            $price_params[$exchange][$pair1.$pair2]['ask']['timestamp']=$ask_timestamp;
            $price_params[$exchange][$pair1.$pair2]['ask']['volume_coef']=$market_coef;
            $price_params[$exchange][$pair1.$pair2]['bid']['price']=$bid_price;
            $price_params[$exchange][$pair1.$pair2]['bid']['volume']=$bid_volume*$market_coef;
            $price_params[$exchange][$pair1.$pair2]['bid']['timestamp']=$bid_timestamp;
            $price_params[$exchange][$pair1.$pair2]['bid']['volume_coef']=$market_coef;
          }

          $output_array[0]=$bid_price;
          $output_array[1]=$bid_volume*$market_coef;
          $output_array[2]=$bid_timestamp;
          $output_array[3]=$ask_price;
          $output_array[4]=$ask_volume*$market_coef;
          $output_array[5]=$ask_timestamp;
          $output_array[6]=$price_status;
          return $output_array;

        }


        //çekilen fiyatlara bakarak kar oranlarını hesaplar ve sipariş oluşturur
        function calculate_rates_and_make_a_cycle($exchange1,$pair1,$pair1A,$exchange2,$pair2,$pair2A){

          global $price_params;
          global $gain_rates;
          global $min_start_rates;
          global $cur_rates;
          global $wallet_params;
          global $json_return_params;
          global $market_data;

          if($pair1A=='ZUSD'){
            $cur_text="USD";
            $cur_rate_str=1/$cur_rates['USDTRY'];
            $cur_rate_rev=$cur_rates['USDTRY'];
          } else if($pair1A=='ZEUR'){
            $cur_text="EUR";
            $cur_rate_str=1/$cur_rates['EURTRY'];
            $cur_rate_rev=$cur_rates['EURTRY'];
          }

          $cur_date=$cur_rates['CURTIME'];

          ////////////////////////////////
          /*
          echo "FİYAT KARŞILAŞTIRMA BAŞLADI ----". "<br/>". "<br/>";
          echo $cur_text.":" . $cur_rate_str . "<br/>";
          echo $cur_text.":" . $cur_rate_rev ."<br/>";
          echo "ZAMAN:" . $cur_date ."<br/>";
          echo "AL " . $exchange1 . " : " . $price_params[$exchange1][$pair1.$pair1A]['ask']['price']*$cur_rate_rev . " SAT "  . $exchange2 . " : " . $price_params[$exchange2][$pair2.$pair2A]['bid']['price']."<br/>";
          echo "SAT " . $exchange1 . " : " . $price_params[$exchange1][$pair1.$pair1A]['bid']['price']*$cur_rate_rev . " AL "  . $exchange2 . " : " . $price_params[$exchange2][$pair2.$pair2A]['ask']['price']."<br/>";

        */
          ////////////////////////////////



          @$gain_rates['straight']['normal'] = ((($price_params[$exchange2][$pair2.$pair2A]['bid']['price']/$price_params[$exchange1][$pair1.$pair1A]['ask']['price'])*$cur_rate_str)-1)*100;
          @$gain_rates['straight']['first_sell'] = ((($price_params[$exchange2][$pair2.$pair2A]['ask']['price']/$price_params[$exchange1][$pair1.$pair1A]['ask']['price'])*$cur_rate_str)-1)*100;
          @$gain_rates['straight']['first_buy'] = ((($price_params[$exchange2][$pair2.$pair2A]['bid']['price']/$price_params[$exchange1][$pair1.$pair1A]['bid']['price'])*$cur_rate_str)-1)*100;
          @$gain_rates['straight']['max'] = ((($price_params[$exchange2][$pair2.$pair2A]['ask']['price']/$price_params[$exchange1][$pair1.$pair1A]['bid']['price'])*$cur_rate_str)-1)*100;

          @$gain_rates['reverse']['normal'] = ((($price_params[$exchange1][$pair1.$pair1A]['bid']['price']/$price_params[$exchange2][$pair2.$pair2A]['ask']['price'])*$cur_rate_rev)-1)*100;
          @$gain_rates['reverse']['first_sell'] = ((($price_params[$exchange1][$pair1.$pair1A]['ask']['price']/$price_params[$exchange2][$pair2.$pair2A]['ask']['price'])*$cur_rate_rev)-1)*100;
          @$gain_rates['reverse']['first_buy'] = ((($price_params[$exchange1][$pair1.$pair1A]['bid']['price']/$price_params[$exchange2][$pair2.$pair2A]['bid']['price'])*$cur_rate_rev)-1)*100;
          @$gain_rates['reverse']['max'] = ((($price_params[$exchange1][$pair1.$pair1A]['ask']['price']/$price_params[$exchange2][$pair2.$pair2A]['bid']['price'])*$cur_rate_rev)-1)*100;

          /*
          echo "Düz Arbi :". $gain_rates['straight']['normal']."<br/>";
          echo "Ters Arbi :" . $gain_rates['reverse']['normal']."<br/>"."<br/>";
          print_r($wallet_params);
          */
                //BTCturk bağlantı oluştur
              //  list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys('BtcTurk','mustafa',2);
              //  $btcturk_connect = new Client ($btcturk_key, $btcturk_secret);


              /*
            if($gain_rates['straight']['first_sell']>=$min_start_rates['straight']['first_sell'] && $gain_rates['straight']['first_sell']>($gain_rates['straight']['normal']+$min_start_rates['straight']['first_sell_diff'])){
                //make_order

                  make_order_on_market('BtcTurk','first_sell','sell',$price_params[$exchange2][$pair2.$pair2A]['ask']['price'],$price_params[$exchange2][$pair2.$pair2A]['ask']['volume'],$price_params[$exchange2][$pair2.$pair2A]['ask']['price'],'BTCTRY','','');

                //prepare_order_on_db('Kraken','wait','buy','5500','0.1','5650','XXBTZUSD',$related_tx_id,$related_id);
                  echo "first_sell";
                    echo "<br/>";
              }else if($gain_rates['reverse']['first_buy']>=$min_start_rates['reverse']['first_buy'] && $gain_rates['reverse']['first_buy']>($gain_rates['reverse']['normal']+$min_start_rates['reverse']['first_buy_diff'])){
                //make_order
                echo "ters first_buy";
                  echo "<br/>";
              }
              */
            /*  echo "başla <br/>

              </br/>";*/
              $arbitrage_found=0;
              $color_code="#36a64f";
              $explanation="";

              $json_return_params['slack_status_api']="DYNO_CYCLE";

              if($gain_rates['straight']['normal']>=$min_start_rates['straight']['normal']){

                  $arbitrage_found=1;
                  $target_gain_rate=$gain_rates['straight']['normal'];
                  $arbi_type="Straight Cycle";
                  $cycle_status_api="DYNO_CYCLE";
                  $target_volume=0;
                  $market1_act_type="buy";
                  $market1_force_type="normal";
                  $market1_price=$price_params[$exchange1][$pair1.$pair1A]['ask']['price'];
                  $market1_volume=$price_params[$exchange1][$pair1.$pair1A]['ask']['volume'];
                  $market1_avaible=$wallet_params[$exchange1][$pair1]['avaiable_balance'];
                  $market1_fiat_avaible=$wallet_params[$exchange1][$pair1A]['avaiable_balance'];
                  $market1_pairs=$pair1.$pair1A;

                  $market2_act_type="sell";
                  $market2_force_type="normal";
                  $market2_price=$price_params[$exchange2][$pair2.$pair2A]['bid']['price'];
                  $market2_volume=$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'];
                  $market2_avaible=$wallet_params[$exchange2][$pair2]['avaiable_balance'];
                  $market2_fiat_avaible=$wallet_params[$exchange2][$pair2A]['avaiable_balance'];
                  $market2_pairs=$pair2.$pair2A;

                  @$market1_max_act=($wallet_params[$exchange1][$pair1A]['avaiable_balance'] / $market1_price)*0.95;
                  @$market2_max_act=$wallet_params[$exchange2][$pair2]['avaiable_balance']*0.95;
                //  @$target_volume=sprintf("%1.4f",min($market1_avaible, $market1_max_act, $market1_volume, $market2_avaible, $market2_max_act, $market2_volume));
                    @$target_volume=sprintf("%1.4f",min($market1_max_act, $market1_volume, $market2_max_act, $market2_volume));
                  if($target_volume>0)
                   $target_volume-=0.0001;
                  ///////////////////
                  /*
                  echo "Geçerli $arbi_type Arbitraj yakalandı: ". $target_gain_rate."<br/>";

                  */

                //  echo "İşlem Hacmi : " . $target_volume;

                            //      echo $market1_avaible . " : " . $market1_max_act. " : " .$market1_volume. " : " . $market2_avaible. " : " . $market2_max_act. " : " .$market2_volume;
                            //      echo "<br/>

                            //      </br/>sonnn1 : " . $wallet_params[$exchange2][$pair2A]['avaiable_balance'] . " / ". $market2_price;
                                 ///////////////////
              } else if($gain_rates['reverse']['normal']>=$min_start_rates['reverse']['normal']){
            /*    echo "2<br/>

                </br/>";*/
                 $arbitrage_found=2;
                 $target_gain_rate=$gain_rates['reverse']['normal'];
                 $arbi_type="Reverse Cycle";
                $cycle_status_api="DYNO_CYCLE";
                 $target_volume=0;
                 $market1_act_type="sell";
                 $market1_force_type="normal";
                 $market1_price=$price_params[$exchange1][$pair1.$pair1A]['bid']['price'];
                 $market1_volume=$price_params[$exchange1][$pair1.$pair1A]['bid']['volume'];
                 $market1_avaible=$wallet_params[$exchange1][$pair1]['avaiable_balance'];
                 $market1_fiat_avaible=$wallet_params[$exchange1][$pair1A]['avaiable_balance'];
                 $market1_pairs=$pair1.$pair1A;

                 $market2_act_type="buy";
                 $market2_force_type="normal";
                 $market2_price=$price_params[$exchange2][$pair2.$pair2A]['ask']['price'];
                 $market2_volume=$price_params[$exchange2][$pair2.$pair2A]['ask']['volume'];
                 $market2_avaible=$wallet_params[$exchange2][$pair2]['avaiable_balance'];
                 $market2_fiat_avaible=$wallet_params[$exchange2][$pair2A]['avaiable_balance'];
                 $market2_pairs=$pair2.$pair2A;


                 @$market1_max_act=$wallet_params[$exchange1][$pair1]['avaiable_balance'] *0.95;
                 @$market2_max_act=($wallet_params[$exchange2][$pair2A]['avaiable_balance'] / $market2_price)*0.95;
                // @$target_volume=sprintf("%1.4f",min($market1_avaible, $market1_max_act, $market1_volume, $market2_avaible, $market2_max_act, $market2_volume));
                   @$target_volume=sprintf("%1.4f",min($market1_max_act, $market1_volume, $market2_max_act, $market2_volume));
                 if($target_volume>0)
                  $target_volume-=0.0001;

              //    echo $market1_avaible . " : " . $market1_max_act. " : " .$market1_volume. " : " . $market2_avaible. " : " . $market2_max_act. " : " .$market2_volume;
              //    echo "<br/>

                //  </br/>sonnn2 : " . $wallet_params[$exchange2][$pair2A]['avaiable_balance'] . " / ". $market2_price;
                 ///////////////////
                /*
                 echo "Geçerli $arbi_type Arbitraj yakalandı: ". $target_gain_rate."<br/>";

                 echo "İşlem Hacmi : " . $target_volume;
                 */

                } else {
                /*  echo "3<br/>

                  </br/>";*/
                  /*
                  echo "UYGUN ARBİ YOK";
                    echo "<br/>";
                    */
                    $color_code="#848484";
                    $target_gain_rate=$gain_rates['straight']['normal'];
                    $arbi_type="No Cycle";
                    $cycle_status_api="CYCLE_STATUS";
                    $explanation=0;
                    $target_volume=0;
                    $market1_act_type="buy";
                    $market1_force_type="normal";
                    $market1_price=$price_params[$exchange1][$pair1.$pair1A]['ask']['price'];
                    $market1_volume=$price_params[$exchange1][$pair1.$pair1A]['ask']['volume'];
                    $market1_avaible=$wallet_params[$exchange1][$pair1]['avaiable_balance'];
                    $market1_fiat_avaible=$wallet_params[$exchange1][$pair1A]['avaiable_balance'];
                    $market1_pairs=$pair1.$pair1A;

                    $market2_act_type="sell";
                    $market2_force_type="normal";
                    $market2_price=$price_params[$exchange2][$pair2.$pair2A]['bid']['price'];
                    $market2_volume=$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'];
                    $market2_avaible=$wallet_params[$exchange2][$pair2]['avaiable_balance'];
                    $market2_fiat_avaible=$wallet_params[$exchange2][$pair2A]['avaiable_balance'];
                    $market2_pairs=$pair2.$pair2A;

                    @$market1_max_act=($wallet_params[$exchange1][$pair1A]['avaiable_balance'] / $market1_price)*0.95;
                    @$market2_max_act=$wallet_params[$exchange2][$pair2]['avaiable_balance']*0.95;
                    //@$target_volume=sprintf("%1.4f",min($market1_avaible, $market1_max_act, $market1_volume, $market2_avaible, $market2_max_act, $market2_volume));

                    ///////////////////
                  //  echo "Geçerli $arbi_type Arbitraj yakalandı: ". $target_gain_rate."<br/>";
                  //  $target_volume=sprintf("%1.4f",min($market1_avaible, $market1_max_act, $market1_volume, $market2_avaible, $market2_max_act, $market2_volume));
                  //  $target_volume-=0.0001;
                  //  echo "İşlem Hacmi : " . $target_volume;

                }

                @$wallet_params[$exchange1][$pair1]['act_balance']=$market1_max_act;
                @$wallet_params[$exchange2][$pair2]['act_balance']=$market2_max_act;
                @$json_return_params['market1_volume']=$market1_volume;
                @$json_return_params['market2_volume']=$market2_volume;
                $max_market_volume=min($market1_volume, $market2_volume);
                $max_straight_market_volume=min($price_params[$exchange1][$pair1.$pair1A]['ask']['volume'],$price_params[$exchange2][$pair2.$pair2A]['bid']['volume']);
                $max_reverse_market_volume=min($price_params[$exchange1][$pair1.$pair1A]['bid']['volume'],$price_params[$exchange2][$pair2.$pair2A]['ask']['volume']);
                $json_return_params["act_volume"]=$target_volume;

                $sell_return="";
                $buy_return="";

                $json_return_params['status']=$arbi_type;
                $json_return_params['result']="false";
                $json_return_params['act_rate']=$target_gain_rate;
                $json_return_params['explanation']=$explanation;
                $json_return_params['color']=$color_code;
                $json_return_params['slack_status']=$arbitrage_found;
                $json_return_params['slack_status_api']=$cycle_status_api;

             if($arbitrage_found>0){
            //   echo "1";
                      //echo $exchange2. " : " .$market2_force_type. " : " .$market2_act_type. " : " .$market2_price. " : " .$target_volume. " : " .$market2_price . " : " . $market2_pairs;

                    //  if(($target_volume>0.0001 && $market2_pairs=="BTCTRY") || ($target_volume>1 && $market2_pairs=="XRPTRY") || ($target_volume>0.01 && $market2_pairs=="ETHTRY") || ($target_volume>1 && $market2_pairs=="XLMTRY") || ($target_volume>0.01 && $market2_pairs=="LTCTRY")){
                    if($target_volume>=$market_data[0]['minumum_order']) {
                    //  echo "2";
                             //e

                        $json_return_params["explanation"]=$target_volume . " " . $market2_pairs . ", Rate:" .  $target_gain_rate . ", Max Volume: " . $max_market_volume ;

                        list($sell_return,$sell_error)=make_order_on_market($exchange2,$market2_force_type,$market2_act_type,$market2_price,$target_volume,$market2_price, $market2_pairs,'','');
                        if ($sell_return!="") {
                          list($buy_return,$buy_error)=make_order_on_market($exchange1,$market1_force_type,$market1_act_type,$market1_price,$target_volume,$market1_price, $market1_pairs,'',$sell_return);
                        }


                          $sell_row_id=0;
                          $buy_row_id=0;
                          if($sell_return!="")
                            $sell_row_id=make_order_row_on_db($sell_return, $exchange2, $market2_pairs, $market2_act_type, $market2_force_type, $market2_price, $target_volume, $market2_price, 0,$buy_return);

                          if($buy_return!="")
                            $buy_row_id=make_order_row_on_db($buy_return, $exchange1, $market1_pairs, $market1_act_type, $market1_force_type, $market1_price, $target_volume, $market1_price, $sell_row_id, $sell_return);

                          $json_return_params['tx_id1']=$buy_return;
                          $json_return_params['tx_id2']=$sell_return;

                            if($sell_return!="" && $buy_return!="") {
                            //  $json_return_params['status']=$arbi_type . " " . $target_gain_rate;
                              $json_return_params['result']="true";
                              //$json_return_params['explanation']="";
                            //  $json_return_params['color']=$color_code;
                            } else if($sell_return!="") {
                            //  $json_return_params['status']=$arbi_type . " " . $target_gain_rate.  " -> : M1: ". $buy_error;
                              $json_return_params['result']="true false";
                              $json_return_params['explanation']="M1: ". $buy_error;
                              $json_return_params['color']="#FF0000";
                            //  $json_return_params['slack_status_api']="CYCLE_WARNINGS";
                            } else if($sell_return=="") {
                              //$json_return_params['status']=$arbi_type . " " . $target_gain_rate . " -> M2: ". $sell_error;
                              $json_return_params['result']="false";
                              $json_return_params['explanation']="M2: ". $sell_error;
                              $json_return_params['color']="#FF0000";
                                $json_return_params['slack_status_api']="CYCLE_WARNINGS";
                            }


                      } else {
                        //  echo "3";
                          $json_return_params['result']="false";
                          $json_return_params['color']="#FF0000";
                          $json_return_params['slack_status_api']="CYCLE_WARNINGS";
                        if($market1_max_act<$market_data[0]['minumum_order']){
                          //  echo "4";
                          $json_return_params['explanation']="Insufficient balance on M1";

                        }  else {
                          //  echo "5";
                          $json_return_params['explanation']="Insufficient balance on M2";
                        }

                        $json_return_params["explanation"] .=" Rate:" .  $target_gain_rate . ", Max Volume: " . $max_market_volume ;

                      }





          }
           else {
             //echo "6";
             $json_return_params['result']="false";
             $json_return_params['explanation']="Its Still Mining...";
               $json_return_params["explanation"] .=" Straight Rate:" .  $gain_rates['straight']['normal'] . ", Max Volume: " . $max_straight_market_volume .  " Reverse Rate:" .  $gain_rates['reverse']['normal'] . ", Max Volume: " . $max_reverse_market_volume ;
             $json_return_params['color']="#848484";
             $json_return_params['slack_status_api']="CYCLE_STATUS";
             $mnt=date('i');
             $scn=date('s');
             if($mnt==0 && $scn>=0 && $scn<16){

               $json_return_params['slack_status']=1;
             }


          }
/*
          echo "<br/>

          </br/>";
          echo "A: ". $json_return_params['slack_status'] . "<br/>

          </br/>B: ";
          $json_return_params['slack_status_api']. "<br/>

          </br/>";*/

                                // DBde fiyat logu tut

                                   $action_id=mt_rand();

                                   $price_tl1=$price_params[$exchange1][$pair1.$pair1A]['ask']['price']*$cur_rate_rev;
                                   $price_tl1A=$price_params[$exchange1][$pair1.$pair1A]['bid']['price']*$cur_rate_rev;
                                   $price_tl2=$price_params[$exchange2][$pair2.$pair2A]['ask']['price'];
                                   $price_tl2A=$price_params[$exchange2][$pair2.$pair2A]['bid']['price'];

                                   $arb_max_vol1=min($price_params[$exchange1][$pair1.$pair1A]['bid']['volume'],$price_params[$exchange2][$pair2.$pair2A]['ask']['volume']);
                                   $arb_max_vol2=min($price_params[$exchange1][$pair1.$pair1A]['ask']['volume'],$price_params[$exchange2][$pair2.$pair2A]['bid']['volume']);

                                   $final_balance1=$arb_max_vol1*$price_params[$exchange1][$pair1.$pair1A]['ask']['price'];
                                   $final_balance2=$arb_max_vol2*$price_params[$exchange2][$pair2.$pair2A]['ask']['price'];

                                   $total_tl1=$price_tl1*$price_params[$exchange1][$pair1.$pair1A]['ask']['volume'];
                                   $total_tl1A=$price_tl1A*$price_params[$exchange1][$pair1.$pair1A]['bid']['volume'];

                                   $total_tl2=$price_tl2*$price_params[$exchange2][$pair2.$pair2A]['ask']['volume'];
                                   $total_tl2A=$price_tl2A*$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'];

                                  //   if($sell_return!=''){
                                       make_price_row_log_on_db($action_id,$exchange2,$market2_pairs,$pair2,$pair2A,
                                       $price_params[$exchange2][$pair2.$pair2A]['ask']['price'],$price_params[$exchange2][$pair2.$pair2A]['bid']['price'],
                                       $price_tl2,$price_tl2A,
                                       $price_params[$exchange2][$pair2.$pair2A]['ask']['volume'],$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'],
                                       $total_tl2,$total_tl2A,
                                       $gain_rates['straight']['normal'],$gain_rates['straight']['first_sell'],$gain_rates['straight']['first_buy'],$gain_rates['straight']['max'],
                                       $arb_max_vol2,$final_balance2,
                                       $price_params[$exchange2][$pair2.$pair2A]['ask']['timestamp'],
                                       $sell_return);
                                  //   }
                                  //   if($buy_return!='') {
                                       make_price_row_log_on_db($action_id,$exchange1,$market1_pairs,$pair1,$pair1A,
                                       $price_params[$exchange1][$pair1.$pair1A]['ask']['price'],$price_params[$exchange1][$pair1.$pair1A]['bid']['price'],
                                       $price_tl1,$price_tl1A,
                                       $price_params[$exchange1][$pair1.$pair1A]['ask']['volume'],$price_params[$exchange1][$pair1.$pair1A]['bid']['volume'],
                                       $total_tl1,$total_tl1A,
                                       $gain_rates['reverse']['normal'],$gain_rates['reverse']['first_sell'],$gain_rates['reverse']['first_buy'],$gain_rates['reverse']['max'],
                                       $arb_max_vol1,$final_balance1,
                                       $price_params[$exchange1][$pair1.$pair1A]['ask']['timestamp'],
                                       $buy_return);

                                  //   }


                                  // D:B de fiyat logu tut bitti





        }

function parse_decimals($volume){

    $volume_endi=array_pad(explode('.', $volume,2),2,null);

  return $volume_endi;


}

function make_order_on_market($exchange,$force_type,$act_type,$price,$volume,$no_gain_price,$pairs,$related_id='',$related_tx_id=''){
  global $kraken_connect;
  global $btcturk_connect;

  if($volume>0) {
    if($force_type!='wait'){
      if($exchange=='BtcTurk'){

      //  echo "<br/> burda";
        list($volume_body,$volume_end)=parse_decimals($volume);
        list($price_body,$price_end)=parse_decimals($price);


        /*
        list($btcturk_key,$btcturk_secret)=get_market_api_secret_keys('BtcTurk','mustafa',2);
        $btcturk_connect = new Client ($btcturk_key, $btcturk_secret);
        */

        /*
        echo "<br/>";
        echo $volume . " : ". $price;      echo "<br/>";
        echo $pairs . "," . $volume_body. "," .$volume_end. "," .$price_body. "," .$price_end. " ";
        echo "<br/>";
        */
        $arr[0]="";
        $arr[1]="";


          if($act_type=='sell' && $force_type=='first_sell'){
                ///    echo "<br/> 1";
                try{
                 $btcturk_order_response=$btcturk_connect->getLimitSell($pairs,$volume_body,$volume_end,$price_body,$price_end);
                 $arr[0]=$btcturk_order_response->id;
               }  catch (Exception $e) {
                    $arr[1]=$btcturk_order_response->message;
                }



          } else if($act_type=='buy' && $force_type=='first_buy'){

            try{
                $btcturk_order_response=$btcturk_connect->getLimitBuy($pairs,$volume_body,$volume_end,$price_body,$price_end);
             $arr[0]=$btcturk_order_response->id;
           }  catch (Exception $e) {
                $arr[1]=$btcturk_order_response->message;
            }
              //      echo "<br/> 2";

          } else if($act_type=='sell' && $force_type=='normal'){
            try{
              //    echo "<br/> 3";
              $btcturk_order_response=$btcturk_connect->getLimitSell($pairs,$volume_body,$volume_end,$price_body,$price_end);
             $arr[0]=$btcturk_order_response->id;
           }  catch (Exception $e) {
                $arr[1]=$btcturk_order_response->message;
            }


         } else if($act_type=='buy' && $force_type=='normal'){
           try{
             //   echo "<br/> 4";
             $btcturk_order_response=$btcturk_connect->getLimitBuy($pairs,$volume_body,$volume_end,$price_body,$price_end);
            $arr[0]=$btcturk_order_response->id;
          }  catch (Exception $e) {
               $arr[1]=$btcturk_order_response->message;
           }


        }




              if(@$btcturk_order_response->id){
                  $arr[0]=$btcturk_order_response->id;
              } else {
              //  print_r($btcturk_order_response);
                  $arr[1]=$btcturk_order_response->message;
              }
            return $arr;

      } else if($exchange=='Kraken'){

        $arr[0]="";
        $arr[1]="";
      // echo "<br/> 5";
                      try {
                       $addOrderResponse = $kraken_connect->addOrder(
                            $pairs,
                            $act_type,
                            'limit',
                            $price,
                            $volume,
                            true
                        );

/*
                        $addOrderResponse = $kraken_connect->addOrder(
                            $pairs,
                            $act_type,
                            'limit',
                            3155.6,
                            $volume,
                            true
                        );

*/

                          $arr[0]=$addOrderResponse->getTxid();
                      } catch (Exception $e) {
                          $arr[1]=$e->getMessage();
                      }
                      return $arr;
                      /*
            if($act_type=='sell' && $force_type=='first_sell'){

            } else if($act_type=='buy' && $force_type=='first_buy'){

            } else if($act_type=='sell' && $force_type=='normal'){

            } else if($act_type=='buy' && $force_type=='normal'){


            }

            */


      }
    }
  }
}


function make_order_row_on_db($txid, $exchange, $pairs, $act_type, $force_type, $price, $volume,  $no_gain_price, $id=0,$related_tx_id=''){
  global $dbc;
  $total=$price*$volume;
  /*
echo "<br/>
$txid $exchange row işledi
  </br/>";*/
 $q ="INSERT INTO open_orders (txid, market, pairs, act_type, force_type, price, volume, filled_volume, total, no_gain_price, fld_active,status, related_id, related_tx_id) VALUES (
  '".  $txid . "', '" . $exchange . "','" .  $pairs . "','" . $act_type .  "', '" . $force_type . "','" .  $price. "','" . $volume. "', '0', '" .   $total. "','" .  $no_gain_price. "','1','new','" . $id. "','" . $related_tx_id  ."' )";
  $r = @mysqli_query ($dbc, $q); // Run the query.

  $last_insert_id=$dbc->insert_id;
  return $last_insert_id;




}

function update_order_row_on_db(){


}


function make_price_row_log_on_db($action_id,$market1,$pairs1,$k2_1,$k1_1,$price1,$price1A,$price_tl1,$price_tl1A,$quantity1,$quantity1A,$total_tl1,$total_tl1A,$gain1,$gain1_A,$gain1_B,$gain1_C,$arb_max_vol1,$final_balance1,$timestamp1,$open_tx_id){
  global $dbc;
  global $cur_rates;



  $q="INSERT INTO `cycle_price_rows`
  (action_id, market, pairs, coin1, coin2,
    buy_price, sell_price,
    buy_price_tl, sell_price_tl,
    buy_volume, sell_volume,
    buy_total_tl, sell_total_tl,
    sh_gain,  sh_gain_A, sh_gain_B, sh_gain_C,
     sh_volume, sh_total,
    atimestamp, entry, usd_cur, eur_cur, cur_time, cur_source, open_tx_id)
  VALUES ('" . $action_id ."','" . $market1 ."', '" . $pairs1 ."', '" . $k2_1 ."', '" . $k1_1 . "','" . $price1 ."', '" . $price1A ."', '" . $price_tl1 ."', '" . $price_tl1A .
    "','" . $quantity1 ."', '" . $quantity1A ."', '" . $total_tl1 ."', '" . $total_tl1A ."',
    '" . $gain1 .  "',    '" . $gain1_A .  "',    '" . $gain1_B .  "',    '" . $gain1_C .  "',

    '" . $arb_max_vol1 ."', '" . $final_balance1 ."', '" . $timestamp1 . "','" . date("Y-m-d H:i:s") ."', '" . $cur_rates['USDTRY'] . "', '" . $cur_rates['EURTRY'] . "', '" . $cur_rates['CURTIME'] . "', '" . $cur_rates['APISOURCE'] . "','" . $open_tx_id . "');";

  $r = @mysqli_query ($dbc, $q); // Run the query.
}

/*
    function get_current_balance($exchange,$balance){
        if($exchange=="Kraken") {
            $datas=$balance['result'];
            $current_euro=$datas['ZEUR'];
            $current_eth=$datas['XETH'];
            $current_ltc=$datas['XLTC'];
            $current_btc=$datas['XXBT'];
        } else if($exchange=="Kucoin") {

            $datas=$balance['data'];

            foreach ($datas as $data) {
                if($data['coinType']=='ETH')  {
                    $current_eth=$data['balance'];
                }
                if($data['coinType']=='LTC')  {
                    $current_ltc=$data['balance'];
                }
                if($data['coinType']=='BTC')  {
                    $current_btc=$data['balance'];
                }
            }
            $current_euro=0;

        }

        $arr[0]=$current_euro;
        $arr[1]=$current_eth;
        $arr[2]=$current_ltc;
        $arr[3]=$current_btc;
        return $arr;
    }


function kraken_make_order($pair1, $pair2, $type, $order_type, $volume, $price){
    if($order_type=="limit") {
        $res = $kraken->QueryPrivate('AddOrder', array(
            'pair' => $pair1.$pair2,
            'type' => $type,
            'ordertype' => $order_type,
            'price' => $price,
            'volume' => $volume,
        ));
    }{
        $res = $kraken->QueryPrivate('AddOrder', array(
            'pair' => $pair1.$pair2,
            'type' => $type,
            'ordertype' => $order_type,
            'volume' => $volume,
        ));

    }
   return $res;

}*/




function print_json_results($basla){
global $dbc;
    global $json_return_params;
    global $market_data;
    global $wallet_params;
    global $cur_rates;
    global $min_start_rates;
    global $gain_rates;



    $q="INSERT INTO `cycle_results`
    (status, result, pairs1, pairs2,
      usd_tl, eur_tl, cur_time,
      market1_volume, market2_volume,
      max_volume1, max_volume2, act_volume,
       rate, target_rate, rev_rate, target_rev_rate,
       tx_id1, tx_id2,
       entry, duration)
    VALUES ('" . $json_return_params['status'] ." : ". $json_return_params['explanation'] . "',
      '" . $json_return_params['result'] ."',
      '" . $market_data[0]['pairs'] ."', '" . $market_data[1]['pairs'] ."',
      '" . $cur_rates['USDTRY'] . "','" . $cur_rates['EURTRY'] ."', '" . $cur_rates['CURTIME'] ."',
          '" . $json_return_params['market1_volume']."',
          '" . $json_return_params['market2_volume']."',
      '" . $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ."',
      '" . $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance'] ."',
      '" . $json_return_params['act_volume'] ."',
      '" . $gain_rates['straight']['normal'] ."',
       '" . $min_start_rates['straight']['normal'] ."',
       '" . $gain_rates['reverse']['normal'] ."',
      '" . $min_start_rates['reverse']['normal'] .  "',
      '" . $json_return_params['tx_id1'] .  "',    '" . $json_return_params['tx_id2'] .  "',
         '" . date('Y-m-d H:i:s') .  "',
      '" . $basla ."'
    );";

    $r = @mysqli_query ($dbc, $q); // Run the query.


    if($json_return_params['slack_status']>0){

      $attachments=array();
      //#36a64f
      $attachments[0]["color"]=$json_return_params['color'];
      $attachments[0]["text"]=$json_return_params['explanation'];

      $attachments[0]["fields"][0]["title"]=$json_return_params['status'];
      $attachments[0]["fields"][0]["value"]="";
      if($json_return_params['tx_id1']!="")
        $attachments[0]["fields"][0]["value"]= $json_return_params['tx_id1'] ;
      if($json_return_params['tx_id2']!="")
        $attachments[0]["fields"][0]["value"].=" | " .  $json_return_params['tx_id2'] ;
      $attachments[0]["fields"][0]["short"]=true;

      $attachments[0]["fields"][1]["title"]=$json_return_params['result'] ;
      $attachments[0]["fields"][1]["value"]=$json_return_params['act_rate'];
      $attachments[0]["fields"][1]["short"]=true;

      $attachments[0]["fields"][2]["title"]=$market_data[1]['pairs'];
      $attachments[0]["fields"][2]["value"]=$json_return_params['act_volume'];
      $attachments[0]["fields"][2]["short"]=true;

      $attachments[0]["fields"][3]["title"]=$wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'];
      $attachments[0]["fields"][3]["value"]=$wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance'];
      $attachments[0]["fields"][3]["short"]=true;


      $attachments[0]["image_url"]="http://my-website.com/path/to/image.jpg";
      $attachments[0]["thumb_url"]="http://example.com/path/to/thumb.png";
      $attachments[0]["footer"]="Dyno Cycle API";
      $attachments[0]["footer_icon"]="https://platform.slack-edge.com/img/default_application_icon.png";

      $timestamp = time();

      $attachments[0]["ts"]=$timestamp;

      send_slack_message(' ' , $json_return_params['slack_status_api'], $attachments);

    }


/*
    $q="INSERT INTO `cycle_results2`
    (status, result, pairs1, pairs2,
      usd_tl, eur_tl, cur_time,
      max_volume1, max_volume2, act_volume,
       rate, target_rate, rev_rate, target_rev_rate,
       tx_id1, tx_id2,
       entry, duration)
    VALUES ('" . $json_return_params['status'] ."',
      '" . $json_return_params['result'] ."',
      '" . $market_data[0]['pairs'] ."', '" . $market_data[1]['pairs'] ."',
      '" . $cur_rates['USDTRY'] . "','" . $cur_rates['EURTRY'] ."', '" . $cur_rates['CURTIME'] ."',
      '" . $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] ."',
      '" . $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance'] ."',
      '" . $json_return_params['act_volume'] ."',
      '" . $gain_rates['straight']['normal'] ."',
       '" . $min_start_rates['straight']['normal'] ."',
       '" . $gain_rates['reverse']['normal'] ."',
      '" . $min_start_rates['reverse']['normal'] .  "',
      '" . $json_return_params['tx_id1'] .  "',    '" . $json_return_params['tx_id2'] .  "',
         '" . date('Y-m-d H:i:s') .  "',
      '" . $basla ."'
    );";

    $r = @mysqli_query ($dbc, $q); // Run the query.
    */
//echo $q;
  echo '{
    "status":"' . $json_return_params['status'] . ' : ' . $json_return_params['explanation'] . '",
    "result":"' . $json_return_params['result'] . '",
    "pairs1":"' . $market_data[0]['pairs'] . '",
    "pairs2":"' . $market_data[1]['pairs'] . '",
    "usd_tl":"' . $cur_rates['USDTRY'] . '",
    "eur_tl":"' . $cur_rates['EURTRY'] . '",
    "cur_time":"' . $cur_rates['CURTIME'] . '",
    "max_volume1":"' . $wallet_params[$market_data[0]['market']][$market_data[0]['coin1']]['act_balance'] . '",
    "max_volume2":"' . $wallet_params[$market_data[1]['market']][$market_data[1]['coin1']]['act_balance'] . '",
    "act_volume":"' .  $json_return_params['act_volume']. '",
    "rate":"' . $gain_rates['straight']['normal']  . '",
    "target_rate":"' . $min_start_rates['straight']['normal'] . '",
    "rev_rate":"' .$gain_rates['reverse']['normal']  . '",
    "target_rev_rate":"' . $min_start_rates['reverse']['normal'] . '",
    "tx_id1":"' . $json_return_params['tx_id1'] . '",
    "tx_id2":"' . $json_return_params['tx_id2'] . '",
    "time" : "'.date('Y-m-d H:i:s').'",
    "duration": "'.$basla.'"
  }';
  exit();
}
?>
