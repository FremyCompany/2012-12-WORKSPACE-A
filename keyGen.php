<?php
require_once($_SERVER['DOCUMENT_ROOT']."/PHP/init.php");


$login="Nicolas@20@2098765432";
setPasswordOf($login,jSHA512(file_get_contents(USERS_FOLDER.$login."/"."temp-pass.txt")));
echo pathDecode($login);
?>
