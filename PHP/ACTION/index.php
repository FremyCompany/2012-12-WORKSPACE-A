<?php

// INCLUDES
require_once($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/core.php');

// constants
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
	);
} 

//
// transform back an aribtrary string from a safe file name
//
function pathDecode($str) { 
	return rawurldecode(				// we revert special char encoding
		str_replace("|","%",			// we revert % encoding
			str_replace("||",".",$str)	// we revert . encoding
		)
	);
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
// Indicates whether the user is connected or not
//
function isConnected() {
	return isset($_SESSION['login']);
}

require_once('./crypto.php');

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
		if($login==$_SESSION['login']) {
			return Secloud::getMyUserInfo();
		} else {
			Secloud::disconnect();
		}
		
		// convert argument into valid data
		$login = pathEncode($login);
		
		// check that the user exists
		if(!is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_NOT_FOUND); }
		
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
		
		// ok
		return true;
	}
	
	//
	// Register you on the website
	//
	public static function register($data, $pass) {
		
		// create a login
		$login = pathEncode($data['firstName'].' '.$data['idNumber']);
		
		// check that the login don't exist already
		if(is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_ARGS,'Login déjà utilisé'); }
		
		// create the user folders
		mkdir(USERS_FOLDERS.$login);
        mkdir(USERS_FOLDERS.$login.'/user');
        mkdir(USERS_FOLDERS.$login.'/files');
        mkdir(USERS_FOLDERS.$login.'/'.$login);
		
		// create a password
		$pwd="ok"; //TODO: use a generator!
		file_put_contents('temp-pass.txt',$pwd);
		
		// TODO: check data validity
		//...
		
		// now, save data
		foreach($data as $key=>$value) {
			file_put_contents(USERS_FOLDERS.$login.'/user/'.pathEncode($key).'.txt',$value);
		}
		
		// return ok
		return true;
		
	}
	
	//
	// Revoke your access on the website
	//
	public static function unregister($pass) {
		
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
		
		// create the user folder
		deleteFile(USERS_FOLDERS.$_SESSION['login']);
		
		// return ok
		return true;
		
	}


//
// USERS
//
	
	//
	// Returns a list of all the registered users of the service
	//
	public static function getAllUsers() {
	
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
	
		// initialize data
		$result = array(); $dirpath=USERS_FOLDER;
		$dir = opendir($dirpath);
		
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
		if(!is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_NOT_FOUND); }
		
		// craft the user info
		return array(
			"firstName"	=> file_get_contents(USERS_FOLDERS.$login.'/user/firstName.txt'),
			"lastName"	=> file_get_contents(USERS_FOLDERS.$login.'/user/lastName.txt')
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
			if(!is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_NOT_FOUND); }
			
			// craft the user info
			$_SESSION['user'] = array(
				"firstName"	=> file_get_contents(USERS_FOLDERS.$login.'/user/firstName.txt'),
				"lastName"	=> file_get_contents(USERS_FOLDERS.$login.'/user/lastName.txt'),
				"mail"		=> file_get_contents(USERS_FOLDERS.$login.'/user/mail.txt'),
				"phone"		=> file_get_contents(USERS_FOLDERS.$login.'/user/phone.txt'),
				"address"	=> file_get_contents(USERS_FOLDERS.$login.'/user/address.txt'),
				"idNumber"	=> file_get_contents(USERS_FOLDERS.$login.'/user/idNumber.txt'),
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
		if(!is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_NOT_FOUND); }
		
		// craft the user info
		foreach($data as $key=>$value) {
			file_put_contents(USERS_FOLDERS.$login.'/user/'.pathEncode($key).'.txt',$value);
		}
		
		// return ok
		return true;
		
	}

//
// FILES
//
	
	//
	// Returns a list of all the files you have access from a user
	//
	public static function getFilesFor($login) {
	
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
						
		// convert argument into valid data
		$login = pathEncode($login);
		
		// check that the user exists
		if(!is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_NOT_FOUND); }
		
		// check that data exists for you by this user
		$dirpath=USERS_FOLDERS.$login.'/'.$_SESSION['login'];
		if(!is_dir($dirpath)) { return $result; }	
		
		// initialize data
		$result = array(); 
		$dir = opendir($dirpath);
		
		// walk the key folder
		while (($file = readdir($dir)) !== false) {
		
			// don't consider UNIX virtual paths
			if($file=="."||$file=="..") { continue; }
			
			// check that we only give access to folders
			if (is_dir($dirpath.$file)) {
				
				// check that the linked data files still exist (and are signed)
				if(
                    file_exists($dirpath.$file.'/.data')
                    && file_exists($dirpath.$file.'/.hmac')
                    && file_exists($dirpath.'/../files/'.$file.'/.data')
                    && file_exists($dirpath.'/../files/'.$file.'/.hmac')
                ) {
				
					// append the file to the results
					$result[] = array(pathDecode($file));
					
				}
				
			}
			
			
		}
		
		return $result;

	}
	
	//
	// Returns a list of all the files you have access from a user
	//
	public static function getPublicKeyOf($login) {
	
		// check that you're connected
		if(!isConnected()) { cThrow(ERR_RIGHTS); }
						
		// convert argument into valid data
		$login = pathEncode($login);
		
		// check that the user exists
		if(!is_dir(USERS_FOLDERS.$login)) { cThrow(ERR_NOT_FOUND); }
		
		// check that the file exists
		if(!file_exists(USERS_FOLDERS.$login.'/public.key')) { cThrow(ERR_NOT_FOUND); }
		
		// return its content
		return base64_encode(file_get_contents(USERS_FOLDERS.$login.'/public.key'));
		
	}
	
}

// IF NOT INCLUDE
if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) {

	// ACTIVATE WEBSERVICE
	include($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/json.php');
	if (false) { ?><script><?php }
	?>
/* Module used to load informations about the connected user */
(function() {
    
	function updateData(ok, data, errMessage) {
		try {
			if(ok) {
				if(!window.sCredentials || data[0]!=window.sCredentials[0]) {
					window.sCredentials = data; lastId=null; lastPassword=null;
					sessionStorage.setItem("sCredentials",JSON.stringify(data));
					raiseCustomEvent('connectedUserChanged',{});
				}
			} else {
				switch(data) {
					case 1102: // Licence
						Dialogs.showMessage("Ceci est votre première connexion sur le site web. Vous devez accepter la licence avant de continuer. Vous allez être redirigé vers le site ShareSpace après fermeture de cette boite de dialogue.","Première connexion",function(){
							location.href = "http://sharespace.saint-boni.be";
						}); 
						return;
					case 1105: // Messages
						var message = errMessage.split("|") // [id, "nom", "date", "sujet", "message"]
						message[0] = message[0].split(";")[1];
						Dialogs.open('Message important', [
							{type:'x-text',value:'Veuillez lire ce message important avant de continuer:'},
							//{type:'x-html',value:'<small>De: '+unescape(message[1])+'<br />Le: '+unescape(message[2])+'<br />SUJET: '+unescape(message[3])+'</small>'},
							//{type:'x-text',value:'Le: '+unescape(message[2])},
							//{type:'x-text',value:'SUJET: '+unescape(message[3])},
							{type:'x-html',value:'<div style="padding: 5px; background-color: rgb(200,200,200); overflow: auto; max-height: 250px;"><small style="color: white;">'+unescape(message[1])+', le '+unescape(message[2])+'<br /><b>Concerne: '+unescape(message[3])+'</b></small><div style="margin-top: 5px; max-width: 460px; text-align: justify;">'+unescape(message[4])+'</div></div>'}
						],[
							{type:'button',value:'Annuler',onclick:function(){Dialogs.close();}},
							{type:'button',value:'Continuer',onclick:function(){Secloud.connect(lastId,SHA_ENCODE(lastPassword),parseInt(message[0]),updateData); Dialogs.close();}}
						]); 
						return;
					case 1104:
						// special check for session revival
						if(cookies.getItem("PHPSESSID")!=lastSessID) { 
							login(lastId,lastPassword);
							return;
						}
					default:
						Dialogs.showMessage(errMessage+" (Erreur "+data+")", "Connexion impossible"); 
						return;
				}
			}
		} catch (ex) {}
	}
	
	function fetchData() {
        Secloud.getMyUserInfo(updateData);
    }
	
	var lastId, lastPassword, lastSessID;
	function login(id, password) {
		lastId=id; lastPassword=password; lastSessID=cookies.getItem("PHPSESSID");
		Secloud.connect(lastId,SHA_ENCODE(lastPassword),updateData);
	}
	
	function logout() {
		lastId=null; lastPassword=null;
		Secloud.disconnect();
		updateData(true,window.sCredentialsManager.defaultValue);
	}
	
	window.sCredentialsManager = {
		update: fetchData,  // ()
		login:  login,      // (id,password)
		logout: logout,     // ()
		defaultValue: null
	};
	window.sCredentials = window.sCredentialsManager.defaultValue;
	
    // if cache is empty
    if(!sessionStorage.getItem("sCredentials")) {
	
        // fetch from server using XMLHTTPRequest
		fetchData();
		
    } else {
	
        // else, read data from cache (which takes less time)
        try {
			window.sCredentials = JSON.parse(sessionStorage.getItem("sCredentials")); 
			raiseCustomEvent('connectedUserChanged',{});
			if(!cookies.getItem("PHPSESSID")) { fetchData(); }
		} catch (ex) {
			fetchData();
		}
		
    }
    
	// Regulary update connection status (keep session on server)
    setInterval(fetchData, 30000);
    
})();<?php

}


?>