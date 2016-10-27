<?php
error_reporting (E_ALL|E_NOTICE); 
	ini_set('display_errors', 1);
// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASSES);
	require_once(FILE_CONTACTNEW);
	include_once('MailMerge.php');

// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	require_once('../includes/functions.php');
	require_once('local_config.php');
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
	
// ** END INITIALIZATION *******************************************************

// using COM is not very fast, if you are merging a lot of letters php will timeout if you dont
// set the max_execution_time configuration.
ini_set('max_execution_time', 600);
ini_set('error_reporting', 'E_ALL');

$mm = new MailMerge();

// create the first merge.

//$res = $db->Query("SELECT id, company, first, last, address1, address2, city, state, zip, country FROM potential_customers");
$res = "SELECT contact_address.line1, contact_address.city, contact_address.state, contact_address.zip, 		
		contact_address.country, contact_address.phone1, contact_address.phone2, contact_contact.id, 	
		contact_contact.fullname, contact_contact.icnum, contact_contact.title, contact_contact.primaryAddress, 
		contact_contact.pictureURL, contact_contact.lastupdate, contact_contact.hidden FROM 
		(contact_contact LEFT JOIN contact_address ON contact_contact.id = contact_address.id)
		WHERE delflag !=1 AND contact_contact.id =218 AND contact_contact.primaryAddress = contact_address.refid";
					
//$mm->SetList( $db->FetchAll($res) );
$querylink = mysql_query($res, $db_link);
//$mm->SetList( $db->FetchAll($querylink) );
$mm->SetList($querylink);

//$numGoTo = mysql_num_rows($querylink);
/**
 * The format here is to pass in an array, the first array parameter should be the name of the 
 * template, the second should be the name you want the .doc saved as
 */
$mm->Template(array('Letter', 'Potential'));

if($mm->Execute()) 
{
	// if the letter merge succeeded create the envelope merge.
	$mm->Template(array('Envelope', 'Potential_Envelope'));
	$mm->Execute();
}

// create the second merge.

echo $res = $db->Query("SELECT contact_address.line1, contact_address.city, contact_address.state, contact_address.zip, 		
		contact_address.country, contact_address.phone1, contact_address.phone2, contact_contact.id, 	
		contact_contact.fullname, contact_contact.icnum, contact_contact.title, contact_contact.primaryAddress, 
		contact_contact.pictureURL, contact_contact.lastupdate, contact_contact.hidden FROM 
		(contact_contact LEFT JOIN contact_address ON contact_contact.id = contact_address.id)
		WHERE delflag !=1 AND contact_contact.id =218 AND contact_contact.primaryAddress = contact_address.refid");
//$mm->setList( $db->FetchAll( $res ) );

/**
 * The format here is to pass in an array, the first array parameter should be the name of the 
 * template, the second should be the name you want the .doc saved as
 */
//$mm->Template(array('Letter1', 'Customers'));
//if($mm->Execute()) {
	// if the letter merge succeeded create the envelope merge.
	//$mm->Template(array('Envelope', 'Customers_Envelope'));
	//$mm->Execute();
//}

$mm = NULL;
echo 'Merge Process Completed!';
?>