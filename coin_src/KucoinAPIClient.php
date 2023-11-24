<?php
function milliseconds() {
    $mt = explode(' ', microtime());
    return ((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000));
}

function Kucoin_connect_api($ku_key,$ku_secret, $endpoint, $querystring){

 
    $host = 'https://api.kucoin.com'; 
    $nonce = milliseconds(); 
   
    $signstring = $endpoint.'/'.$nonce.'/'.$querystring; 
    $hash = hash_hmac('sha256', base64_encode($signstring) , $ku_secret); 
    $headers = [ 'KC-API-SIGNATURE:' . $hash, 'KC-API-KEY:' . $ku_key, 'KC-API-NONCE:' . $nonce, 'Content-Type:application/x-www-form-urlencoded' ]; 


     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL,  $host .  $endpoint);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     curl_setopt($ch, CURLOPT_HEADER, FALSE);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
 
     $response = curl_exec($ch);
     curl_close($ch);
     return $response;

}



function Kucoin_connect_api2($ku_key,$ku_secret, $endpoint, $querystring, $post_num=0, $post_string=''){
  
   
      $host = 'https://api.kucoin.com'; 
      $nonce = milliseconds(); 
     
      $signstring = $endpoint.'/'.$nonce.'/'.$querystring; 
      $hash = hash_hmac('sha256', base64_encode($signstring) , $ku_secret); 
      if($post_num>0){
        $headers = [ 'KC-API-SIGNATURE:' . $hash, 'KC-API-KEY:' . $ku_key, 'KC-API-NONCE:' . $nonce, 'Content-Type:application/x-www-form-urlencoded', 'type:buy' , 'price:0.14', 'amount:1']; 
      } else {
        $headers = [ 'KC-API-SIGNATURE:' . $hash, 'KC-API-KEY:' . $ku_key, 'KC-API-NONCE:' . $nonce, 'Content-Type:application/x-www-form-urlencoded' ]; 
      }
     
      echo "KC-API-SIGNATURE : "  . $hash . "<br/>" ;
      echo "KC-API-KEY : "  . $ku_key . "<br/>" ;
      echo "KC-API-NONCE : "  . $nonce . "<br/>" ;
    /*  
  $headers = [
      'KC-API-SIGNATURE:' . $hash,
      'KC-API-KEY:' . $ku_key,
      'KC-API-NONCE:' . $nonce,
      'Content-Type:application/json'
    ];
  */  
  $data=array();
  $data = array( "type" => "buy", "price" => "0.14", "amount" => "1");                                                                    
  $data_string = json_encode($data);
  
  $postlength = strlen($data_string);
  
       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL,  $host .  $endpoint);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
       /*curl_setopt($ch, CURLOPT_USERAGENT,
       'Mozilla/4.0 (compatible; Kucoin Bot; '.php_uname('a').'; PHP/'.phpversion().')'
      );*/
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
       curl_setopt($ch, CURLOPT_HEADER, FALSE);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
       if($post_num>0){
  
         curl_setopt($ch, CURLOPT_POST, TRUE);
          //curl_setopt($ch,CURLOPT_POST, $post_num);
         // curl_setopt($ch,CURLOPT_POSTFIELDS, 'type=buy&price=0.14&amount=1');
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
         // curl_setopt($ch,CURLOPT_POST,$postlength);       
        //  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
         
        
       }
       $response = curl_exec($ch);
       curl_close($ch);
       return $response;
  
  }



 


?>