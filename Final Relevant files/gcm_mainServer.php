
<?php

require_once 'db_functions.php';
$db = new DB_Functions_GCM();
require_once 'commonutils.php';

//////////////Do some validation First//////////////
$error = 0;



//////exit if error/////
function sendPushNotification($registration_ids,$message) {

	$url = GOOGLE_API_URL;

	$fields = array('registration_ids' => $registration_ids, 'data' => $message, );

	$headers = array('Authorization:key=' . GOOGLE_API_KEY, 'Content-Type: application/json');
	echo json_encode($fields);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

	$result = curl_exec($ch);
	if ($result === false)
		die('Curl failed ' . curl_error());

	curl_close($ch);
	return $result;

}



?>

