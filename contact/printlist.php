<?
session_start();
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);
	require_once('local_config.php');
	session_start();

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	// ** CHECK FOR LOGIN **
	checkForLogin();
	
	// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
	
	// ** END INITIALIZATION *******************************************************
	
	$from = $_GET['from'];
	
	// ** BROWSER CHECK ***********************************************
	function browser_detection( $which_test ) 
	{
	
		// initialize the variables
		$browser = '';
		$dom_browser = '';
	
		// set to lower case to avoid errors, check to see if http_user_agent is set
		$navigator_user_agent = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
	
		// run through the main browser possibilities, assign them to the main $browser variable
		if (stristr($navigator_user_agent, "opera")) 
		{
			$browser = 'opera';
			$dom_browser = true;
		}
	
		elseif (stristr($navigator_user_agent, "msie 4")) 
		{
			$browser = 'msie4'; 
			$dom_browser = false;
		}
	
		elseif (stristr($navigator_user_agent, "msie")) 
		{
			$browser = 'msie'; 
			$dom_browser = true;
		}
	
		elseif ((stristr($navigator_user_agent, "konqueror")) || (stristr($navigator_user_agent, "safari"))) 
		{
			$browser = 'safari'; 
			$dom_browser = true;
		}
	
		elseif (stristr($navigator_user_agent, "gecko")) 
		{
			$browser = 'mozilla';
			$dom_browser = true;
		}
		
		elseif (stristr($navigator_user_agent, "mozilla/4")) 
		{
			$browser = 'ns4';
			$dom_browser = false;
		}
		
		else 
		{
			$dom_browser = false;
			$browser = false;
		}
	
		// return the test result you want
		if ( $which_test == 'browser' )
		{
			return $browser;
		}
		elseif ( $which_test == 'dom' )
		{
			return $dom_browser;
			//  note: $dom_browser is a boolean value, true/false, so you can just test if
			// it's true or not.
		}
	}	
	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ASMIC - Academy of Science Malaysia Information Center</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<? echo $app_absolute_path; ?>asmic.css" rel="stylesheet" type="text/css">
</head>
<script language="javascript">
function Clickheretoprint()
{ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25"; 
  var content_vlue = document.getElementById("print_content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('<html><head><title>ASMIC</title>');
   docprint.document.write('<link href="../asmic.css" rel="stylesheet" type="text/css">'); 
   docprint.document.write('</head><body onLoad="window.print()"><center>');          
   docprint.document.write(content_vlue);          
   docprint.document.write('</center></body></html>'); 
   docprint.document.close(); 
   docprint.focus(); 
}
function Clickheretosave()
{ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25"; 
  var content_vlue = document.getElementById("print_content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('<html><head><title>@ASMIC</title>');
   docprint.document.write('<link href="../asmic.css" rel="stylesheet" type="text/css">');    
   docprint.document.write('</head><body><center>');          
   docprint.document.write(content_vlue);          
   docprint.document.write('</center></body></html>'); 
   docprint.document.close(); 
   docprint.focus();
   docprint.document.execCommand('SaveAs', 'null', 'Contact_<? echo date('dmY_hisA'); ?>.html');
   docprint.close();
}
function check_all(print)
{
  for (i = 0; i < print.elements.length; i++)
  print.elements[i].checked = "on" ;
}

function uncheck_all(print)
{
  for (i = 0; i < print.elements.length; i++)
  print.elements[i].checked = "" ;
}
</script>
<body>
<table width="800" cellpadding="1" cellspacing="0" border="0">
<tr>
<td>
<table width="100%" cellpadding="1" cellspacing="0" border="0">
<tr>
<td>&nbsp;

</td>
</tr>
</table>
<div class="style3" id="print_content">
<style type="text/css">
.ar11_content 
{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}
</style>
<?
//ASM A4 Paper - Print Header Space
/*************************************************************/
echo(file_get_contents("../includes/print_header_space.php"));
/*************************************************************/
?>
<table width="100%" cellpadding="1" cellspacing="0" border="0">
<tr>
<td>
<font class="ar11_content"><b>ASMIC - Academy of Science Malaysia Information Center</b></font><br><br>
<font class="ar11_content"><b><u>Contact Listings</u></b></font>
</td>
</tr>
<tr>
<td background="<? echo $app_absolute_path; ?>images/separator2.gif">&nbsp;
<? //echo date('dmY_gisA'); 

?>
</td>
</tr>
<?
if($_GET['choose']) {
?>
<tr>
<td class="ar11_content">
<form name="print" method="post" action="printlist.php">
Please Check the specific options to print particular details<br><br>
				
<!-- &nbsp;< input type="checkbox" name="checkname" value="1" >Name -->
&nbsp;<input  type="checkbox" name="checkaddress" value="1">Address
&nbsp;<input type="checkbox" name="checkphone" value="1">Telephone
&nbsp;<input type="checkbox" name="checkfax" value="1">Fax
&nbsp;<input type="checkbox" name="checkemail" value="1">E-Mail
&nbsp;&nbsp;
<a href="#"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_selectall.gif" border="0" onClick="check_all(print)" align="middle"></a>&nbsp;
<a href="#"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_deselectall.gif" border="0" onClick="uncheck_all(print)" align="middle"></a>

<br><br>
<?

	
	if(!empty($_GET['arrid'])) {
	
	$id = explode("|", $_GET['arrid']);
	
           $cnt_id = count($id);
           for ( $i=0 ; $i < $cnt_id-1 ; $i++ ) {
            $elem = $id[$i];
	    echo "<input type=\"hidden\" name=\"contactid[]\" value=\"$elem\">";
           }
		
	}
	if ($_POST['print']) 
	{		
		while (list ($x_key, $id) = each ($_POST['print'])) 
		{
			$contact = new Contact($id);
			$r_additionalData = mysql_query("SELECT * FROM " . TABLE_ADDITIONALDATA . " AS additionaldata WHERE additionaldata.id=$id", $db_link);
			$r_address = mysql_query("SELECT * FROM " . TABLE_ADDRESS . " AS address WHERE address.id=$id LIMIT 1", $db_link);
			$r_email = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
			$r_groups = mysql_query("SELECT grouplist.groupid, groupname FROM " . TABLE_GROUPS . " AS groups LEFT JOIN " . TABLE_GROUPLIST . " AS grouplist ON groups.groupid=grouplist.groupid WHERE id=$id", $db_link);
			
			echo "<input type=\"hidden\" name=\"contactid[]\" value=\"$id\">";
			echo "<p>";
			if (!empty($contact->title))
			{
				//echo "<b>$contact->title</b>&nbsp;";
			}
			//echo "<b>$contact->fullname</b>";
			while ($tbl_address = mysql_fetch_array($r_address)) 
			{
				$address_refid = $tbl_address['refid'];
				$address_line1 = stripslashes( $tbl_address['line1'] );
				$address_city = stripslashes( $tbl_address['city'] );
				$address_state = stripslashes( $tbl_address['state'] );
				$address_zip = stripslashes( $tbl_address['zip'] );
				$address_phone1 = stripslashes( $tbl_address['phone1'] );
				$address_phone2 = stripslashes( $tbl_address['phone2'] );
				$address_phone3 = stripslashes( $tbl_address['phone3'] );
				$address_fax1 = stripslashes( $tbl_address['fax1'] );
				$address_fax2 = stripslashes( $tbl_address['fax2'] );
				$address_country = $tbl_address['country'];
				
			}
			
		}
	}
?>
<input title="Print this list" type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_print.gif" align="middle" name="print">&nbsp;
<input type="hidden" name="submit" value="Submit">
</form>
</td>
</tr>
<?
}
else if($_GET['choose']!=1) {
?>
<tr>
<td class="ar11_content">
<?

$id = $_REQUEST['contactid'];

	foreach($id as $id)
		{
			$arrid .= $id."|";
			$contact = new Contact($id);
			$r_additionalData = mysql_query("SELECT * FROM " . TABLE_ADDITIONALDATA . " AS additionaldata WHERE additionaldata.id=$id", $db_link);
			$r_address = mysql_query("SELECT * FROM " . TABLE_ADDRESS . " AS address WHERE address.id=$id LIMIT 1", $db_link);
			$r_email = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
			$r_groups = mysql_query("SELECT grouplist.groupid, groupname FROM " . TABLE_GROUPS . " AS groups LEFT JOIN " . TABLE_GROUPLIST . " AS grouplist ON groups.groupid=grouplist.groupid WHERE id=$id", $db_link);
			echo "<p>";
			if (!empty($contact->title))
			{
				echo "<b>$contact->title</b>&nbsp;";
			}
			echo "<b>$contact->fullname</b>";
			while ($tbl_address = mysql_fetch_array($r_address)) 
			{
				$address_refid = $tbl_address['refid'];
				$address_line1 = stripslashes( $tbl_address['line1'] );
				$address_city = stripslashes( $tbl_address['city'] );
				$address_state = stripslashes( $tbl_address['state'] );
				$address_zip = stripslashes( $tbl_address['zip'] );
				$address_phone1 = stripslashes( $tbl_address['phone1'] );
				$address_phone2 = stripslashes( $tbl_address['phone2'] );
				$address_phone3 = stripslashes( $tbl_address['phone3'] );
				$address_fax1 = stripslashes( $tbl_address['fax1'] );
				$address_fax2 = stripslashes( $tbl_address['fax2'] );
				$address_country = $tbl_address['country'];
			if($_POST['checkaddress'] ==1) {
				if ($address_line1) { echo "\n<BR>".nl2br($address_line1); }
				if ($address_city OR $address_state OR $address_zip) { echo "\n<BR>"; }
				if ($address_city) { echo "$address_city"; }
				if ($address_city AND $address_state) { echo ", "; }
				if ($address_state) { echo "$address_state"; }
				if ($address_zip) { echo " $address_zip"; }
				if ($address_country) 
				{ 
					echo "\n<br>$country[$address_country]";
				}
			}
			if($_POST['checkphone'] ==1) {
				if ($address_phone1) { echo "\n<BR>(M): $address_phone1"; }
				if ($address_phone2) { echo "\n<BR>(H): $address_phone2"; }
				if ($address_phone3) { echo "\n<BR>(O): $address_phone3"; }
			}
			if($_POST['checkfax']==1) {
				if ($address_fax1) { echo "\n<BR>Fax: $address_fax1"; }
				if ($address_fax2) { echo " / $address_fax2"; }
			}
			}
			if($_POST['checkemail']==1) {
			// ** E-MAIL **
			$tbl_email = mysql_fetch_array($r_email);
			$email_address = stripslashes( $tbl_email['email'] );
			$email_address2 = stripslashes( $tbl_email['email2'] );
			$email_address3 = stripslashes( $tbl_email['email3'] );
			$email_type = stripslashes( $tbl_email['type'] );
			if ($email_address) 
			{
				echo("<br>\n$lang[LBL_EMAIL]&nbsp;: ");
				if ($options->useMailScript == 1) 
				{
					echo("<br/>");
					echo $email_address;
				}
			}
			if ($email_address2) 
			{
				echo("<br/>");
				if ($options->useMailScript == 1) 
				{
					echo $email_address2;
				}
			}
			if ($email_address3) 
			{
				echo("<br/>");
				if ($options->useMailScript == 1) 
				{
					echo $email_address3;
				}
			}
			}
		//--END OF CONTACT DETAILS--
		}

?>
</td>
</tr>
<?
}
?>
</table>
</div>
</td>
</tr>
<tr>
<td background="<? echo $app_absolute_path; ?>images/separator2.gif">&nbsp;</td>
</tr>
<tr>
<td>
<?
if($_GET['choose']!=1) {
?>
<table width="100%" cellspacing="0" cellpadding="1">
<tr>
<td>&nbsp;
<input title="Print this list" type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_print.gif" align="middle" onClick="Clickheretoprint()">&nbsp;
<? 
	$user_browser = browser_detection('browser');
	
	if ( $user_browser == 'msie' )
	{
		echo "<input title=\"Save this list\" type=\"image\" src=\"".$app_absolute_path."images/m5/m5_btn_save.gif\" align=\"middle\" onClick=\"Clickheretosave();\">&nbsp;";
	}

	if ($from == "search")
	{
?>
		<a href="searchnew.php"><img title="Back to listings" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle" border="0"></a>
<?
	}
	else
	{
	//echo $_POST['contactid'];
?>
		<a href="printlist.php?choose=1&arrid=<?=$arrid?>"><img title="Back to listings" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle" border="0"></a>
<?
	}
?>
</td>
</tr>
</table>
<?
}
?>
</td>
</tr>
</table>
</body>
</html>
