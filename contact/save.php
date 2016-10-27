<?php
error_reporting(E_ALL); 
ini_set("display_errors", 0); 
// ** GET CONFIGURATION DATA **
require_once('constants.inc');
require_once(FILE_FUNCTIONS);
require(FILE_LIB_UPLOAD);
require('../includes/clsFileUpload.php');
include_once("local_config.php");
include_once("resizeimage.inc.php");

// ** OPEN CONNECTION TO THE DATABASE **
$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** CHECK FOR ID **;
$memberstatus = 0;
$start = $_GET['start'];
$mode = $_GET['mode'];
$book_code = $_GET['bookcode'];

if ($mode != 'new') 
{
	$id = check_id();
	$whoaddedsql = "SELECT whoAdded FROM " . TABLE_CONTACT . " AS contact WHERE contact.id=$id";
	$tbl_contact = mysql_fetch_array(mysql_query($whoaddedsql, $db_link)) or die(reportSQLError());
	$contact_whoAdded = stripslashes($tbl_contact['whoAdded']);
	if ((($contact_whoAdded != $_SESSION['username']) AND ($_SESSION['usertype'] != 'admin')) OR ($_SESSION['usertype'] == 'guest'))
	{
		$_SESSION = array();
		session_destroy();
		reportScriptError("URL tampering detected. You have been logged out.");
	}
}
else
{	$contact_whoAdded = $_SESSION['username'];	}

// ** AUDIT TRAIL **
include_once("../classes/audit_trail.php");
$audit_trail = new audit_trail();	

// ** END INITIALIZATION *******************************************************

function runQuery($sql) 
{
	global $db_link;
	$result = mysql_query($sql, $db_link) or die(reportSQLError($sql));
	return $result;
}

function optimizeTable($table) 
{
	global $db_link;
	mysql_query("OPTIMIZE TABLE $table", $db_link) or die();
}

$mode = $_GET['mode'];

if (($mode != 'new') && ($mode != 'edit') && ($mode != 'delete')) 
{	header("Location: list.php");	}


if ( ($mode == 'new') || ($mode == 'edit') )
{
	$checkname = trim($_POST['fullname']);
	$checkic = trim($_POST['icnum']); 
	$checkemail = $_POST['email'];
	$checkemail2 = $_POST['email2'];
	$checkemail3 = $_POST['email3'];
	$checkpostcode0 = $_POST['address_zip_0'];
	$checkpostcode1 = $_POST['address_zip_1'];
	$checkpostcode2 = $_POST['con_zip'];
	$checkcity0 = $_POST['address_city_0'];
	$checkcity1 = $_POST['address_city_1'];
	$checkcity2 = $_POST['con_city'];
	$checkstate0 = ($_POST['address_state_0']);
	$checkstate1 = ($_POST['address_state_1']);
	$checkstate2 = ($_POST['address_state_2']);
	$checkcontribution = $_POST['committee'];
	$checkpos = $_POST['position'];
	
	$tempname = ($_POST['fullname']);
	$temptitle = ($_POST['title']);
	$tempic = ($_POST['icnum']);
	$tempemail = ($_POST['email']);
	$tempemail2 = ($_POST['email2']);
	$tempemail3 = ($_POST['email3']);			
	$tempaddress = ($_POST['address_line1_0']);
	$tempcity = ($_POST['address_city_0']);
	$tempstate = ($_POST['address_state_0']);
	$tempzip = ($_POST['address_zip_0']);
	$tempphone1 = ($_POST['address_phone1_0']);
	$tempphone2 = ($_POST['address_phone2_0']);
	$tempphone3 = ($_POST['address_phone3_0']);
	$tempfax1 = ($_POST['address_fax1_0']);
	$tempfax2 = ($_POST['address_fax2_0']);
	$tempcountry = ($_POST['address_country_0']);

	$tempaddress1 = ($_POST['address_line1_1']);
	$tempcity1 = ($_POST['address_city_1']);
	$tempstate1 = ($_POST['address_state_1']);
	$tempzip1 = ($_POST['address_zip_1']);
	$tempphone11 = ($_POST['address_phone1_1']);
	$tempphone21 = ($_POST['address_phone2_1']);
	$tempphone31 = ($_POST['address_phone3_1']);
	$tempfax11 = ($_POST['address_fax1_1']);
	$tempfax21 = ($_POST['address_fax2_1']);
	$tempcountry2 = ($_POST['address_country_1']);
	
	$confaddress = $_POST['con_line1'];
	$confcity = $_POST['con_city'];
	$confstate = $_POST['con_state'];
	$confzip = $_POST['con_zip'];
	$confphone1 = $_POST['con_phone1'];
	$confphone2 = $_POST['con_phone2'];
	$confphone3 = $_POST['con_phone3'];
	$confcountry = ($_POST['con_country']);
	
	$contribution = $_POST['committee'];
	$contribution_pos = $_POST['position'];
	$contribution_ye = $_POST['year'];
	
	$med_fax = $_POST['m_fax'];
	$med_email = $_POST['m_email'];
	$med_post = $_POST['m_post'];
	$med_hand = $_POST['m_hand'];
	$med_inform = $_POST['m_inform'];
	$med_invite = $_POST['m_invite'];
	
	$checkarea = $_POST['areas'];
	$checkgroups = $_POST['groups'];
		
	$i = 0;
	while (list ($x_key, $x_gid) = each ($checkarea)) 
	{
		$areaid[$i] = $x_gid;
		$i++;
	}
	
	while (list ($x_key, $x_gid) = each ($checkgroups)) 
	{	$categoryid = $x_gid;	}

	$membercheck = $_POST['membercheck'];
	
	//***********	BEGIN CHECKING FOR INVALID CHARACTERS. DATA IS SAVED INTO HIDDEN FIELDS AND 
	//***********	WILL BE SUMBITTED BACK IF THERE'S ERROR
	if (empty($checkname))
	{
		$errormessage = "The character you have entered is invalid. Please fill in a valid full name.";
		require_once('prefill.php');	
	}

	if (account_namevalid($checkname) == false)
	{
		$errormessage = "The character you have entered is invalid. Please fill in a valid full name.";
		require_once('prefill.php');	
	}

	if (account_namevalid($checkic) == false)
	{
		$errormessage = "The character you have entered is invalid. Please fill in a valid IC number.";
		require_once('prefill.php');	
	}
	
	if (!empty($checkemail))
	{
		if (validate_email($checkemail) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid email address.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkemail2))
	{
		if (validate_email($checkemail2) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid email address.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkemail3))
	{
		if (validate_email($checkemail3) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid email address.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkpostcode0))
	{
		if (account_namevalid($checkpostcode0) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid postcode.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkpostcode1))
	{
		if (account_namevalid($checkpostcode1) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid postcode.";
			require_once('prefill.php');
		}
	}

	if (!empty($checkpostcode2))
	{
		if (account_namevalid($checkpostcode2) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid postcode.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkcity0))
	{
		if (account_namevalid($checkcity0) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid city.";
			require_once('prefill.php');
		}
	}	
	
	if (!empty($checkcity1))
	{
		if (account_namevalid($checkcity1) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid city.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkcity2))
	{
		if (account_namevalid($checkcity2) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid city.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkstate0))
	{
		if (account_namevalid($checkstate0) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid state.";
			require_once('prefill.php');
		}
	}	
	
	if (!empty($checkstate1))
	{
		if (account_namevalid($checkstate1) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid state.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkstate2))
	{
		if (account_namevalid($checkstate2) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid state.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkcontribution))
	{
		if (account_namevalid($checkcontribution) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid contribution.";
			require_once('prefill.php');
		}
	}
	
	if (!empty($checkpos))
	{
		if (account_namevalid($checkpos) == false)
		{
			$errormessage = "The character you have entered is invalid. Please fill in a valid position.";
			require_once('prefill.php');
		}
	}	
}

if ($mode == 'new')
{
	// ** WHEN NEW CONTACTS ARE CREATED, IT CHECKS FOR DUPLICATE IC NUMBER **
	if ($_POST['icnum'])
	{
		$noic = $_POST['icnum'];
		$checkic = mysql_query("SELECT icnum, id FROM contact_contact where icnum = $noic and delflag != 1 LIMIT 1", $db_link);
		$t_icnum = mysql_fetch_array($checkic);
		$temp_icnum = $t_icnum['icnum'];
		$temp_id = $t_icnum['id'];
		
		if (!empty($temp_icnum))
		{			
			include_once($app_absolute_path."includes/template_header.php");
			?>
			<br><br><br><br><br><br>
			<table width="300" align="center" border="1" cellpadding="0" cellspacing="0" bordercolor="#FF0000">
			<tr>
			<td>
			<table width="100%" align="center">
			<tr> 
			<td class="ar11_content" colspan="2"> 
      		<?
			require_once($app_absolute_path . "classes/user.php");
			$objUser = new User();
			$isUser = $objUser->isUser($temp_id);
			if ($isUser > 0)
			{	$status = "Member";	}
			else 
			{	$status = "Non-member";	}
			
			if ($status == "Member")
			{	
				echo "<br><div align=\"center\">&nbsp;This IC number ($tempic) is already used by an existing member</div><br>";
				if ($start == 'contact')
				{
					if ($mode == 'edit')
					{
						$action = "edit.php?id=$id";
						$button = "../images/m5/m5_btn_back.gif";
					}
					else
					{
						$action = "edit.php?mode=new";
						$button = "../images/m5/m5_btn_back.gif";
					}
				}
				else
				{
					$action = "../index.php?mod=user&obj=user";
					$button = "../images/m5/m5_btn_back.gif";
				}
			}			
			
			if ($status == "Non-member")
			{
				echo "<br><div align=\"center\">&nbsp;We have found an existing contact which is having the same IC number ($tempic). \nDo you want to convert this contact to user?</div><br>";
				
				if ($start == 'contact')
				{
					// if tekan button yes
					$action = "../index.php?mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
					$button = "../images/m5/yes.gif";
					// if tekan button cancel
					if ($mode == 'edit')
					{	$cancelaction = "edit.php?id=$id";	}
					else
					{	$cancelaction = "edit.php?mode=new";	}					
				}
				else
				{
					$action = "../index.php?mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
					$button = "../images/m5/yes.gif";
					$cancelaction = "../index.php?mod=user&obj=user";
				}
			}
			?>
    </td>
  </tr>
  <tr> 
    <td> 
	<form method="post" action="<? echo $action; ?>" enctype="multipart/form-data">
        <table width="150" border="0">
          <tr> 
            <td align="center"> <input name="fullname" type="hidden" value="<? echo $tempname; ?>"> 
              <input name="title" type="hidden" value="<? echo $temptitle; ?>"> 
              <input name="icnum" type="hidden" value="<? echo $tempic; ?>"> 
			  <input name="email" type="hidden" value="<? echo $tempemail; ?>"> 
			  <input name="email2" type="hidden" value="<? echo $tempemail2; ?>"> 
			  <input name="email3" type="hidden" value="<? echo $tempemail3; ?>"> 
              <input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>"> 
              <input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>"> 
              <input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>"> 
              <input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>"> 
              <input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>"> 
              <input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>"> 
			  <input name="address_phone3_0" type="hidden" value="<? echo $tempphone3 ?>"> 
			  <input name="address_fax1_0" type="hidden" value="<? echo $tempfax1 ?>"> 
			  <input name="address_fax2_0" type="hidden" value="<? echo $tempfax2 ?>"> 
              <input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>"> 
              <input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>"> 
              <input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>"> 
              <input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>"> 
              <input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>"> 
              <input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>"> 
              <input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>"> 
			  <input name="address_phone3_1" type="hidden" value="<? echo $tempphone31; ?>"> 
			  <input name="address_fax1_1" type="hidden" value="<? echo $tempfax11 ?>"> 
			  <input name="address_fax2_1" type="hidden" value="<? echo $tempfax21 ?>"> 
              <input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>"> 
              <input name="con_line1" type="hidden" value="<? echo $confaddress; ?>"> 
              <input name="con_city" type="hidden" value="<? echo $confcity; ?>"> 
              <input name="con_state" type="hidden" value="<? echo $confstate; ?>"> 
              <input name="con_zip" type="hidden" value="<? echo $confzip; ?>"> 
              <input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>"> 
              <input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>"> 
			  <input name="con_phone3" type="hidden" value="<? echo $confphone3; ?>"> 
              <input name="address_country" type="hidden" value="<? echo $confcountry; ?>"> 
              <?
				$j = 0;
				while ($j < $i)
				{
					echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
					$j++;
				}	
				?>
              <input type="hidden" name="selectcat" value="<? echo $categoryid; ?>"> 
              <input type="hidden" name="committee" value="<? echo $contribution; ?>"> 
              <input type="hidden" name="position" value="<? echo $contribution_pos; ?>"> 
              <input type="hidden" name="year" value="<? echo $contribution_ye; ?>"> 
	      <input type="hidden" name="medium_fax" value="<? echo $med_fax; ?>">
	      <input type="hidden" name="medium_email" value="<? echo $med_email; ?>">
	      <input type="hidden" name="medium_hand" value="<? echo $med_hand; ?>">
	      <input type="hidden" name="medium_post" value="<? echo $med_post; ?>">
	      <input type="hidden" name="medium_inform" value="<? echo $med_inform; ?>">
	      <input type="hidden" name="medium_invite" value="<? echo $med_invite; ?>">
              <input type="hidden" name="membercheck" value="<? echo $membercheck; ?>"> 
              <input type="image" src="<? echo $button; ?>"> <input type="hidden" name="Submit" value="Submit"> 
            </td>
          </tr>
        </table>
      </form>
	  </td>
    <td> 
	<form method="post" action="<? echo $cancelaction; ?>" enctype="multipart/form-data">
        <table width="150">
          <tr> 
            <td align="center"> 
			<input name="fullname" type="hidden" value="<? echo $tempname; ?>"> 
              <input name="title" type="hidden" value="<? echo $temptitle; ?>"> 
              <input name="icnum" type="hidden" value="<? echo $tempic; ?>"> 
			  <input name="email" type="hidden" value="<? echo $tempemail; ?>"> 
			  <input name="email2" type="hidden" value="<? echo $tempemail2; ?>"> 
			  <input name="email3" type="hidden" value="<? echo $tempemail3; ?>"> 
              <input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>"> 
              <input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>"> 
              <input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>"> 
              <input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>"> 
              <input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>"> 
              <input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>"> 
			  <input name="address_phone3_0" type="hidden" value="<? echo $tempphone3 ?>"> 
			  <input name="address_fax1_0" type="hidden" value="<? echo $tempfax1 ?>"> 
			  <input name="address_fax2_0" type="hidden" value="<? echo $tempfax2 ?>"> 
              <input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>"> 
              <input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>"> 
              <input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>"> 
              <input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>"> 
              <input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>"> 
              <input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>"> 
              <input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>"> 
			  <input name="address_phone3_1" type="hidden" value="<? echo $tempphone31; ?>"> 
			  <input name="address_fax1_1" type="hidden" value="<? echo $tempfax11 ?>"> 
			  <input name="address_fax2_1" type="hidden" value="<? echo $tempfax21 ?>"> 
              <input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>"> 
              <input name="con_line1" type="hidden" value="<? echo $confaddress; ?>"> 
              <input name="con_city" type="hidden" value="<? echo $confcity; ?>"> 
              <input name="con_state" type="hidden" value="<? echo $confstate; ?>"> 
              <input name="con_zip" type="hidden" value="<? echo $confzip; ?>"> 
              <input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>"> 
              <input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>"> 
			  <input name="con_phone3" type="hidden" value="<? echo $confphone3; ?>"> 
              <input name="address_country" type="hidden" value="<? echo $confcountry; ?>"> 
              <?
				$j = 0;
				while ($j < $i)
				{
					echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
					$j++;
				}	
				?>
              <input type="hidden" name="selectcat" value="<? echo $categoryid; ?>"> 
              <input type="hidden" name="committee" value="<? echo $contribution; ?>"> 
              <input type="hidden" name="position" value="<? echo $contribution_pos; ?>"> 
              <input type="hidden" name="year" value="<? echo $contribution_ye; ?>"> 
	      <input type="hidden" name="medium_fax" value="<? echo $med_fax; ?>">
	      <input type="hidden" name="medium_email" value="<? echo $med_email; ?>">
	      <input type="hidden" name="medium_hand" value="<? echo $med_hand; ?>">
	      <input type="hidden" name="medium_post" value="<? echo $med_post; ?>">
	      <input type="hidden" name="medium_inform" value="<? echo $med_inform; ?>">
	      <input type="hidden" name="medium_invite" value="<? echo $med_invite; ?>">
              <input type="hidden" name="membercheck" value="<? echo $membercheck; ?>"> 
              <?
			if ($status == "Non-member")
			{
				echo "<input type=\"image\" src=\"../images/m5/m5_btn_cancel.gif\">";
				echo "<input type=\"hidden\" name=\"Submit\" value=\"Submit\">";
			}
			?>
            </td>
          </tr>
        </table>
      </form>
	  </tr>
	  </td>
</table>
</td>
</tr>
</table>
		<?
			include_once($app_absolute_path."includes/template_footer.php");
			exit();
		}	
	}
}

if (($mode == 'new') || ($mode == 'edit'))
{
	// ** WHEN NEW CONTACS ARE ADDED, IT CHECKS FOR THE FULLNAME AND THE PRIMARY ADDRESS ** 
	if ((empty($_POST['fullname'])) || (empty($_POST['address_line1_0'])))
	{
		include_once($app_absolute_path."includes/template_header.php");
		?>
		<br><br><br><br><br><br>		
		<table width="300" align="center" border="1" cellpadding="1" cellspacing="0" bordercolor="#FF0000">
		<tr>
			<td class="ar11_content">
			<div align="center">Full name or Address field is empty. Please fill in all mandatory fields which are marked by the red color asterisks (<font color="#FF0000">*</font>).</div>
		<?
		// ** CHECKS THE $ID, IF $ID EXIST, THAT MEANS THE USER IS EDITING, 
		// IF $ID NOT EXIST, IT MEANS NEW USER IS BEING CREATED ** 
		if (!empty($id))
		{	$action = "edit.php?id=$id&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";	}
		else
		{	$action = "edit.php?mode=new&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";	}
		?>
			<form method="post" action="<? echo $action; ?>" enctype="multipart/form-data">
			<table align="center">
			<tr>
				<td>
				<input name="fullname" type="hidden" value="<? echo $tempname; ?>">
				<input name="title" type="hidden" value="<? echo $temptitle; ?>">
				<input name="icnum" type="hidden" value="<? echo $tempic; ?>">
				<input name="email" type="hidden" value="<? echo $tempemail; ?>">
				<input name="email2" type="hidden" value="<? echo $tempemail2; ?>"> 
			    <input name="email3" type="hidden" value="<? echo $tempemail3; ?>"> 
				<input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>">
				<input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>">
				<input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>">
				<input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>">
				<input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>">
				<input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>">
				<input name="address_phone3_0" type="hidden" value="<? echo $tempphone3 ?>">
				<input name="address_fax1_0" type="hidden" value="<? echo $tempfax1 ?>"> 
			    <input name="address_fax2_0" type="hidden" value="<? echo $tempfax2 ?>"> 
				<input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>">
				
				<input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>">
				<input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>">
				<input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>">
				<input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>">
				<input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>">
				<input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>">
				<input name="address_phone3_1" type="hidden" value="<? echo $tempphone31; ?>">
				<input name="address_fax1_1" type="hidden" value="<? echo $tempfax11 ?>"> 
			    <input name="address_fax2_1" type="hidden" value="<? echo $tempfax21 ?>"> 
				<input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>">

				<input name="con_line1" type="hidden" value="<? echo $confaddress; ?>">
				<input name="con_city" type="hidden" value="<? echo $confcity; ?>">
				<input name="con_state" type="hidden" value="<? echo $confstate; ?>">
				<input name="con_zip" type="hidden" value="<? echo $confzip; ?>">
				<input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>">
				<input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>">
				<input name="con_phone3" type="hidden" value="<? echo $confphone3; ?>">
				<input name="address_country" type="hidden" value="<? echo $confcountry; ?>">
				<?
				$j = 0;
				while ($j < $i)
				{
					echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
					$j++;
				}	
				?>
				<input type="hidden" name="selectcat" value="<? echo $categoryid; ?>">
				
				<input type="hidden" name="committee" value="<? echo $contribution; ?>">
				<input type="hidden" name="position" value="<? echo $contribution_pos; ?>">
				<input type="hidden" name="year" value="<? echo $contribution_ye; ?>">
				<input type="hidden" name="medium_fax" value="<? echo $med_fax; ?>">
			      <input type="hidden" name="medium_email" value="<? echo $med_email; ?>">
			      <input type="hidden" name="medium_hand" value="<? echo $med_hand; ?>">
			      <input type="hidden" name="medium_post" value="<? echo $med_post; ?>">
			      <input type="hidden" name="medium_inform" value="<? echo $med_inform; ?>">
			      <input type="hidden" name="medium_invite" value="<? echo $med_invite; ?>">
				<input type="hidden" name="membercheck" value="<? echo $membercheck; ?>">
				<input type="image" src="../images/m5/m5_btn_back.gif">
				<input type="hidden" name="Submit" value="Submit">
				</td>
				</tr>
				</table>
				</form>
				</td>
				</tr>
				</table>
			
		<?
		include_once($app_absolute_path."includes/template_footer.php");
		exit();	
	}

	// ** CHECKS FOR THE BECOME MEMBER BUTTON, IF CHECK, THEN EXECUTE THIS COMMAND **
	if ($_POST['membercheck'] == "checking")
	{
		// ** CHECKS WHETHER ICNUM IS FILLED, THEN EXECUTE CHECKING FOR DUPLICATE ICNUM ** 
		$nokad = $_POST['icnum'];
		
		if ( !empty($nokad) )
		{
			$checkic = mysql_query("SELECT icnum FROM contact_contact where icnum = $nokad and delflag != 1 LIMIT 1", $db_link);
			$t_icnum = mysql_fetch_array($checkic);
			$temp_icnum = $t_icnum['icnum'];
			
			if ( !empty($temp_icnum) )
			{
				include_once($app_absolute_path."includes/template_header.php");
				?>
				<!--HERE START-->
				<br><br><br><br><br><br>
			<table width="300" align="center" border="1" cellpadding="0" cellspacing="0" bordercolor="#FF0000">
			<tr>
			<td>
			<table width="100%" align="center">
			<tr> 
			<td class="ar11_content" colspan="2"> 
      		<?
			require_once($app_absolute_path . "classes/user.php");
			$objUser = new User();
			$isUser = $objUser->isUser($temp_id);
			if ($isUser > 0)
			{
				$status = "Member";
			}
			else 
			{
				$status = "Non-member";
			}
			
			
			if ($status == "Member")
			{
				
				echo "<br><div align=\"center\">&nbsp;This IC number ($tempic) is already used by an existing member</div><br>";
				if ($start == 'contact')
				{
					if ($mode == 'edit')
					{
						$action = "edit.php?id=$id&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
						$button = "../images/m5/m5_btn_back.gif";
					}
					else
					{
						$action = "edit.php?mode=new&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
						$button = "../images/m5/m5_btn_back.gif";
					}
				}
				else
				{
					$action = "../index.php?mod=user&obj=user";
					$button = "../images/m5/m5_btn_back.gif";
				}
			}			
			
			if ($status == "Non-member")
			{
				echo "<br><div align=\"center\">&nbsp;We have found an existing contact which is having the same IC number ($tempic). \nDo you want to convert this contact to user?</div><br>";
				
				if ($start == 'contact')
				{
					// if yes
					$action = "../index.php?mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
					$button = "../images/m5/yes.gif";
					// if cancel
					if ($mode == 'edit')
					{
						$cancelaction = "edit.php?id=$id&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
					}
					else
					{
						$cancelaction = "edit.php?mode=new&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
					}					
				}
				else
				{
					$action = "../index.php?mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
					$button = "../images/m5/yes.gif";
					$cancelaction = "../index.php?mod=user&obj=user";
				}
			}
			?>
    </td>
  </tr>
  <tr> 
    <td> 
	<form method="post" action="<? echo $action; ?>" enctype="multipart/form-data">
        <table width="150" border="0">
          <tr> 
            <td align="center"> <input name="fullname" type="hidden" value="<? echo $tempname; ?>"> 
              <input name="title" type="hidden" value="<? echo $temptitle; ?>"> 
              <input name="icnum" type="hidden" value="<? echo $tempic; ?>"> 
			  <input name="email" type="hidden" value="<? echo $tempemail; ?>"> 
			  <input name="email2" type="hidden" value="<? echo $tempemail2; ?>"> 
			  <input name="email3" type="hidden" value="<? echo $tempemail3; ?>"> 
              <input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>"> 
              <input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>"> 
              <input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>"> 
              <input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>"> 
              <input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>"> 
              <input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>"> 
			  <input name="address_phone3_0" type="hidden" value="<? echo $tempphone3 ?>">
			  <input name="address_fax1_0" type="hidden" value="<? echo $tempfax1 ?>"> 
			  <input name="address_fax2_0" type="hidden" value="<? echo $tempfax2 ?>"> 
              <input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>"> 
              <input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>"> 
              <input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>"> 
              <input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>"> 
              <input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>"> 
              <input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>"> 
              <input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>"> 
			  <input name="address_phone3_1" type="hidden" value="<? echo $tempphone31; ?>">
			  <input name="address_fax1_1" type="hidden" value="<? echo $tempfax11 ?>"> 
			  <input name="address_fax2_1" type="hidden" value="<? echo $tempfax21 ?>">  
              <input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>"> 
              <input name="con_line1" type="hidden" value="<? echo $confaddress; ?>"> 
              <input name="con_city" type="hidden" value="<? echo $confcity; ?>"> 
              <input name="con_state" type="hidden" value="<? echo $confstate; ?>"> 
              <input name="con_zip" type="hidden" value="<? echo $confzip; ?>"> 
              <input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>"> 
              <input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>"> 
			  <input name="con_phone3" type="hidden" value="<? echo $confphone3; ?>">
              <input name="address_country" type="hidden" value="<? echo $confcountry; ?>"> 
              <?
				$j = 0;
				while ($j < $i)
				{
					echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
					$j++;
				}	
				?>
              <input type="hidden" name="selectcat" value="<? echo $categoryid; ?>"> 
              <input type="hidden" name="committee" value="<? echo $contribution; ?>"> 
              <input type="hidden" name="position" value="<? echo $contribution_pos; ?>"> 
              <input type="hidden" name="year" value="<? echo $contribution_ye; ?>"> 
	      <input type="hidden" name="medium_fax" value="<? echo $med_fax; ?>">
	      <input type="hidden" name="medium_email" value="<? echo $med_email; ?>">
	      <input type="hidden" name="medium_hand" value="<? echo $med_hand; ?>">
	      <input type="hidden" name="medium_post" value="<? echo $med_post; ?>">
	      <input type="hidden" name="medium_inform" value="<? echo $med_inform; ?>">
	      <input type="hidden" name="medium_invite" value="<? echo $med_invite; ?>">
              <input type="hidden" name="membercheck" value="<? echo $membercheck; ?>"> 
              <input type="image" src="<? echo $button; ?>"> <input type="hidden" name="Submit" value="Submit"> 
            </td>
          </tr>
        </table>
      </form>
	  </td>
    <td> 
	<form method="post" action="<? echo $cancelaction; ?>" enctype="multipart/form-data">
        <table width="150">
          <tr> 
            <td align="center"> 
			<input name="fullname" type="hidden" value="<? echo $tempname; ?>"> 
              <input name="title" type="hidden" value="<? echo $temptitle; ?>"> 
              <input name="icnum" type="hidden" value="<? echo $tempic; ?>"> 
			  <input name="email" type="hidden" value="<? echo $tempemail; ?>">
			  <input name="email2" type="hidden" value="<? echo $tempemail2; ?>"> 
			  <input name="email3" type="hidden" value="<? echo $tempemail3; ?>">  
              <input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>"> 
              <input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>"> 
              <input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>"> 
              <input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>"> 
              <input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>"> 
              <input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>">
			  <input name="address_phone3_0" type="hidden" value="<? echo $tempphone3 ?>"> 
			  <input name="address_fax1_0" type="hidden" value="<? echo $tempfax1 ?>"> 
			  <input name="address_fax2_0" type="hidden" value="<? echo $tempfax2 ?>"> 
              <input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>"> 
              <input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>"> 
              <input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>"> 
              <input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>"> 
              <input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>"> 
              <input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>"> 
              <input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>">
			  <input name="address_phone3_1" type="hidden" value="<? echo $tempphone31; ?>"> 
			  <input name="address_fax1_1" type="hidden" value="<? echo $tempfax11 ?>"> 
			  <input name="address_fax2_1" type="hidden" value="<? echo $tempfax21 ?>"> 
              <input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>"> 
              <input name="con_line1" type="hidden" value="<? echo $confaddress; ?>"> 
              <input name="con_city" type="hidden" value="<? echo $confcity; ?>"> 
              <input name="con_state" type="hidden" value="<? echo $confstate; ?>"> 
              <input name="con_zip" type="hidden" value="<? echo $confzip; ?>"> 
              <input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>"> 
              <input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>">
			  <input name="con_phone3" type="hidden" value="<? echo $confphone3; ?>"> 
              <input name="address_country" type="hidden" value="<? echo $confcountry; ?>"> 
              <?
				$j = 0;
				while ($j < $i)
				{
					echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
					$j++;
				}	
				?>
              <input type="hidden" name="selectcat" value="<? echo $categoryid; ?>"> 
              <input type="hidden" name="committee" value="<? echo $contribution; ?>"> 
              <input type="hidden" name="position" value="<? echo $contribution_pos; ?>"> 
              <input type="hidden" name="year" value="<? echo $contribution_ye; ?>"> 
	      <input type="hidden" name="medium_fax" value="<? echo $med_fax; ?>">
	      <input type="hidden" name="medium_email" value="<? echo $med_email; ?>">
	      <input type="hidden" name="medium_hand" value="<? echo $med_hand; ?>">
	      <input type="hidden" name="medium_post" value="<? echo $med_post; ?>">
	      <input type="hidden" name="medium_inform" value="<? echo $med_inform; ?>">
	      <input type="hidden" name="medium_invite" value="<? echo $med_invite; ?>">
              <input type="hidden" name="membercheck" value="<? echo $membercheck; ?>"> 
              <?
			if ($status == "Non-member")
			{
				echo "<input type=\"image\" src=\"../images/m5/m5_btn_cancel.gif\">";
				echo "<input type=\"hidden\" name=\"Submit\" value=\"Submit\">";
			}
			?>
            </td>
          </tr>
        </table>
      </form>
	  </tr>
	  </td>
</table>
</td>
</tr>
</table>
		<?
				include_once($app_absolute_path."includes/template_footer.php");
				$member = 1;
				exit();
			}
		}
		
		// ** IF THE ICNUMBER IS NOT ENTERED AND USER WANTS TO BE A MEMBER, PROMPT FOR IC NUMBER **
		if ($_POST['icnum'] == "")
		{
			include_once($app_absolute_path."includes/template_header.php");
			if (!empty($id))
			{
				//$action = "edit.php?id=$id";
				$action = "edit.php?id=$id&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
			}
			else
			{
				$action = "edit.php?mode=new&mod=user&obj=user&do=add&contact_id=".$temp_id."&start=".$start."&bookcode=$book_code";
			}
			?>
			<br><br><br><br><br><br>
			<form method="post" action="<? echo $action; ?>" enctype="multipart/form-data">
			<table width="300" align="center" border="1" cellpadding="1" cellspacing="0" bordercolor="#FF0000">
			<tr>
			<td class="ar11_content">
			<div align="center">
			IC Number / Passport field is empty. Please provide IC Number / Passport in order to proceed to add as member.</div>
			
				<input name="fullname" type="hidden" value="<? echo $tempname; ?>">
				<input name="title" type="hidden" value="<? echo $temptitle; ?>">
				<input name="icnum" type="hidden" value="<? echo $tempic; ?>">
				<input name="email" type="hidden" value="<? echo $tempemail; ?>">
				<input name="email2" type="hidden" value="<? echo $tempemail2; ?>"> 
			    <input name="email3" type="hidden" value="<? echo $tempemail3; ?>"> 
				<input name="address_line1_0" type="hidden" value="<? echo $tempaddress; ?>">
				<input name="address_city_0" type="hidden" value="<? echo $tempcity; ?>">
				<input name="address_state_0" type="hidden" value="<? echo $tempstate; ?>">
				<input name="address_zip_0" type="hidden" value="<? echo $tempzip; ?>">
				<input name="address_phone1_0" type="hidden" value="<? echo $tempphone1; ?>">
				<input name="address_phone2_0" type="hidden" value="<? echo $tempphone2 ?>">
				<input name="address_phone3_0" type="hidden" value="<? echo $tempphone3 ?>">
				<input name="address_fax1_0" type="hidden" value="<? echo $tempfax1 ?>"> 
			    <input name="address_fax2_0" type="hidden" value="<? echo $tempfax2 ?>"> 
				<input name="address_country_0" type="hidden" value="<? echo $tempcountry; ?>">
				
				<input name="address_line1_1" type="hidden" value="<? echo $tempaddress1; ?>">
				<input name="address_city_1" type="hidden" value="<? echo $tempcity1; ?>">
				<input name="address_state_1" type="hidden" value="<? echo $tempstate1; ?>">
				<input name="address_zip_1" type="hidden" value="<? echo $tempzip1; ?>">
				<input name="address_phone1_1" type="hidden" value="<? echo $tempphone11; ?>">
				<input name="address_phone2_1" type="hidden" value="<? echo $tempphone21; ?>">
				<input name="address_phone3_1" type="hidden" value="<? echo $tempphone31; ?>">
				<input name="address_fax1_1" type="hidden" value="<? echo $tempfax11 ?>"> 
			    <input name="address_fax2_1" type="hidden" value="<? echo $tempfax21 ?>"> 
				<input name="address_country_1" type="hidden" value="<? echo $tempcountry2; ?>">

				<input name="con_line1" type="hidden" value="<? echo $confaddress; ?>">
				<input name="con_city" type="hidden" value="<? echo $confcity; ?>">
				<input name="con_state" type="hidden" value="<? echo $confstate; ?>">
				<input name="con_zip" type="hidden" value="<? echo $confzip; ?>">
				<input name="con_phone1" type="hidden" value="<? echo $confphone1; ?>">
				<input name="con_phone2" type="hidden" value="<? echo $confphone2; ?>">
				<input name="con_phone3" type="hidden" value="<? echo $confphone3; ?>">
				<input name="address_country" type="hidden" value="<? echo $confcountry; ?>">
				<?
				$j = 0;
				while ($j < $i)
				{
					echo "<input name=\"area[]\" type=\"hidden\" value=\"$areaid[$j]\">";
					$j++;
				}	
				?>
				<input type="hidden" name="selectcat" value="<? echo $categoryid; ?>">
				
				<input type="hidden" name="committee" value="<? echo $contribution; ?>">
				<input type="hidden" name="position" value="<? echo $contribution_pos; ?>">
				<input type="hidden" name="year" value="<? echo $contribution_ye; ?>">
				<input type="hidden" name="medium_fax" value="<? echo $med_fax; ?>">
				      <input type="hidden" name="medium_email" value="<? echo $med_email; ?>">
				      <input type="hidden" name="medium_hand" value="<? echo $med_hand; ?>">
				      <input type="hidden" name="medium_post" value="<? echo $med_post; ?>">
				      <input type="hidden" name="medium_inform" value="<? echo $med_inform; ?>">
				      <input type="hidden" name="medium_invite" value="<? echo $med_invite; ?>">
				<input type="hidden" name="membercheck" value="<? echo $membercheck; ?>">

			<div align="center"><input type="image" src="../images/m5/m5_btn_back.gif" align="middle">
			<input type="hidden" name="Submit" value="Submit"></div>
			</tr>
			</td>
			</table>
			</form>
			<?
			$member = 1;
			exit();
		}
		$memberstatus = 1;
	}

	$lastUpdate = mysql_fetch_array(mysql_query("SELECT NOW() AS lastUpdate", $db_link));
	$lastUpdate = $lastUpdate['lastUpdate'];

	$contact_fullname = addslashes(strip_tags(trim( $_POST['fullname'] )));
	$contact_icnum = addslashes(strip_tags(trim( $_POST['icnum'] )));
	$contact_title = addslashes(strip_tags(trim( $_POST['title'] )));
	$contact_pictureURL = addslashes(strip_tags(trim( $_POST['pictureURL'] )));
	$contact_hidden = 0;
	$contact_status = addslashes(strip_tags(trim( $_POST['status'] )));
}
	
//-- INSERT INTO CONTACT --
if ($mode == 'new') 
{
	$sql = "INSERT INTO " . TABLE_CONTACT . " (id) VALUES ('')";
	runQuery($sql);
	$getID = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()", $db_link));
	$id = $getID[0];  
}

//-- INSERT INTO CONTACT CONTRIBUTION --
if (($mode == 'new') || ($mode == 'edit'))
{
	$committee = $_POST['committee']; 
	$position = $_POST['position'];
	$year = $_POST['year'];

	runQuery("INSERT INTO " . TABLE_CONTRIBUTION . " (committee, position, year, id) VALUES ('$committee', '$position', '$year', $id)"); 
	runQuery("DELETE FROM " . TABLE_CONTRIBUTION . " WHERE committee = '' AND position = ''");

	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	if ( !empty($temp_name) && !empty($committee) )
	$audit_trail->writeLog($_SESSION['username'], "contact", "Insert contribution to $temp_name");			
}

//-- INSERT INTO CONTACT NEXT OF KIN --
if (($mode == 'new') || ($mode == 'edit'))
{
	$kin_date_of_deceased = $_POST['kin_date_of_deceased']; 
	$kin_full_name = $_POST['kin_full_name'];
	$kin_ic_passport = $_POST['kin_ic_passport'];
	$kin_address = $_POST['kin_address'];
	$kin_city = $_POST['kin_city'];
	$kin_postcode = $_POST['kin_postcode'];
	$kin_country = $_POST['kin_country'];
	$kin_email = $_POST['kin_email'];
	$kin_relationship = $_POST['kin_relationship'];
	$kin_contact_no = $_POST['kin_contact_no'];

	runQuery("INSERT INTO " . TABLE_NEXT_OF_KIN . " (contact_id, date_of_deceased, full_name, ic_passport, address, city, postcode, country, email, relationship, contact_no) VALUES ('$id', '$kin_date_of_deceased', '$kin_full_name', '$kin_ic_passport', '$kin_address', '$kin_city', '$kin_postcode', '$kin_country', '$kin_email', '$kin_relationship', '$kin_contact_no')"); 

	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	if ( !empty($temp_name) && !empty($committee) )
	$audit_trail->writeLog($_SESSION['username'], "contact", "Insert next of kin to $temp_name");			
}

//-- INSERT INTO CONTACT MEDIUM --
if (($mode == 'new') || ($mode == 'edit'))
{
	$med_fax = $_POST['m_fax'];
	$med_email = $_POST['m_email'];
	$med_post = $_POST['m_post'];
	$med_hand = $_POST['m_hand'];
	$med_inform = $_POST['m_inform'];
	$med_invite = $_POST['m_invite'];
	
	$checkmedium = mysql_query("SELECT medium_id FROM " . TABLE_MEDIUM . " WHERE medium_contact_id = '$id'", $db_link);
	$c_medium = mysql_fetch_array($checkmedium);
	$id_medium = $c_medium['medium_id'];
	if(!empty($id_medium)) {
		runQuery("UPDATE " . TABLE_MEDIUM . " SET medium_fax='$med_fax', medium_email='$med_email', medium_hand='$med_hand', medium_post='$med_post', medium_invite='$med_invite', medium_inform='$med_inform' WHERE medium_id = '$id_medium' LIMIT 1"); 
	}
	else if(empty($id_medium)) {
		runQuery("INSERT INTO " . TABLE_MEDIUM . " (medium_contact_id, medium_fax, medium_email, medium_hand, medium_post, medium_invite, medium_inform) VALUES ('$id', '$med_fax', '$med_email', '$med_hand', '$med_post', '$med_invite', '$med_inform')"); 		
	}
	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	if ( !empty($temp_name) && !empty($committee) )
	$audit_trail->writeLog($_SESSION['username'], "contact", "Insert preferable medium to $temp_name");			
}

// -- PROCESS ADDRESSES --
if ($mode == 'new')
{
	//echo $_POST['addnum']."<br>";
	for ( $x = 0; $x <= $_POST['addnum']; $x++ ) 
	{
		$address_refid = $_POST['address_refid_' . $x];
		$address_line1 = addslashes( strip_tags( trim($_POST['address_line1_' . $x]) ) );
		$address_city = addslashes( strip_tags( trim($_POST['address_city_' . $x]) ) );
		$address_state = addslashes( strip_tags( trim($_POST['address_state_' . $x]) ) );
		$address_zip = addslashes( strip_tags( trim($_POST['address_zip_' . $x]) ) );
		$address_phone1 = addslashes( strip_tags( trim($_POST['address_phone1_' . $x]) ) );
		$address_phone2 = addslashes( strip_tags( trim($_POST['address_phone2_' . $x]) ) );
		$address_phone3 = addslashes( strip_tags( trim($_POST['address_phone3_' . $x]) ) );
		$address_fax1 = addslashes( strip_tags( trim($_POST['address_fax1_' . $x]) ) );
		$address_fax2 = addslashes( strip_tags( trim($_POST['address_fax2_' . $x]) ) );
		$address_country = addslashes( strip_tags( trim($_POST['address_country_' . $x]) ) );
		$address_primary = "address_primary_" . $x;

		// Check for blanks. If not, use REPLACE INTO
		if (empty($address_type) && empty($address_line1) && empty($address_line2) && empty($address_city) && empty($address_state) && empty($address_zip) && empty($address_phone1) && empty($address_phone2) && empty($address_phone3) && empty($address_fax1) && empty($address_fax2)) 
		{
			if (!empty($address_refid)) 
			{	runQuery("DELETE FROM " . TABLE_ADDRESS . " WHERE refid = $address_refid LIMIT 1");	}
		}
		else 
		{
			runQuery("REPLACE INTO " . TABLE_ADDRESS . " VALUES ('$address_refid', $id, '$address_line1', '$address_city', '$address_state', '$address_zip', '$address_country', '$address_phone1', '$address_phone2', '$address_phone3', '$address_fax1', '$address_fax2')");
			if (empty($address_refid)) 
			{
				$address_refid = mysql_fetch_row(runQuery("SELECT LAST_INSERT_ID()"));
				$address_refid = $address_refid[0];  
			}
		}

		if ($address_primary == "address_primary_0")
		{	$contact_primaryAddress = $address_refid;	}
	}
}
	
if ($mode == 'edit')
{
	for ( $x = 0; $x <= 1; $x++ ) 
	{
		$address_refid = $_POST['address_refid_' . $x];
		$address_line1 = addslashes( strip_tags( trim($_POST['address_line1_' . $x]) ) );
		$address_city = addslashes( strip_tags( trim($_POST['address_city_' . $x]) ) );
		$address_state = addslashes( strip_tags( trim($_POST['address_state_' . $x]) ) );
		$address_zip = addslashes( strip_tags( trim($_POST['address_zip_' . $x]) ) );
		$address_phone1 = addslashes( strip_tags( trim($_POST['address_phone1_' . $x]) ) );
		$address_phone2 = addslashes( strip_tags( trim($_POST['address_phone2_' . $x]) ) );
		$address_phone3 = addslashes( strip_tags( trim($_POST['address_phone3_' . $x]) ) );
		$address_fax1 = addslashes( strip_tags( trim($_POST['address_fax1_' . $x]) ) );
		$address_fax2 = addslashes( strip_tags( trim($_POST['address_fax2_' . $x]) ) );
		$address_country = addslashes( strip_tags( trim($_POST['address_country_' . $x]) ) );
		$address_primary = "address_primary_" . $x;

		// Check for blanks. If not, use REPLACE INTO
		if (empty($address_type) && empty($address_line1) && empty($address_line2) && empty($address_city) && empty($address_state) && empty($address_zip) && empty($address_phone1) && empty($address_phone2) && empty($address_phone3) && empty($address_fax1) && empty($address_fax2)) 
		{
			if (!empty($address_refid)) 
			{	runQuery("DELETE FROM " . TABLE_ADDRESS . " WHERE refid = $address_refid LIMIT 1");	}
		}
		else 
		{
			runQuery("REPLACE INTO " . TABLE_ADDRESS . " VALUES ('$address_refid', $id, '$address_line1', '$address_city', '$address_state', '$address_zip', '$address_country', '$address_phone1', '$address_phone2', '$address_phone3', '$address_fax1', '$address_fax2')");
			if (empty($address_refid)) 
			{
				$address_refid = mysql_fetch_row(runQuery("SELECT LAST_INSERT_ID()"));
				$address_refid = $address_refid[0];  
			}
		}

		if ($address_primary == "address_primary_0")
		{	$contact_primaryAddress = $address_refid;	}
	}
}

if ($mode == 'delete') 
{
	require_once("../classes/user.php");
	$objUser = new User();
	$success = $objUser->deleteUserByContactId($id);
}

if ( ($mode == 'new') || ($mode == 'edit') )
{
	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];

	// -- PROCESS GROUPS --
	$numOldRows = mysql_num_rows(mysql_query("SELECT * FROM " . TABLE_GROUPS . " WHERE id = $id", $db_link));
	runQuery("DELETE FROM " . TABLE_GROUPS . " WHERE id = $id LIMIT $numOldRows");
	optimizeTable(TABLE_GROUPS);
	if ($mode == 'edit')
	{	$audit_trail->writeLog($_SESSION['username'], "contact", "Remove group from $temp_name");	}	
	//-- PROCESS AREA OF EXPERTISE --
	$numOldRows = mysql_num_rows(mysql_query("SELECT * FROM " . TABLE_EXPERTLINK . " WHERE id = $id", $db_link));
	runQuery("DELETE FROM " . TABLE_EXPERTLINK . " WHERE id = $id LIMIT $numOldRows");
	optimizeTable(contact_expertlink);
	if ($mode == 'edit')
	{	$audit_trail->writeLog($_SESSION['username'], "contact", "Remove area of expertise from $temp_name");	}			
}

//ADDING GROUPS
if ($_POST['groups']) 
{
	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	
	while (list ($x_key, $x_gid) = each ($_POST['groups'])) 
	{
		$groupsql = "INSERT INTO " . TABLE_GROUPS . " VALUES ($id,$x_gid)";
		runQuery($groupsql);
		$audit_trail->writeLog($_SESSION['username'], "contact", "Add group $x_gid for $contact_fullname");			
	}
}
else
{	mysql_query("INSERT INTO " . TABLE_GROUPS . " VALUES ($id, 1)", $db_link);	}

//ADDING AREA OF EXPERTISE
if ($_POST['areas']) 
{
	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	
	while (list ($x_key, $x_gid) = each ($_POST['areas'])) 
	{
		$audit_trail->writeLog($_SESSION['username'], "contact", "Add new area of expertise: $x_gid to $contact_fullname");
		$areaql = "INSERT INTO " . TABLE_EXPERTLINK . " VALUES ($id, $x_gid)";
		runQuery($areaql);
	}
}

if ($mode == 'delete') 
{
	runQuery("UPDATE " . TABLE_CONTACT . " SET delflag = 1 WHERE id = $id LIMIT 1");
	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	optimizeTable(TABLE_CONTACT);
	$audit_trail->writeLog($_SESSION['username'], "contact", "Delete contact $temp_name");
	header("Location: list.php");
	exit();
}

//EMAIL FUNCTIONS
if (($mode == 'edit') || ($mode == 'new')) 
{	
	$email = trim($_POST['email']);
	$email2 = trim($_POST['email2']);
	$email3 = trim($_POST['email3']);
	if (!empty($email) || !empty($email2) || !empty($email3))
	{
		$check = mysql_query("SELECT id FROM " . TABLE_EMAIL . " WHERE id = '$id'", $db_link);
		$t_check = mysql_fetch_array($check);
		$temp_id = $t_check['id'];

		$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
		$t_audit = mysql_fetch_array($audit);
		$temp_name = $t_audit['fullname']; 

		if (!empty($temp_id))
		{
			mysql_query("UPDATE " . TABLE_EMAIL . " SET email = '$email', email2 = '$email2', email3 = '$email3' WHERE id = '$temp_id' LIMIT 1 ;", $db_link);
			$audit_trail->writeLog($_SESSION['username'], "contact", "Update email for $temp_name");			
		}
		else
		{
			mysql_query("INSERT INTO " . TABLE_EMAIL . " VALUES ($id, '$email', '$email2', '$email3')", $db_link);
			$audit_trail->writeLog($_SESSION['username'], "contact", "Insert email for $contact_fullname");			
		}
	}
}

//--CONFIDENTIAL INFORMATION--
if ( ($mode == 'new') || ($mode == 'edit') )
{	
	$checkexist = mysql_query("SELECT con_refid, con_id FROM " . TABLE_CONFIDENTIAL . " WHERE con_id = '$id'", $db_link);
	$t_checkexist = mysql_fetch_array($checkexist);
	$temp_ref_id = $t_checkexist['con_refid'];

	if (empty($temp_ref_id))
	{
		$con_line1 = addslashes( strip_tags( trim($_POST['con_line1']) ) );
		$con_city = addslashes( strip_tags( trim($_POST['con_city']) ) );
		$con_state = addslashes( strip_tags( trim($_POST['con_state']) ) );
		$con_zip = addslashes( strip_tags( trim($_POST['con_zip']) ) );
		$con_phone1 = addslashes( strip_tags( trim($_POST['con_phone1']) ) );
		$con_phone2 = addslashes( strip_tags( trim($_POST['con_phone2']) ) );
		$con_phone3 = addslashes( strip_tags( trim($_POST['con_phone3']) ) );
		$con_country = addslashes( strip_tags( trim($_POST['con_country']) ) );
		$con_resume1 =  $_POST['con_resume1'];
		$con_resume2 =  $_POST['con_resume2'];
		
		// SET VARIABLES TO BE USED FOR UPLOAD
		$strUploadDir = "resumes/";
		$arrValidExtensions = array( "doc", "pdf", "xls", "DOC", "PDF", "XLS" );
		// GET UPLOADED DATA	
		$clsFileUpload1 = new clsFileUpload('con_resume1');
		$clsFileUpload1->moveFile($strUploadDir);
		$clsFileUpload2 = new clsFileUpload('con_resume2');
		$clsFileUpload2->moveFile($strUploadDir);
		
		$con_sql = "INSERT INTO 
					" . TABLE_CONFIDENTIAL . " (con_id, con_line1,	con_city, con_state,
					con_zip, con_country, con_phone1, con_phone2, con_phone3, con_resume1, con_resume2) 
					VALUES ('$id', '$con_line1', '$con_city', '$con_state', '$con_zip', '$con_country', '$con_phone1', 
					'$con_phone2', '$con_phone3', '". $clsFileUpload1->getNewFileName() ."', 
					'". $clsFileUpload2->getNewFileName() ."')";
		runQuery($con_sql);
		
		$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
		$t_audit = mysql_fetch_array($audit);
		$temp_name = $t_audit['fullname'];
		if (!empty($temp_name))
		{	$audit_trail->writeLog($_SESSION['username'], "contact", "Add confidentials for $temp_name");	}			
	}
}

if ($mode == 'edit')
{
	$check = mysql_query("SELECT con_resume1, con_resume2 FROM " . TABLE_CONFIDENTIAL . " WHERE con_id = '$id'", $db_link);
	$t_check = mysql_fetch_array($check);
	$temp_cv1 = $t_check['con_resume1'];
	$temp_cv2 = $t_check['con_resume2'];
	
	$con_line1 = addslashes( strip_tags( trim($_POST['con_line1']) ) );
	$con_city = addslashes( strip_tags( trim($_POST['con_city']) ) );
	$con_state = addslashes( strip_tags( trim($_POST['con_state']) ) );
	$con_zip = addslashes( strip_tags( trim($_POST['con_zip']) ) );
	$con_phone1 = addslashes( strip_tags( trim($_POST['con_phone1']) ) );
	$con_phone2 = addslashes( strip_tags( trim($_POST['con_phone2']) ) );
	$con_phone3 = addslashes( strip_tags( trim($_POST['con_phone3']) ) );
	$con_country = addslashes( strip_tags( trim($_POST['con_country']) ) );
	$con_resume1 =  $_POST['con_resume1'];
	$con_resume2 =  $_POST['con_resume2'];
	
	// SET VARIABLES TO BE USED FOR UPLOAD
	$strUploadDir = "resumes/";
	$arrValidExtensions = array( "doc", "pdf", "xls", "DOC", "PDF", "XLS" );
	// GET UPLOADED DATA	
	$clsFileUpload1 = new clsFileUpload('con_resume1');
	$boolNewUpload1 = ( $clsFileUpload1->getFileSize() == 0 ) ? false : true;
	if ($boolNewUpload1 == true)
	{	
		$clsFileUpload1->moveFile($strUploadDir);
		$updatecv1 = $clsFileUpload1->getNewFileName();
	}
	
	if ($boolNewUpload1 == false)
	{	$updatecv1 = $temp_cv1;	}
	
	$clsFileUpload2 = new clsFileUpload('con_resume2');
	$boolNewUpload2 = ( $clsFileUpload2->getFileSize() == 0 ) ? false : true;
	$updatecv2 = $temp_cv2;
	if ($boolNewUpload2 == true)
	{
		$clsFileUpload2->moveFile($strUploadDir);
		$updatecv2 = $clsFileUpload2->getNewFileName();
	}
	
	$con_sql = "UPDATE " . TABLE_CONFIDENTIAL . " SET  
					con_line1 = '$con_line1',	
					con_city = '$con_city', 
					con_state = '$con_state',
					con_zip = '$con_zip', 
					con_country = '$con_country', 
					con_phone1 = '$con_phone1', 
					con_phone2 = '$con_phone2', 
					con_phone3 = '$con_phone3', 
					con_resume1 = '$updatecv1', 
					con_resume2 = '$updatecv2' 
					WHERE con_id=$id LIMIT 1";
	runQuery($con_sql);
	
	$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
	$t_audit = mysql_fetch_array($audit);
	$temp_name = $t_audit['fullname'];
	$audit_trail->writeLog($_SESSION['username'], "contact", "Update confidentials for $contact_fullname");			
}
	
if (($mode == 'edit') || ($mode == 'new'))
{
	$gambar = $_POST['pics'];
	$my_uploader = new uploader('en'); 
	// OPTIONAL: set the max filesize of uploadable files in bytes
	$my_uploader->max_filesize(4000000);
	// OPTIONAL: if you're uploading images, you can set the max pixel dimensions 
	$my_uploader->max_image_size(1300, 1100);
	// UPLOAD the file
	$my_uploader->upload("userfile2", "image/jpeg, image/gif, image/pjpeg", ".jpg, .gif");
	// MOVE THE FILE to its final destination
	//	$mode = 2 ::	rename new file if a file
	//	           		with the same name already 
	//             		exists: file.txt becomes file_copy0.txt
	
	if ($my_uploader->error){
		$errormessage = $my_uploader->error;
		require_once('prefill.php');
	}
	
	$picmode = 2;
	$my_uploader->save_file("mugshots/", $picmode);
	// RETURN RESULTS
	if ($my_uploader->error) 
	{
		if ($my_uploader->error == "error")
			$error = "yes";
		
		$sql = "UPDATE " . TABLE_CONTACT . " SET 
							fullname = '$contact_fullname',
							icnum = '$contact_icnum',
							title = '$contact_title',
							primaryAddress = '$contact_primaryAddress',
							pictureURL = '$gambar',
							lastUpdate = '$lastUpdate',
							hidden = $contact_hidden,
							whoAdded = 'NULL'
							WHERE id=$id LIMIT 1";
		runQuery($sql);
		$audit_trail->writeLog($_SESSION['username'], "contact", "Add/update contacts $contact_fullname");
		// ** IF BECOME MEMBER IS CHECK, UPDATE DATA IN LIBRARY AND REDIRECTS TO USER MANAGEMENT **
		if ($memberstatus == 1)
		{
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=../index.php?mod=user&obj=user&do=add&contact_id=".$id."&start=".$start."&bookcode=".$book_code."\">";
			exit();
		}
		header("Location: address.php?id=$id&mode=$mode&error=$error");
		exit();
	} 
	
	$rimg=new RESIZEIMAGE("mugshots/".$my_uploader->file['name']);
	$rimg->resize_limitwh(126, 160);
	
	$sql = "UPDATE " . TABLE_CONTACT . " SET 
							fullname = '$contact_fullname',
							icnum = '$contact_icnum',
							title = '$contact_title',
							primaryAddress = '$contact_primaryAddress',
							pictureURL = '".$my_uploader->file['name']."',
							lastUpdate = '$lastUpdate',
							hidden = $contact_hidden,
							whoAdded = 'NULL'
						WHERE id=$id LIMIT 1";
	runQuery($sql);
	$audit_trail->writeLog($_SESSION['username'], "contact", "Add/update contacts $contact_fullname");
	// ** IF BECOME MEMBER IS CHECK, UPDATE DATA IN LIBRARY AND REDIRECTS TO USER MANAGEMENT **
	if ($memberstatus == 1)
	{
		echo "<meta http-equiv=\"refresh\" content=\"0;URL=../index.php?mod=user&obj=user&do=add&contact_id=".$id."&start=".$start."&bookcode=".$book_code."\">";
		exit();
	}
	header("Location: address.php?id=$id&mode=$mode");
	exit();
}
?>

