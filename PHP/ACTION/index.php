<?php
// INCLUDES

// SERVICE
$jsonService = "Actions"; class Actions {


}

// IF NOT INCLUDE
if(realpath($_SERVER["SCRIPT_FILENAME"]) == realpath(__FILE__)) {

	// ACTIVATE WEBSERVICE
	include($_SERVER['DOCUMENT_ROOT'].'/PHP/FUNCTIONS/json.php');

}


?>