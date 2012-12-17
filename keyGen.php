<?php
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");

session_destroy();
$login="Jean12345";
setPasswordOf($login,jSHA512(file_get_contents(USERS_FOLDER.$login."/"."temp-pass.txt")));
echo pathDecode($login);
?>
