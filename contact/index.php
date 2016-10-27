<?php

// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once('local_config.php');

// ** START SESSION **
	session_start();

// ** OPEN CONNECTION TO THE DATABASE **
// the configuration is put in the config file
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
	if (!isset($_SESSION['abspath'])) 
	{
		$_SESSION['abspath'] = dirname($_SERVER['SCRIPT_FILENAME']);
	}

	// REDIRECT TO LIST
	header("Location: " . FILE_LIST);
	exit();