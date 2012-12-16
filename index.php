<?php

session_start();
$page_description="Project sécu II";

if(isset($_GET["page"])){
	
	// logged out users can't access the site
	if(!isset($_SESSION['login'])) {
		header('Location: /'); exit;
	}
	
	// the other can
	$page=$_GET["page"];
	$content="TEMPLATE/HTML/".$page.".html";
	
}
else{
	$content="TEMPLATE/HTML/welcome.html";
}
?><!doctype html>
<html xmlns:fb='http://www.facebook.com/2008/fbml'>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Secloud</title>
<meta name="author" content="" />
<meta name="copyright" content="" />
<meta name="email" content="" />
<meta name="description" content="<?php echo $page_description; ?>" />

<link rel="stylesheet"
	href="/MODULES/bootstrap/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="/MODULES/bootstrap/css/bootstrap.css" />

<link rel="stylesheet" href="/TEMPLATE/CSS/main.css" />
</head>
<body>

	<img id="general-background" alt=""
		src="TEMPLATE/PICTURES/Smart-Meter-Security.jpeg">
	<script src="MODULES/bootstrap/js/bootstrap.min.js"
		type="text/javascript"></script>
	<script src="JS/lib.js" type="text/javascript"></script>
	<script src="PHP/ACTION/" type="text/javascript"></script>
	<script src="JS/functions.js" type="text/javascript"></script>
	
	<div id="whole">
		<?php 	include($content);?>
	</div>
	<div style="display: none;" id="addFile"></div>
</body>
</html>
