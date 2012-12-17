<?php

// INCLUDES
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");

// SERVICE
$jsonService = "Secloud"; class Secloud {

	//
	// CONNECTION
	//

	//
	// Connect to the webservice
	//
	public static function connect($login, $proof) {

		// if an user is already connected, disconnect first
		if(isset($_SESSION['login'])) {
			if($login==$_SESSION['login']) {
				return Secloud::getMyUserInfo();
			} else {
				Secloud::disconnect();
			}
		}

		// convert argument into valid data
		$login = pathEncode($login);

		// check that the user exists
		if(!is_dir(USERS_FOLDER.$login)) {
			cThrow(ERR_BADINFOS);
		}

		// check that the proof is valid
		if(checkProof($login, session_id(), $proof)) {
			 
			// connect the user
			$_SESSION['login'] = $login;
			 
			// return user data
			return Secloud::getMyUserInfo();
			 
		} else {

			// bad infos
			cThrow(ERR_BADINFOS);
			 
		}

	}

	//
	// Disconnect from the webservice
	//
	public static function disconnect() {

		// clean
		unset($_SESSION['login']); unset($_SESSION['user']);

		// delete session and recreate one (just to be sure)
		session_destroy(); session_start();

		// ok
		return null;
	}

	//
	// Register you on the website
	//
	public static function register($data, $pass) {

		// create a login
		$login = pathEncode($data['firstName'].$data['idNumber']);

		// check that the login don't exist already
		if(is_dir(USERS_FOLDER.$login)) {
			cThrow(ERR_ARGS,'Login déjà utilisé'); return false;
		}

		// create the user folders
		mkdir(USERS_FOLDER.$login);
		mkdir(USERS_FOLDER.$login.'/user');
		mkdir(USERS_FOLDER.$login.'/FILES');
		mkdir(USERS_FOLDER.$login.'/KEYS');
		mkdir(USERS_FOLDER.$login.'/'.$login);

		// create a password
		file_put_contents(USERS_FOLDER.$login.'/temp-pass.txt',$pass);

		// TODO: check data validity
		//...

		// now, save data
		foreach($data as $key=>$value) {
			file_put_contents(USERS_FOLDER.$login.'/user/'.pathEncode($key).'.txt',$value);
		}

		// return ok
		return true;

	}

	//
	// Revoke your access on the website
	//
	public static function unregister($pass) {

		// check that you're connected
		if(!isConnected()) {
			cThrow(ERR_RIGHTS);
		}

		// create the user folder
		if(checkProof($_SESSION['login'], session_id(), $pass)) {
			deleteFile(USERS_FOLDER.$_SESSION['login']);
			session_destroy();
			return true;
		} else {
			return false;
		}
		// return ok
	}


	//
	// USERS
	//

	//
	// Returns a list of all the registered users of the service
	//
	public static function getAllUsers() {

		// check that you're connected
		if(!isConnected()) {
			cThrow(ERR_RIGHTS);
		}

		// initialize data
		$result = array(); $dirpath=USERS_FOLDER;
		$dir = opendir($dirpath);

		// walk the USERS folder
		while (($file = readdir($dir)) !== false) {

			// don't consider UNIX virtual paths
			if($file=="."||$file=="..") {
				continue;
			}
			 
			// check that we only give access to folders
			if (is_dir($dirpath.$file)) {

				// check that the user has been successfully registered
				if(file_exists($dirpath.$file.'/public.key')) {

					// append the file to the results
					$result[] = pathDecode($file);
					 
				}

			}
			 
			 
		}

		return $result;

	}
	
	//
	// Returns a list of all the registered users of the service (+their info)
	//
	public static function getAllUsersInfo() {
		return array_map('Secloud::getUserInfo', Secloud::getAllUsers());
	}

	//
	// Returns more information about an user
	//
	public static function getUserInfo($login) {
		 
		// check that you're connected
		if(!isConnected()) {
			cThrow(ERR_RIGHTS);
		}

		// convert argument into valid data
		$login = pathEncode($login);

		// check that the user exists
		if(!is_dir(USERS_FOLDER.$login)) {
			cThrow(ERR_NOTFOUND);
		}

		// craft the user info
		return array(
				"firstName"	=> file_get_contents(USERS_FOLDER.$login.'/user/firstName.txt'),
				"lastName" 	=> file_get_contents(USERS_FOLDER.$login.'/user/lastName.txt'),
				"mail" 		=> file_get_contents(USERS_FOLDER.$login.'/user/mail.txt'),
				"url" 		=> "?page=userPage&user=".urlencode(pathDecode($login))
		);

	}

	//
	// Returns more information about the connected user
	//
	public static function getMyUserInfo() {
		 
		// check that you're connected
		if(!isConnected()) {
			return null;
		}

		if(!isset($_SESSION['user'])) {
			 
			// convert argument into valid data
			$login = $_SESSION['login'];
			 
			// check that the user exists
			if(!is_dir(USERS_FOLDER.$login)) {
				cThrow(ERR_NOTFOUND);
			}
			 
			// craft the user info
			$_SESSION['user'] = array(
					"login"  => pathDecode($_SESSION['login']),
					"firstName" => file_get_contents(USERS_FOLDER.$login.'/user/firstName.txt'),
					"lastName" => file_get_contents(USERS_FOLDER.$login.'/user/lastName.txt'),
					"mail"  => file_get_contents(USERS_FOLDER.$login.'/user/mail.txt'),
					"phone"  => file_get_contents(USERS_FOLDER.$login.'/user/phone.txt'),
					"address" => file_get_contents(USERS_FOLDER.$login.'/user/address.txt'),
					"idNumber" => file_get_contents(USERS_FOLDER.$login.'/user/idNumber.txt'),
			);
			 
		}

		return $_SESSION['user'];

	}

	//
	// Modify more information about the connected user
	//
	public static function setMyUserInfo($data) {
		 
		// check that you're connected
		if(!isConnected()) {
			cThrow(ERR_RIGHTS);
		}
		
		// convert argument into valid data
		$login=pathEncode($_SESSION['login']);
		addLog($login);
		// check that the user exists
		if(!is_dir(USERS_FOLDER.$login)) {
			cThrow(ERR_NOTFOUND);
		}

		// craft the user info
		foreach($data as $key=>$value) {
			file_put_contents(USERS_FOLDER.$login.'/user/'.pathEncode($key).'.txt',$value);
		}
		$_SESSION['user'] = array(
					"login"  => pathDecode($_SESSION['login']),
					"firstName" => file_get_contents(USERS_FOLDER.$login.'/user/firstName.txt'),
					"lastName" => file_get_contents(USERS_FOLDER.$login.'/user/lastName.txt'),
					"mail"  => file_get_contents(USERS_FOLDER.$login.'/user/mail.txt'),
					"phone"  => file_get_contents(USERS_FOLDER.$login.'/user/phone.txt'),
					"address" => file_get_contents(USERS_FOLDER.$login.'/user/address.txt'),
					"idNumber" => file_get_contents(USERS_FOLDER.$login.'/user/idNumber.txt'),
			);
		// return ok
		return true;

	}

	//
	// FILES
	//
	public static function getMyFiles(){
		return Secloud::getFilesFor($_SESSION["login"]);
	}
	
	//
	// Returns a list of all the files you have access from a user
	//
	public static function getFilesFor($login) {
		
		// check that you're connected
		if(!isConnected()) {
			cThrow(ERR_RIGHTS);
		}

		// convert argument into valid data
		$login = pathEncode($login);

		// check that the user exists
		if(!is_dir(USERS_FOLDER.$login)) {
			cThrow(ERR_NOTFOUND);
		}
		$result = array();
		// check that data exists for you by this user
		$dirpath=USERS_FOLDER.$login.'/KEYS/'.$_SESSION['login'];
		if(!is_dir($dirpath)) {
			return $result;
		}

		// initialize data
		$result = array();
		$dir = opendir($dirpath);

		// walk the key folder
		while (($file = readdir($dir)) !== false) {

			// don't consider UNIX virtual paths
			if($file=="."||$file=="..") {
				continue;
			}
			 
			// check that we only give access to folders
			if (is_dir($dirpath."/".$file)) {

				// check that the linked data files still exist (and are signed)
				/*if(
				 file_exists($dirpath.$file.'/.data')
						&& file_exists($dirpath.$file.'/.hmac')
						&& file_exists($dirpath.'/../FILES/'.$file.'/.data')
						&& file_exists($dirpath.'/../FILES/'.$file.'/.hmac')
				) {*/

				// append the file to the results
				$result[] = array("name"=>pathDecode($file));
				 
				//}

			}
			 
			 
		}

		return $result;

	}

	//
	// Returns a list of all the files you have access from a user
	//
	public static function getPublicKeyOf($login) {

		// check that you're connected
		if(!isConnected()) {
			cThrow(ERR_RIGHTS);
		}

		// convert argument into valid data
		$login = pathEncode($login);

		// check that the user exists
		if(!is_dir(USERS_FOLDER.$login)) {
			cThrow(ERR_NOTFOUND);
		}

		// check that the file exists
		if(!file_exists(USERS_FOLDER.$login.'/public.key')) {
			cThrow(ERR_NOTFOUND);
		}

		// return its content
		return base64_encode(file_get_contents(USERS_FOLDER.$login.'/public.key'));

	}

}

// IF NOT INCLUDE
if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) {

	// ACTIVATE WEBSERVICE
	include($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/json.php');
	include($_SERVER['DOCUMENT_ROOT'].'/PHP/ACTION/index2.js');
 
}
 

?>