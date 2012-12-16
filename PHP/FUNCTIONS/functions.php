<?php

// Make sure a path is valid
function safeDir($dir) {
	return str_replace("..","",str_replace("...","",$dir));
}

//
// transform an aribtrary string into a safe file name
//
function pathEncode($str) {
	if($str=="") cThrow(ERR_ILLOGICAL);
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
	if($str=="") cThrow(ERR_ILLOGICAL);
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
