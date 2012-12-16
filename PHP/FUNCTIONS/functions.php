<?php
function safeDir($dir) {
	return str_replace("..","",str_replace("...","",utf8_decode($dir)));
}


function createDir($pathContainer, $name) {
	$path = safeDir($pathContainer.$name);
	if(!hasPageRight($path)) {
		cThrow(ERR_RIGHTS, $path, "Tentative d'accès à un dossier refusée");
	}
	$path = $_SERVER["DOCUMENT_ROOT"].$path;

	if (is_dir($path)) cThrow(ERR_UNABLE, $path, "Impossible de créer le dossier: le dossier existe déjà");
	if (mkdir($path) === false) cThrow(ERR_UNABLE, $path, "Impossible de créer le dossier à l'emplacement spécifié");
}