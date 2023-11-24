<?php
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

      $q="SELECT * FROM cur_rates ORDER BY action_date DESC LIMIT 1";
      $r = @mysqli_query ($dbc, $q); // Run the query.
        if($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
          $cur_rates['EURTRY']=$row['eur_tl'];
          $cur_rates['USDTRY']=$row['usd_tl'];
          $cur_rates['CURTIME']=$row['action_date'];
          //$cur_rates['CURTIME']=$row['atimestamp'];
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

               if($open_orders[$exchange][$i]['type']=="sell"){
                  $wallet_params[$exchange][$pre_coin]['avaiable_balance']-=$order['amount'];
                  $wallet_params[$exchange][$pre_coin]['reserved_balance']+=$order['amount'];
               } else {
                   $wallet_params[$exchange][$last_coin]['avaiable_balance']-=$order['amount'];
                  $wallet_params[$exchange][$last_coin]['reserved_balance']+=$order['amount'];
               }



             }

          }
        } else if($exchange=='Kraken') {
            $i=-1;
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

             $pre_coin=substr($OrderDetails->getPair(), 0, -4);
             $last_coin=substr($OrderDetails->getPair(), -4);

             if($open_orders[$exchange][$i]['type']=="sell"){
                $wallet_params[$exchange][$pre_coin]['avaiable_balance']-=$order->getVol()-$order->getVolExec();
                $wallet_params[$exchange][$pre_coin]['reserved_balance']+=$order->getVol()-$order->getVolExec();
             } else {
                 $wallet_params[$exchange][$last_coin]['avaiable_balance']-=$order->getVol()-$order->getVolExec();
                $wallet_params[$exchange][$last_coin]['reserved_balance']+=$order->getVol()-$order->getVolExec();
             }



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
                        whole_balance='" . $cur_param['whole_balance'] . "', avaiable_balance='" . $cur_param['avaiable_balance'] . "', reserved_balance='" . $cur_param['reserved_balance'] . "', taker_fee='" . $cur_param['taker_fee']. "', maker_fee='" . $cur_param['maker_fee'] . "'
                        WHERE market='" . $key ."' AND (currency='" . $key2 . "' OR global_currency='" . $key2 . "');";
                  }
              }
          }
        }
        $r = @mysqli_multi_query ($dbc, $q); // Run the query.
        //echo $q;
        $err = mysqli_error($dbc);
        while(mysqli_next_result($dbc)){;}

    }

function update_gain_control(){
  echo "test";
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

		  $cur_text="XXX";
            $cur_rate_str=1;
            $cur_rate_rev=1;

          if($pair1A=='ZUSD' || $pair1A=='USD'){
			  $pair1A_old='ZUSD';
            $cur_text="USD";
            $cur_rate_str=1/$cur_rates['USDTRY'];
            $cur_rate_rev=$cur_rates['USDTRY'];
		} else if($pair1A=='ZEUR' || $pair1A=='EUR'){
            $cur_text="EUR";
			$pair1A_old='ZEUR';
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
			     @$json_return_params['market1_ask_volume']=$price_params[$exchange1][$pair1.$pair1A]['ask']['volume'];
                @$json_return_params['market2_ask_volume']=$price_params[$exchange2][$pair2.$pair2A]['ask']['volume'];
				  @$json_return_params['market1_bid_volume']=$price_params[$exchange1][$pair1.$pair1A]['bid']['volume'];
                @$json_return_params['market2_bid_volume']=$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'];

				   @$json_return_params['market1_ask_price']=$price_params[$exchange1][$pair1.$pair1A]['ask']['price'];
                @$json_return_params['market2_ask_price']=$price_params[$exchange2][$pair2.$pair2A]['ask']['price'];
				  @$json_return_params['market1_bid_price']=$price_params[$exchange1][$pair1.$pair1A]['bid']['price'];
                @$json_return_params['market2_bid_price']=$price_params[$exchange2][$pair2.$pair2A]['bid']['price'];

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
                  $market1_fiat_avaible=$wallet_params[$exchange1][$pair1A_old]['avaiable_balance'];
                  $market1_pairs=$pair1.$pair1A;

                  $market2_act_type="sell";
                  $market2_force_type="normal";
                  $market2_price=$price_params[$exchange2][$pair2.$pair2A]['bid']['price'];
                  $market2_volume=$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'];
                  $market2_avaible=$wallet_params[$exchange2][$pair2]['avaiable_balance'];
                  $market2_fiat_avaible=$wallet_params[$exchange2][$pair2A]['avaiable_balance'];
                  $market2_pairs=$pair2.$pair2A;

                  @$market1_max_act=($wallet_params[$exchange1][$pair1A_old]['avaiable_balance'] / $market1_price)*0.95;
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
                 $market1_fiat_avaible=$wallet_params[$exchange1][$pair1A_old]['avaiable_balance'];
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
                    $market1_fiat_avaible=$wallet_params[$exchange1][$pair1A_old]['avaiable_balance'];
                    $market1_pairs=$pair1.$pair1A;

                    $market2_act_type="sell";
                    $market2_force_type="normal";
                    $market2_price=$price_params[$exchange2][$pair2.$pair2A]['bid']['price'];
                    $market2_volume=$price_params[$exchange2][$pair2.$pair2A]['bid']['volume'];
                    $market2_avaible=$wallet_params[$exchange2][$pair2]['avaiable_balance'];
                    $market2_fiat_avaible=$wallet_params[$exchange2][$pair2A]['avaiable_balance'];
                    $market2_pairs=$pair2.$pair2A;

                    @$market1_max_act=($wallet_params[$exchange1][$pair1A_old]['avaiable_balance'] / $market1_price)*0.95;
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

						/*
                        list($sell_return,$sell_error)=make_order_on_market($exchange2,$market2_force_type,$market2_act_type,$market2_price,$target_volume,$market2_price, $market2_pairs,'','');
                        if ($sell_return!="") {
                          list($buy_return,$buy_error)=make_order_on_market($exchange1,$market1_force_type,$market1_act_type,$market1_price,$target_volume,$market1_price, $market1_pairs,'',$sell_return);
                        }
						*/

						  list($buy_return,$buy_error)=make_order_on_market($exchange1,$market1_force_type,$market1_act_type,$market1_price,$target_volume,$market1_price, $market1_pairs,'',$sell_return);

						 if ($buy_return!="") {
							  list($sell_return,$sell_error)=make_order_on_market($exchange2,$market2_force_type,$market2_act_type,$market2_price,$target_volume,$market2_price, $market2_pairs,'','');

                         }

                          $sell_row_id=0;
                          $buy_row_id=0;

						  if($buy_return!="")
                            $buy_row_id=make_order_row_on_db($buy_return, $exchange1, $market1_pairs, $market1_act_type, $market1_force_type, $market1_price, $target_volume, $market1_price, $sell_row_id, $sell_return);


                          if($sell_return!="")
                            $sell_row_id=make_order_row_on_db($sell_return, $exchange2, $market2_pairs, $market2_act_type, $market2_force_type, $market2_price, $target_volume, $market2_price, 0,$buy_return);


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

$orderMethod="limit";
        /*  if($act_type=='sell' && $force_type=='first_sell'){
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


        }*/


 try{
      $btcturk_order_response=$btcturk_connect->placeOrder($pairs, $orderMethod, $act_type, $price, $volume,  0);

      $arr[0]=$btcturk_order_response->data->id;
    }  catch (Exception $e) {
         $arr[1]=$btcturk_order_response->message;
     }



              if(@$btcturk_order_response->data->id){
                  $arr[0]=$btcturk_order_response->data->id;
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
       entry, duration,
	   market1_ask_price, market1_bid_price, market2_ask_price, market2_bid_price,
	   market1_ask_volume, market1_bid_volume, market2_ask_volume, market2_bid_volume
	   )
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
      '" . $basla ."',
	   '" . $json_return_params['market1_ask_price']."',
	   '" . $json_return_params['market1_bid_price']."',
	   '" . $json_return_params['market2_ask_price']."',
	   '" . $json_return_params['market2_bid_price']."',
	  '" . $json_return_params['market1_ask_volume']."',
	   '" . $json_return_params['market1_bid_volume']."',
	   '" . $json_return_params['market2_ask_volume']."',
	   '" . $json_return_params['market2_bid_volume']."'
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
