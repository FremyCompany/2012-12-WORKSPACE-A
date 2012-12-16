<?php

require_once('Streamer.php');
require_once('init.php');


$ft = new File_Upload();
$ft->setDestination($_SERVER['DOCUMENT_ROOT']."/USERS/"."uploads/");
$ft->receive();
