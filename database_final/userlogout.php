<?php
	session_start();

	// unset session vars
	$_SESSION = array();

	session_destroy();

	header("location: userlogin.php");
	exit;
?>