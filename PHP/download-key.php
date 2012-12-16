<?php
	
	error_reporting(E_ALL);
	
	// INCLUDES
	require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");
	require_once('./ACTION/crypto.php');
	
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
	$user 		= isset($_GET['user'])		? $_GET['user']		: cThrow(ERR_ARGS);
	
	// transmit file
	transmitFile($_SERVER['DOCUMENT_ROOT'].'/USERS/'.pathEncode($user).'/public.key', "public.key");
	
?>