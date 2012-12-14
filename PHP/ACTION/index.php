<?php
// INCLUDES
const USERS_FOLDER = "../../USERS/";

// Make sure a path is valid
function safeDir($dir) { return str_replace("..","",str_replace("...","",$dir)); }

//
// transform an aribtrary string into a safe file name
//
function pathEncode($str) { 
	return str_replace(".","||",		// we can't leave . because it leads to false file extensions
		str_replace("%","|",			// we can't leave % because it causes issues with URLs
			rawurlencode($str)			// we don't want special chars
		)
	; } 

//
// transform back an aribtrary string from a safe file name
//
function pathDecode($str) { 
	return rawurldecode(				// we revert special char encoding
		str_replace("|","%",			// we revert % encoding
			str_replace("||",".",$str)	// we revert . encoding
		)
	;
}

//
// Recursively delete a folder that's not empty
//
function deleteFile($dir) { 
   if (is_dir($dir)) { 
     $objects = scandir($dir); 
     foreach ($objects as $object) { 
       if ($object != "." && $object != "..") { 
         if (filetype($dir."/".$object) == "dir") deleteFile($dir."/".$object); else unlink($dir."/".$object);
        } 
     } 
     reset($objects); 
     rmdir($dir); 
   } else if(file_exists($dir)) {
     unlink($dir);
   }
}

//
// recursively copy a file or a folder
//
function copyFile($src, $dst) {
   if (file_exists($dst)) deleteFile($dst);
   if (is_dir($src)) {
     mkdir($dst);
     $files = scandir($src);
     foreach ($files as $file)
     if ($file != "." && $file != "..") copyFile("$src/$file", "$dst/$file"); 
   }
   else if (file_exists($src)) copy($src, $dst);
}

//
// Returns whether the user is connected or not
//
public static function isConnected() {
	return isset($_SESSION['login']);
}

// SERVICE
$jsonService = "Secloud"; class Secloud {

	//
	// Returns a list of all the registered users of the service
	//
	public static function connect($login, $proof) {
		
		// if an user is already connected, disconnect first
		if($login==$_SESSION['login']) {
			return Secloud::getMyUserInfo();
		} else {
			Secloud::disconnect();
		}
		
		// convert argument into valid data
		$login = pathEncode($login);
		
		// check that the user exists
		if(!is_dir(USERS_FOLDERS.$login) { cThrow(ERR_NOT_FOUND); }
		
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
	
	public static function disconnect() {
		
		// clean
		unset($_SESSION['login']); unset($_SESSION['user']);
		
		// ok
		return true;
	}

	
	//
	// Returns a list of all the registered users of the service
	//
	public static function getAllUsers() {
	
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
	
		// initialize data
		$result = array();
		$dir = opendir(USERS_FOLDER);
		
		// walk the USERS folder
		while (($file = readdir($dir)) !== false) {
		
			// don't consider UNIX virtual paths
			if($file=="."||$file=="..") { continue; }
			
			// check that we only give access to folders
			if (is_dir($dirpath.$file)) {
				
				// check that the user has been successfully registered
				if(file_exists($dirpath.$file.'/public.key')) {
				
					// append the file to the results
					$result[] = array(pathDecode($file));
					
				}
				
			}
			
			
		}
		
		return $result;

	}

	
	//
	// Returns more information about an user
	//
	public static function getUserInfo($login) {
			
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
		
		// convert argument into valid data
		$login = pathEncode($login);
		
		// check that the user exists
		if(!is_dir(USERS_FOLDERS.$login) { cThrow(ERR_NOT_FOUND); }
		
		// craft the user info
		return array(
			"firstName"	=> file_get_contents(USERS_FOLDERS.$login.'/firstName.txt'),
			"lastName"	=> file_get_contents(USERS_FOLDERS.$login.'/lastName.txt')
		);
		
	}
	
	//
	// Returns more information about the connected user
	//
	private static function getMyUserInfo() {
			
		// check that you're connected
		if(!isConnected()) { return null; }
		
		if(!isset($_SESSION['user'])) {
			
			// convert argument into valid data
			$login = $_SESSION['login'];
			
			// check that the user exists
			if(!is_dir(USERS_FOLDERS.$login) { cThrow(ERR_NOT_FOUND); }
			
			// craft the user info
			$_SESSION['user'] = array(
				"firstName"	=> file_get_contents(USERS_FOLDERS.$login.'/firstName.txt'),
				"lastName"	=> file_get_contents(USERS_FOLDERS.$login.'/lastName.txt'),
				"mail"		=> file_get_contents(USERS_FOLDERS.$login.'/mail.txt'),
				"phone"		=> file_get_contents(USERS_FOLDERS.$login.'/phone.txt'),
				"address"	=> file_get_contents(USERS_FOLDERS.$login.'/address.txt'),
				"idNumber"	=> file_get_contents(USERS_FOLDERS.$login.'/idNumber.txt'),
			);
			
		}
		
		return $_SESSION['user'];
		
	}
	
	//
	// Modify more information about the connected user
	//
	public static function setMyUserInfo($data) {
			
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
		$user = Secloud::
		
		// convert argument into valid data
		$login = pathEncode($login);
		
		// check that the user exists
		if(!is_dir(USERS_FOLDERS.$login) { cThrow(ERR_NOT_FOUND); }
		
		// craft the user info
		foreach($data as $key=>$value) {
			file_put_contents(USERS_FOLDERS.$login.'/'.pathEncode($key).'.txt',$value);
		}
		
		// return ok
		return true;
		
	}

}

// IF NOT INCLUDE
if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) {

	// ACTIVATE WEBSERVICE
	include($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/json.php');

}


?>