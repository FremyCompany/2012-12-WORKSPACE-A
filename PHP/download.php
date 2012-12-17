<?php
	
	error_reporting(E_ALL);
	
	// INCLUDES
	require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");
	
	// you should be connected to initiate a download
	if(!isConnected()) exit;
	
	// output a file
	function transmitFile($file,$filename) {
		if (file_exists($file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			exit;
		} else {
			cThrow(ERR_NOTFOUND);
		}
	}
	
	// get request arguments
	$mode 		= isset($_GET['mode']) 		? $_GET['mode'] 	: cThrow(ERR_ARGS);
	$submode 	= isset($_GET['submode']) 	? $_GET['submode']	: cThrow(ERR_ARGS);
	$user 		= isset($_GET['user'])		? $_GET['user']		: cThrow(ERR_ARGS);
	$file 		= isset($_GET['file'])		? $_GET['file']		: cThrow(ERR_ARGS);
	
	// transmit file
	if($mode=='file') {
		if($submode=='data') {
			transmitFile($_SERVER['DOCUMENT_ROOT'].'/USERS/'.pathEncode($user).'/FILES/'.pathEncode($file).'/.data', $file);
		} else if ($submode=='sign') {
			transmitFile($_SERVER['DOCUMENT_ROOT'].'/USERS/'.pathEncode($user).'/FILES/'.pathEncode($file).'/.sign', $file.".sign");
		} else {
			cThrow(ERR_ARGS);
		}
	} else if($mode=='skey') {
		if($submode=='data') {
			transmitFile($_SERVER['DOCUMENT_ROOT'].'/USERS/'.pathEncode($user).'/KEYS/'.$_SESSION['login'].'/'.pathEncode($file).'/.data', $file.".key");
		} else if ($submode=='sign') {
			transmitFile($_SERVER['DOCUMENT_ROOT'].'/USERS/'.pathEncode($user).'/KEYS/'.$_SESSION['login'].'/'.pathEncode($file).'/.sign', $file.".key.sign");
		} else {
			cThrow(ERR_ARGS);
		}
	}
	
?>