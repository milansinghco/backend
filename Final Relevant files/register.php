<?php

// response json
$json = array();

/**
 * Registering a user device
 * Store reg id in users table
 */
if (isset($_POST["regId"])) {
	//set default values
	$phone = $_POST["PhoneNo"];
        $PhoneIMEI = $_POST["deviceId"];
	//$email = 'anonymous@anonymous.com';
	$instanceId = '';
	$gcm_regid = $_POST["regId"];
	if(isset($_POST["PhoneNo"])){$phone=$_POST["PhoneNo"];}
	if(isset($_POST["deviceId"])){$PhoneIMEI=$_POST["deviceId"];}
	if(isset($_POST["instanceId"])){$instanceId=$_POST["instanceId"];}
	// GCM Registration ID
	// Store user details in db
	include_once 'db_functions.php';

	$db = new DB_Functions_GCM();
	if ($db -> checkUserById($gcm_regid) == false && $gcm_regid != "" && $gcm_regid != null) {
                     
                 if ($db -> checkUserByPhoneIMEI($PhoneIMEI) ==true){
                         $res = $db -> updateUser($PhoneIMEI,$gcm_regid,$instanceId,$phone);

                        }else{
		
			$res = $db -> storeUser($PhoneIMEI,$gcm_regid,$instanceId,$phone);
                               }
			if(!$res){echo 'Failed to write to database';}
                                


	} else {
	 echo 'Invalid regId';
	}
} else {
 echo 'regId not given';
}
?>
