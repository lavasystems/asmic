<?php
// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);

// ** START SESSION **
	session_start();

// ** OPEN CONNECTION TO THE DATABASE **
// the configuration is put in the config file
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();

	// ** FIGURE OUT WHAT'S GOING ON
	switch($_GET['mode']) 
	{

		// **LOGOUT **
		case "logout":
			session_destroy();
			require_once('languages/' . $options->language . '.php');			
			// PRINT MESSAGE
			$errorMsg = $lang[MSG_LOGGED_OUT];
			header("Location: " . FILE_INDEX); //required to force site language to override user language at sign in screen
			break;

		// ** AUTHENTICATE A USER
		case "auth":
		
			// LOOK FOR USERNAME AND PASSWORD IN THE DATABASE.
			$usersql = "SELECT username, usertype, password, is_confirmed FROM " . TABLE_USERS . " AS users WHERE username='" . $_POST['username'] . "' AND password=MD5('" . $_POST['password'] . "') LIMIT 1";
			$r_getUser = mysql_query($usersql, $db_link)
				or die(ReportSQLError($usersql));
			$numrows = mysql_num_rows($r_getUser); //fetch the number of rows from table
		    $t_getUser = mysql_fetch_array($r_getUser); //retreive the info from the table as an array
		    
			// THE USERNAME IS FOUND AND ACCOUNT IS CONFIRMED
			if (($numrows != 0) && ($t_getUser['is_confirmed'] == 1)) 
			{
				
				// REGISTER SESSION VARIABLES
				$_SESSION['username'] = $t_getUser['username'];	//optional, can be anything, the user type is the most important
				$_SESSION['usertype'] = $t_getUser['usertype']; // admin or user
				if (!isset($_SESSION['abspath'])) 
				{
					$_SESSION['abspath'] = dirname($_SERVER['SCRIPT_FILENAME']);
				}

				// REDIRECT TO LIST
				header("Location: " . FILE_LIST);
				exit();
				
			}

			// WRONG USERNAME
			else {
				// END SESSION
				session_destroy();
				// PRINT ERROR MESSAGE AND LOGIN SCREEN
				$errorMsg = $lang[MSG_LOGIN_INCORRECT];
			}
			break;
	}
?>