<?php

///////////////////////////////////////////////////////////////
// We don't store the plain hash, we are a bit more subtle 
// (attackers need to know this to exploit hashes)
///////////////////////////////////////////////////////////////

//
// Crypt a password before sending it to the file
//
function encryptPassword($pwd) {
	return strrev($pwd);
}

//
// Decrypt a password after loading it from the file
//
function decryptPassword($pwd) {
	return strrev($pwd);
}

//
// Get the password of an user
//
function getPasswordOf($login) {
	
	// login should be safe (we perform back-and-forth conversion to be sure)
	$login = pathEncode(pathDecode($login));
	
	// return data
	return decryptPassword(file_get_contents(USERS_FOLDER.$login.'/pass.hash'));
	
}

//
// Set the password of an user
//
function setPasswordOf($login, $pass) {
	
	// login should be safe (we perform back-and-forth conversion to be sure)
	$login = pathEncode(pathDecode($login));
	
	// return data
	return decryptPassword(file_put_contents(USERS_FOLDER.$login.'/pass.hash',encryptPassword($pass)));
	
}


//
// Xor two strings
//
function jXOR($a, $b) {
	$a = array_map('ord', str_split($a));
	$b = array_map('ord', str_split($b));
	$c = "";
	for($i=0;$i<count($a);$i++) {
		if($i<count($b)) {
			$c .= chr($b[$i] ^ $a[$i]);
		} else {
			$c .= chr(129 ^ $a[$i]);
		}
	}
	return $c;
}

//
// Hashes a string
//
function jSHA512($a) {
	return hash('sha512', $a);
}

//
// Check that a folder containing file data and its signature is valid according to a public key
//
function checkFileSign($fileDir, $pubkeyFilePath) {

    // fetch data and signature
    $data = file_get_contents($fileDir.'/.data');
    $sign = file_get_contents($fileDir.'/.sign');
    
    // fetch the public key
    $fp = fopen($pubkeyFilePath,"r+"); $cert = fread($fp, 8192); fclose($fp);
    $pubkeyid = openssl_get_publickey($cert);

    // state whether signature is okay or not
    $ok = openssl_verify($data, $sign, $pubkeyid);

    // free the key from memory
    openssl_free_key($pubkeyid);

    // return the result
    if ($ok !== 1) return FALSE;
    return TRUE;
  
}

//
// Indicates whether the connection attempt is successful or not
//
function checkProof($login, $sess, $proof) {
	
	// load some data
	$a = "".getPasswordOf($login);
	$b = "".$sess;
	
	// generate a third data
	$c = jXOR($a,$b);
	
	// double-sha-512 encode
	return ($proof == jSHA512($c.jSHA512($c.$b)));
	
	
}

?>