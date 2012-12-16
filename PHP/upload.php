<?php

require_once('Streamer.php');

$ft = new File_Upload();
$ft->setDestination($_SERVER['DOCUMENT_ROOT']."/USERS/".$_SESSION['login']."/");
echo $ft->receive()." bonjour ça va";

?>