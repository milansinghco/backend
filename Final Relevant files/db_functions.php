<?php
require_once 'config.php';

class DB_Functions_GCM {

	//put your code here
	// constructor
	function __construct() {
		// connecting to database
		$GLOBALS['mysqli_connection'] = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE) or die("Mysqli Error " . mysqli_error($GLOBALS['mysqli_connection']));

	}

	// destructor
	function __destruct() {

	}

	public function connectDefaultDatabase() {

		$GLOBALS['mysqli_connection'] = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE) or die("Mysqli Error " . mysqli_error($GLOBALS['mysqli_connection']));
		return $GLOBALS['mysqli_connection'];
	}

	public function selectDatabase($db) {
		mysqli_select_db($GLOBALS['mysqli_connection'], $db);
	}

	public function closeDatabase() {
		mysqli_close($GLOBALS['mysqli_connection']);
	}

	public function connectNewDatabase($host, $user, $password, $dbname = "") {
		closeDatabase();

		if ($dbname != "" && $dbname != null) {
			$GLOBALS['mysqli_connection'] = mysqli_connect($host, $user, $password, $dbname) or die("Mysqli Error " . mysqli_error($GLOBALS['mysqli_connection']));
		} else {$GLOBALS['mysqli_connection'] = mysqli_connect($host, $user, $password) or die("Mysqli Error " . mysqli_error($GLOBALS['mysqli_connection']));
		}

		return $GLOBALS['mysqli_connection'];
	}

	

	public function getUserById($id) {
		$result = mysqli_query($GLOBALS['mysqli_connection'], "SELECT * FROM NOTIFICATION_USERS WHERE id = '$id' LIMIT 1");
		return $result;
	}

	/**
	 * Getting all users
	 */
	public function getAllUsers() {
		$result = mysqli_query($GLOBALS['mysqli_connection'], "select * FROM NOTIFICATION_USERS");
		return $result;
	}

	/**
	 * Check user exists or not
	 */
	public function checkUserById($id) {
		$result = mysqli_query($GLOBALS['mysqli_connection'], "SELECT gcm_regid from NOTIFICATION_USERS WHERE gcm_regid = '$id'");
		$no_of_rows = mysqli_num_rows($result);
		if ($no_of_rows > 0) {
			return true;
		} else {
			return false;
		}
	}


          public function checkUserByPhoneIMEI($PhoneIMEI) {
		$result = mysqli_query($GLOBALS['mysqli_connection'], "SELECT phone from NOTIFICATION_USERS WHERE PhoneIMEI = '$PhoneIMEI'");
		$no_of_rows = mysqli_num_rows($result);
		if ($no_of_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function deleteUserById($id) {
		$result = mysqli_query($GLOBALS['mysqli_connection'], "DELETE FROM NOTIFICATION_USERS WHERE gcm_regid = '$id'");
		if ($result) {
			return true;
		} else {
			return false;
		}

	}

        public function deleteUserByPhoneIMEI($PhoneIMEI) {
		$result = mysqli_query($GLOBALS['mysqli_connection'], "DELETE FROM NOTIFICATION_USERS WHERE PhoneIMEI = '$PhoneIMEI'");
		if ($result) {
			return true;
		} else {
			return false;
		}

	}

	public function storeUser($PhoneIMEI,$gcm_regid, $instanceId, $phone) {
	echo "$gcm_regid";
		// insert user into database
		$result = mysqli_query($GLOBALS['mysqli_connection'], "INSERT INTO NOTIFICATION_USERS(PhoneIMEI,phone, gcm_instance_id, gcm_regid, created_at) VALUES('$PhoneIMEI','$phone', '$instanceId', '$gcm_regid', NOW())");
		
		// check for successful store
		if ($result) {
			// get user details
			//$id = mysqli_insert_id($GLOBALS['mysqli_connection']);
			// last inserted id
			$result = mysqli_query($GLOBALS['mysqli_connection'], "SELECT * FROM NOTIFICATION_USERS WHERE PhoneIMEI = $PhoneIMEI") or die("Error " . mysqli_error($GLOBALS['mysqli_connection']));
			// return user details
			if (mysqli_num_rows($result) > 0) {
				return mysqli_fetch_array($result);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


public function updateUser($PhoneIMEI,$gcm_regid, $instanceId, $Phone) {
	echo "$gcm_regid";
		// insert user into database
		$result = mysqli_query($GLOBALS['mysqli_connection'], "UPDATE NOTIFICATION_USERS SET PhoneIMEI = '".$PhoneIMEI."', phone = '".$Phone."', gcm_instance_id = '".$instanceId."', gcm_regid = '".$gcm_regid."', created_at = NOW() WHERE PhoneIMEI = $PhoneIMEI");
		
		// check for successful store
		if ($result) {
			
			$result = mysqli_query($GLOBALS['mysqli_connection'], "SELECT * FROM NOTIFICATION_USERS WHERE PhoneIMEI = $PhoneIMEI") or die("Error " . mysqli_error($GLOBALS['mysqli_connection']));
			// return user details
			if (mysqli_num_rows($result) > 0) {
				return mysqli_fetch_array($result);
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
?>
