<?
	// ** THIS BEAUTIFUL FUNCTION EXECUTES WHEN EXISTING USER WANTS TO BECOME A MEMBER **

	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);
 	
	$id = $_GET['id'];
	$start = $_GET['start'];
	if (empty($start))
	{
		$start = "contact";
	}
	$book_code = $_GET['bookcode'];
	
	$check = mysql_query("SELECT icnum FROM " . TABLE_CONTACT . " WHERE id = '$id' LIMIT 1", $db_link);
	$t_check = mysql_fetch_array($check);
	$temp_ic = $t_check['icnum'];
	
	if (!empty($temp_ic))
	{ 
		$library = mysql_query("SELECT cards_issued FROM library_settings LIMIT 1", $db_link);
		$t_library= mysql_fetch_array($library);
		$temp_card = $t_library['cards_issued'];
		
		$check_member = mysql_query("SELECT user_id FROM library_member WHERE user_id = '$id'", $db_link);
		$t_check_member= mysql_fetch_array($check_member);
		$temp_user_id = $t_check_member['user_id'];
		
		if (empty($temp_user_id))
		{
			mysql_query("INSERT INTO library_member (contact_id, member_cards, join_date, date_added) VALUES ($id, '$temp_card', '". date("Y-m-d")."', '". date("Y-m-d H:i:s")."')", $db_link);
			//header("Location: ../index.php?mod=user&obj=user&do=add&contact_id=$id&start=$start&bookcode=$book_code");
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=../index.php?mod=user&obj=user&do=add&contact_id=$id&start=$start&bookcode=$book_code\">";
			exit();
		}
		else
		{
			//header("Location: ../index.php?mod=user&obj=user&do=add&contact_id=$id&start=$start&bookcode=$book_code");
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=../index.php?mod=user&obj=user&do=add&contact_id=$id&start=$start&bookcode=$book_code\">";
			exit();
		}		 		
	}
	else
	{	
		header("Location: edit.php?id=$id&pass=confirm&start=$start&bookcode=$book_code&to=to");
		exit();
	}
?>
