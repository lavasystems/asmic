<?php

	$link = db_mysql_connect();
	if (!$link)
		echo('connection failed<br>' . $MYSQL_ERROR);

	$username = $_SESSION['usr_username'];

	include_once("classes/audit_trail.php");
	$audit_trail = new audit_trail();
	$audit_trail->writeLog($username, "user", "Logout");

	$objUser = new User($_SESSION['usr_id']);
	$login_attempt = $objUser->doLogout();

	session_destroy();
	mysql_close($link);

	header("location: index.php");

	exit();
?>