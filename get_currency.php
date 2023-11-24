<html>
<head>
<meta name="robots" content="noindex">
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1254">
<META HTTP-EQUIV="Content-language" CONTENT="tr">
</head>
<body>

<?php
//opcache_reset();


include ("includes/main_lib.php");
include ("includes/send_slack_message.php");
include("includes/db_connect.php");
putenv("TZ=Europe/Istanbul");
//ate_default_timezone_set('Europe/Istanbul');

function get_currency(){

    //   $url="https://api.fixer.io/latest?base=" . $pair1;
    //    $url="https://www.doviz.com/api/v1/currencies/" . $pair1 . "/latest";
    $url="http://data.fixer.io/api/latest?access_key=f420776558a8d8a53f79e28c61191431";
       $inputJSON = file_get_contents($url);
        $input= json_decode( $inputJSON, TRUE ); //convert JSON into array

    //    $veri=$input["rates"];
      //  $deger=$veri[$pair2];
      //  $veri=$input;
      //  $deger=$veri['selling'];

      $veri=$input["rates"];
        $usd_con_rate=$veri['USD'];
        $eur_tl=$veri['TRY'];
        $usd_tl=$veri['TRY']/$usd_con_rate;

        $output_array[0]=$eur_tl;
        $output_array[1]=$usd_tl;
        $output_array[2]=$input["timestamp"];
        return $output_array;
    }


    list($kur_eur,$kur_usd,$atimestamp)=get_currency();
    echo $kur_eur ."<br/>";
    echo $kur_usd ."<br/>";
    echo $atimestamp ."<br/>";
    echo date('Y-m-d H:i:s', $atimestamp);

      $q="INSERT INTO `cur_rates`
      (api_source, action_date, atimestamp, eur_tl, usd_tl)
      VALUES ('fixer.io','"  . date("Y-m-d H:i:s") ."', '" . date('Y-m-d H:i:s', $atimestamp) . "', '" . $kur_eur . "', '" . $kur_usd . "');";

      $r = @mysqli_query ($dbc, $q); // Run the query.



?>


</body>
</html>
