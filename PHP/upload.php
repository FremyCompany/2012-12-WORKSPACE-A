<?php

require_once('Streamer.php');
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");


$ft = new File_Streamer();
$ft->setDestination('uploads/');
$ft->receive();
