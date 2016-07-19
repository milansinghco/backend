<?php

// response json
$json = array();

/**
 * Registering a user device
 * Store reg id in users table
 */
if (isset($_POST["regId"])) {
	$gcm_regid = $_POST["regId"];
        $PhoneIMEI = $_POST["deviceId"];
	// GCM Registration ID
	// Store user details in db
	include_once 'db_functions.php';

	$db = new DB_Functions_GCM();

	if ($db -> checkUserByPhoneIMEI($PhoneIMEI) == true) {
		$res = $db -> deleteUserByPhoneIMEI($PhoneIMEI);

	}
} else {
	// user details missing
}
?>
