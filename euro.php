<?php

include("includes/db_connect.php");


function get_cur_date_from_db(){
  global $cur_rates;
  global $dbc;

  $q="SELECT * FROM cur_rates ORDER BY id DESC LIMIT 1";
  $r = @mysqli_query ($dbc, $q); // Run the query.
    if($row = mysqli_fetch_array ($r, MYSQLI_ASSOC)){
      $cur_rates['EURTRY']=$row['eur_tl'];
      $cur_rates['USDTRY']=$row['usd_tl'];
      echo $row['eur_tl'];
    }

}
get_cur_date_from_db();
