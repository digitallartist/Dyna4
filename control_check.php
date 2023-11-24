
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://www.google.com");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For HTTPS
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // For HTTPS
$response=curl_exec($ch);
echo $response; // Google's HTML source will be printed
curl_close($ch);
?>