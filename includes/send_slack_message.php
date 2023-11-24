<?php

//dyno_cycle
define('DYNO_CYCLE', 'https://hooks.slack.com/services/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');
//dyno_status
define('CYCLE_STATUS', 'https://hooks.slack.com/services/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');
//dyno_warnings
define('CYCLE_WARNINGS', 'https://hooks.slack.com/services/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');


function slack($txt) {
  $msg = array('text' => $txt);
  $c = curl_init(SLACK_WEBHOOK);
  curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($c, CURLOPT_POST, true);
  curl_setopt($c, CURLOPT_POSTFIELDS, array('payload' => json_encode($msg)));
  curl_exec($c);
  curl_close($c);
}


	function send_slack_message($text, $sender='DYNO_CYCLE', $attachments=array() ){
	$data = array();
	$data["text"]=$text;

/*	$data["username"]=$sender;

	$data["icon_url"]=$icon_url;
	$data["icon_emoji"]=$icon_emoji;
*/
	$data["attachments"]=$attachments;

	$data_string = json_encode($data);

//  echo $data_string;
	//bakırcı
	//$ch = curl_init('https://hooks.slack.com/services/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');
	//tyabi
	//https://hooks.slack.com/services/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
	//mob34
	//https://hooks.slack.com/services/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,,/

	/*
	$ch = curl_init('https://hooks.slack.com/services/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');

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data_string))
	);

	curl_exec($ch);
	*/
  if($sender=="DYNO_CYCLE")
	   $c = curl_init(DYNO_CYCLE);
  else if($sender=="CYCLE_STATUS")
  	 $c = curl_init(CYCLE_STATUS);
  else if($sender=="CYCLE_WARNINGS")
     $c = curl_init(CYCLE_WARNINGS);


	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($c, CURLOPT_POST, true);
	curl_setopt($c, CURLOPT_POSTFIELDS, array('payload' => json_encode($data)));
	curl_exec($c);
	curl_close($c);
	//echo $data_string;



}

function send_basic_slack_message_as_user($text, $username="asd", $channel="dinocycle") {

	$token="xoxp-4410875088-4410875102-4467220125-7f0a3e";
	$qry_str = "?token=" . $token . "&channel=" . $channel . "&text=" . $text . "&username=" . $username ."&as_user=true&pretty=1";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://slack.com/api/chat.postMessage' . $qry_str);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = trim(curl_exec($ch));


	if($result=='ok')
		return '1';
	else
		return $result;


	curl_close($ch);

}

?>
