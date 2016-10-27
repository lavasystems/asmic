<?php
include_once("../global_config.php");
include_once("../includes/functions.php");
include_once("../includes/database.php");
include_once("../classes/user.php");

$err = array(
		"Unauthorized access."
		);		
if(isset($_REQUEST["err"]) && $_REQUEST["err"] != ""){
	$err_msg = $err[$_REQUEST["err"]];
}

$username = '';
$password = '';
if ($_POST['do'] == 'login'){
	$link = db_mysql_connect();
	if (!$link)
		die('connection failed' . $MYSQL_ERROR);

	$username = $_POST['username'];
	$password = $_POST['password'];
	$objUser = new User();
	$login_attempt = $objUser->doLogin($username, $password);

	if ($login_attempt){
		include_once("../classes/audit_trail.php");
		$audit_trail = new audit_trail();
		$audit_trail->writeLog($username, "user", "Login");
		header("location: ../library/index.php");
		exit();
	}
	else 
	{
		echo 'Login failed.';
	}
	mysql_close($link);
}
?>