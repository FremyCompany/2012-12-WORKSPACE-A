<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/core.php');
require_once($_SERVER['DOCUMENT_ROOT']."/lib/connexion.php");
require_once($_SERVER['DOCUMENT_ROOT']."/js/rights.php");

define('UPLOAD_DIR', "/atelier/");

$jsonService = "SiteExplorer";
function safeDir($dir) { return str_replace("..","",str_replace("...","",utf8_decode($dir))); }

// Recursively delete a folder that's not empty
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
 

class SiteExplorer {

	public static function getAccessibleContent() {
		return Rights::getManagedPages(getConnected());
	}

	public static function getContent($unsafeDir) {
		$dir = safeDir($unsafeDir);
		if(!hasPageRight($dir)) { cThrow(ERR_RIGHTS, $unsafeDir, "Tentative d'accès à un dossier refusée"); }
		$dirpath = $_SERVER['DOCUMENT_ROOT'].$dir;
		
		$result = array();
		$dir = opendir($dirpath);
		while (($file = readdir($dir)) !== false) {
			if($file=="."||$file=="..") { continue; }
			$result[] = array($file,is_dir($dirpath.$file));
		}
		
		return $result;
	}
	
	public static function rename($oldName, $newName) {
		$oldName = safeDir($oldName);
		if(!hasPageRight($oldName)) { cThrow(ERR_RIGHTS, $oldName, "Tentative d'accès à un dossier refusée"); }
		$oldName = $_SERVER['DOCUMENT_ROOT'].$oldName;
		
		$newName = safeDir($newName);
		if(!hasPageRight($newName)) { cThrow(ERR_RIGHTS, $newName, "Tentative d'accès à un dossier refusée"); }
		$newName = $_SERVER['DOCUMENT_ROOT'].$newName;

		if(is_dir($oldName)) {
			copyFile($oldName, $newName);
			deleteFile($oldName);
		} else {
			rename($oldName, $newName);
		}
	}
	
	public static function renameUploaded($oldName, $newName) {
		$oldName = $_SERVER["DOCUMENT_ROOT"].UPLOAD_DIR.safeDir($oldName);
		if(!is_file($oldName) OR substr_count($oldName, "/".$_SESSION['id']."_") == -1)
			cThrow(ERR_UNABLE, $oldName, $oldName."Erreur dans le transfert du fichier uploadé");
		
		$newName = safeDir($newName);
		if(!hasPageRight($newName)) { 
			self::private_del($oldName);
			cThrow(ERR_RIGHTS, $newName, "Tentative d'accès à un dossier refusée"); 
		}
		$newName = $_SERVER['DOCUMENT_ROOT'].$newName;

		if(is_dir($oldName)) {
			copyFile($oldName, $newName);
			deleteFile($oldName);
		} else {
			rename($oldName, $newName);
		}
	}

	public static function delete($unsafeDir) {
		$dir = safeDir($unsafeDir);
		if(!hasPageRight($dir)) { cThrow(ERR_RIGHTS, $unsafeDir, "Tentative d'accès à un dossier refusée"); }
		self::private_del($_SERVER['DOCUMENT_ROOT'].$dir);
		return true;
	}
	
	private static function private_del($safedir) {
		$dir = $safedir;
		
		if (is_dir($dir)) deleteFile($dir);
		elseif (is_file($dir)) unlink($dir);
		else cThrow(ERR_UNABLE, $dir, "Impossible de supprimer l'élément: l'élément n'existe pas");
	}
	
	
	public static function copy($oldName, $newName) {
		$oldName = safeDir($oldName);
		if(!hasPageRight($oldName)) { cThrow(ERR_RIGHTS, $oldname, "Tentative d'accès à un dossier refusée"); }
		$oldName = $_SERVER['DOCUMENT_ROOT'].$oldName;
		
		$newName = safeDir($newName);
		if(!hasPageRight($newName)) { cThrow(ERR_RIGHTS, $newName, "Tentative d'accès à un dossier refusée"); }
		$newName = $_SERVER['DOCUMENT_ROOT'].$newName;

		copyFile($oldName, $newName);
	}
	
	public static function createDir($pathContainer, $name) {
		$path = safeDir($pathContainer.$name);
		if(!hasPageRight($path)) { cThrow(ERR_RIGHTS, $path, "Tentative d'accès à un dossier refusée"); }
		$path = $_SERVER["DOCUMENT_ROOT"].$path;
		
		if (is_dir($path)) cThrow(ERR_UNABLE, $path, "Impossible de créer le dossier: le dossier existe déjà");
		if (mkdir($path) === false) cThrow(ERR_UNABLE, $path, "Impossible de créer le dossier à l'emplacement spécifié");
	}
	
}

if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__) OR (isset ($MIRROR_service) AND $MIRROR_service == strtolower($jsonService))) {
	include($_SERVER['DOCUMENT_ROOT'].'/lib/json.php');
}

?>