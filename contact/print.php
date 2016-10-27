<?php
// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASSES);
	require_once('local_config.php');
	session_start();
	
// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** CHECK FOR LOGIN **
	//checkForLogin();

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();

// ** CHECK FOR ID **
	$id = check_id();

// ** END INITIALIZATION *******************************************************
// ** RETRIEVE CONTACT INFORMATION **
	$contact = new Contact($id);
		$r_additionalData = mysql_query("SELECT * FROM " . TABLE_ADDITIONALDATA . " AS additionaldata WHERE additionaldata.id=$id", $db_link);
		$r_address = mysql_query("SELECT * FROM " . TABLE_ADDRESS . " AS address WHERE address.id=$id", $db_link);
		$r_email = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
		$r_groups = mysql_query("SELECT grouplist.groupid, groupname FROM " . TABLE_GROUPS . " AS groups LEFT JOIN " . TABLE_GROUPLIST . " AS grouplist ON groups.groupid=grouplist.groupid WHERE id=$id", $db_link);
		$r_messaging = mysql_query("SELECT * FROM " . TABLE_MESSAGING . " AS messaging WHERE messaging.id=$id", $db_link);
		$r_otherPhone = mysql_query("SELECT * FROM " . TABLE_OTHERPHONE . " AS otherphone WHERE otherphone.id=$id", $db_link);
		$r_websites = mysql_query("SELECT * FROM " . TABLE_WEBSITES . " AS websites WHERE websites.id=$id", $db_link);
		
// CALCULATE 'NEXT' AND 'PREVIOUS' ADDRESS ENTRIES
	$r_prev = mysql_query("SELECT id, fullname FROM " . TABLE_CONTACT . " AS contact WHERE fullname < \"" . $contact->fullname . "\" AND contact.hidden != 1 ORDER BY fullname DESC LIMIT 1", $db_link)
		or die(reportSQLError());
	$t_prev = mysql_fetch_array($r_prev); 
	$prev = $t_prev['id']; 
	if ($prev<1) $prev = $id; 
	$r_next = mysql_query("SELECT id, fullname FROM " . TABLE_CONTACT . " AS contact WHERE fullname > \"" . $contact->fullname . "\" AND contact.hidden != 1 ORDER BY fullname ASC LIMIT 1", $db_link)
		or die(reportSQLError());
	$t_next = mysql_fetch_array($r_next); 
	$next = $t_next['id']; 
	if ($next<1) $next=$id;

// PICTURE STUFF.
	// do we have a picture?
	if ($contact->picture_url) 
	{ 
		$tableColumnAmt = 3;
		$tableColumnWidth = (540 - $options->picWidth) / 2;
	} 
	else 
	{
		if ($options->picAlwaysDisplay == 1) 
		{
			$tableColumnAmt = 3;
			$tableColumnWidth = (540 - $options->picWidth) / 2;
		}
		else 
		{
			$tableColumnAmt = 2;
			$tableColumnWidth = (540 / 2);
		}
	}
	
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
		{	return $browser;	}
		elseif ( $which_test == 'dom' )
		{	return $dom_browser;	}
	}	
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>ASMIC Contact Management</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="<? echo $app_absolute_path; ?>asmic.css" rel="stylesheet" type="text/css">
</head>
<script language="Javascript1.2">
<!--
function printpage() 
{	window.print();	}
//-->
</script>
<script language="javascript">
function doSaveAs()
{	document.execCommand("SaveAs")	}

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
   docprint.document.write('<html><head><title>ASMIC</title>');
   docprint.document.write('<link href="../asmic.css" rel="stylesheet" type="text/css">'); 
   docprint.document.write('</head>'); 
   docprint.document.write('<body>');
   docprint.document.write(content_vlue);          
   docprint.document.write('</body></html>'); 
   docprint.document.close(); 
   docprint.focus(); 
   docprint.document.execCommand('SaveAs', 'null', '<? echo $contact->fullname; ?>_<? echo date('dmY_hisA'); ?>.html');
   docprint.close();
}
</script>
<body>
<div class="style3" id="print_content">
<?
//ASM A4 Paper - Print Header Space
/*************************************************************/
echo(file_get_contents("../includes/print_header_space.php"));
/*************************************************************/
?>
<table width="600" cellpadding="1" cellspacing="0">
<tr>
<td class="ar11_content">
<b>
ASMIC Contact Management Printer Friendly
</b>
</td>
</tr>
<tr>
<td>&nbsp;
</td>
</tr>
<tr>
<td class="ar11_content">
<b>Contact Details</b>
</td>
</tr>
</table>

<br>

<style type="text/css">
.ar11_content 
{
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #000000;
}

.m5_table_outline 
{
	BORDER-RIGHT: #206497 1px solid; BORDER-TOP: #206497 1px solid; BORDER-LEFT: #206497 1px solid; BORDER-BOTTOM: #206497 1px solid;
}
</style>

<table width="600">
<tr>
<td background="<? echo $app_absolute_path; ?>images/separator2.gif">&nbsp;
</td>
</tr>
</table>
<table width="600" border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">

<tr> 
<td width= "100%"> 
<table width="100%"  border="0" cellspacing="0" cellpadding="5">
<tr> 
<td class="ar11_content">
<strong><?	echo $contact->title . " " . $contact->fullname;	?></strong>
</td>
<td class="ar11_content">
<div align="right"><b>Category: </b>
<?php
	// IF ENTRY IS HIDDEN
	if ($contact->hidden == 1) 
	{	echo("[HIDDEN ENTRY] ");	}

	// LIST GROUPS
	$tbl_groups = mysql_fetch_array($r_groups);
	$groupname = stripslashes( $tbl_groups['groupname'] );
	$group_id = $tbl_groups['groupid'];
	 // check if no groups
	if ( !$groupname ) 
	{	echo("<IMG SRC=\"spacer.gif\" WIDTH=1 HEIGHT=1 BORDER=0 ALT=\"\">");	}
	 // format for group links
	$Groups = "<A HREF=\"" . FILE_LIST . "?groupid=" . $group_id . "\" CLASS=\"ar11_content\">" . $groupname . "</A>";
	while ( $tbl_groups = mysql_fetch_array($r_groups) ) 
	{
		$groupname = stripslashes( $tbl_groups['groupname'] );
		$group_id = $tbl_groups['groupid'];
		$Groups = $Groups . ", <A HREF=\"" . FILE_LIST . "?groupid=" . $group_id . "\" CLASS=\"ar11_content\">" . $groupname . "</A>";
	}
	echo($Groups);
?>
</div>
</td>
</tr>
<tr> 
<td colspan="2" class="ar11_content">
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=5 WIDTH="100%">
<TR VALIGN="top">
<TD ROWSPAN="4" class="ar11_content"><?
	/*echo "<IMG SRC=\"";
	if ($contact->picture_url) 
	{	echo(PATH_MUGSHOTS . $contact->picture_url);	} 
	else 
	{	echo("images/nopicture.gif");	}
	echo "\" BORDER=0 title=\"Contacts\">";*/
	?>
</TD>
<tr>
<td colspan=3 valign="top" style="font-family: Tahoma, sans-serif; font-size: 1px; color: #FFFFFF; border-bottom: 1px solid #000000;">&nbsp;
</td>
</tr>
<TD WIDTH="40%" class="ar11_content" VALIGN="top">
<?
	echo "<b>IC Number:</b> $contact->icnum<BR>";
	echo "<b>Title:</b> $contact->title";
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

		echo "<P>\n<B>" . (($contact->primary_address == $address_refid) ? $lang[LBL_PRIMARY_ADDRESS] : $lang[LBL_ADDRESS]);
		echo "</B>\n";
		if ($address_line1) { echo "\n<BR>$address_line1"; }
		if ($address_city OR $address_state OR $address_zip) { echo "\n<BR>"; }
		if ($address_city) { echo "$address_city"; }
		if ($address_city AND $address_state) { echo ", "; }
		if ($address_state) { echo "$address_state"; }
		if ($address_zip) { echo " $address_zip"; }
		if ($address_country) 
		{	echo "\n<br>$country[$address_country]";	}
		if ($address_phone1) { echo "\n<BR>(M): $address_phone1"; }
		if ($address_phone2) { echo "\n<BR>(H): $address_phone2"; }
		if ($address_phone3) { echo "\n<BR>(O): $address_phone3"; }
		if ($address_fax1) { echo "\n<BR>Fax: $address_fax1"; }
		if ($address_fax2) { echo " / $address_fax2"; }
		
	}
	// ** E-MAIL **
	$tbl_email = mysql_fetch_array($r_email);
	$email_address = stripslashes( $tbl_email['email'] );
	$email_address2 = stripslashes( $tbl_email['email2'] );
	$email_address3 = stripslashes( $tbl_email['email3'] );
	$email_type = stripslashes( $tbl_email['type'] );
	if ($email_address) 
	{
		echo("<P>\n<B>$lang[LBL_EMAIL]</B>\n");
			if ($options->useMailScript == 1) 
			{	echo("<BR><A HREF=\"mailto:$email_address\">$email_address</A>");	}
			echo("\n");
	}
	if ($email_address2) 
	{
			if ($options->useMailScript == 1) 
			{
				echo("<BR><A HREF=\"mailto:$email_address\">$email_address2</A>");
			}
			echo("\n");
	}
	if ($email_address3) 
	{
			if ($options->useMailScript == 1) 
			{
				echo("<BR><A HREF=\"mailto:$email_address\">$email_address3</A>");
			}
			echo("\n");
	}
?>
</TD>
<td style="border-left: 1px solid #000000;">&nbsp;
</td>
<TD WIDTH=400 class="ar11_content" VALIGN="top">
<table width="100%" border="0">
	<tr>
	<td class="ar11_content" colspan="3"><b>Confidential Information</b></td>
	<td class="ar11_content" colspan="2">&nbsp;</td>
 	</tr>
	<?
		$confisql = "SELECT * FROM " . TABLE_CONFIDENTIAL . " WHERE con_id = $id";
		$confi_list = mysql_query($confisql, $db_link);

			while ($tbl_grouplist = mysql_fetch_array($confi_list)) 
			{
				$list_con_line1 = $tbl_grouplist['con_line1'];
				$list_con_city = $tbl_grouplist['con_city'];
				$list_con_state = $tbl_grouplist['con_state'];
				$list_con_zip = $tbl_grouplist['con_zip'];
				$list_con_country = $tbl_grouplist['con_country'];
				$list_con_phone1 = $tbl_grouplist['con_phone1'];
				$list_con_phone2 = $tbl_grouplist['con_phone2'];
				$list_con_phone3 = $tbl_grouplist['con_phone3'];
	?>
	<tr>
	<td class="ar11_content" colspan="3">
	<? 
				if ($list_con_line1)
				echo $list_con_line1."<br>";
				if ($list_con_city)
				echo $list_con_city.",";
				if ($list_con_state)
				echo $list_con_state.",";
				if ($list_con_zip)
				echo $list_con_zip."<br>";
				if ($list_con_country) 
				{	echo "$country[$list_con_country]<br/>";	}
				if ($list_con_phone1)
					echo "(M): ". $list_con_phone1."<br>";
				if ($list_con_phone2)
					echo "(H): ". $list_con_phone2."<br>";
				if ($list_con_phone3)
					echo "(O): ". $list_con_phone3."<br>";
	?>
	</td>
	<td class="ar11_content" width="20">&nbsp;</td>
	<td class="ar11_content" width="20">&nbsp;</td>
	</tr>
	<?
			}
	?>
</table>
<!-- DIVIDER FOR THE TABLE -->
<table width="100%" cellspacing="1" border="0" class="ar11_content">
	<tr>
   	<td colspan="3" class="ar11_content"><b>Area of expertise</b></td>
	<td colspan="2" class="ar11_content">&nbsp;</td>
 	</tr>
	<?
		$joinsql = "SELECT " . TABLE_EXPERTISE . ".area_id, " . TABLE_EXPERTISE . ".area_name, 
					" . TABLE_EXPERTISE . ".area_desc, " . TABLE_EXPERTLINK . ".id, 
					" . TABLE_EXPERTLINK . ".area_id FROM (" . TABLE_EXPERTISE . " 
					INNER JOIN " . TABLE_EXPERTLINK . " ON 
					" . TABLE_EXPERTISE . ".area_id = " . TABLE_EXPERTLINK . ".area_id) 
					WHERE id = $id";
		$join_list = mysql_query($joinsql, $db_link);
		while ($tbl_grouplist = mysql_fetch_array($join_list)) 
		{
			$list_areaid = $tbl_grouplist['area_id'];
			$list_areaname = $tbl_grouplist['area_name'];
			$list_id = $tbl_grouplist['id'];
	?>
	<tr>
	<td class="ar11_content" colspan="3"><? echo $list_areaname; ?></td>
	<td class="ar11_content" width="20">&nbsp;</td>
	<td class="ar11_content" width="20">&nbsp;</td>
	</tr>
	<?
		}
	?>
</table>
<br>
	<table width="100%" border="0">
	<tr>
   	<td class="ar11_content"><b>Committee</b></td>
	<td class="ar11_content"><b>Position</b></td>
	<td class="ar11_content"><b>Year</b></td>
 	</tr>
<?
	$c_list = mysql_query("SELECT * FROM " . TABLE_CONTRIBUTION . " WHERE id = $id ORDER BY year DESC", $db_link);
	while ($tbl_grouplist = mysql_fetch_array($c_list)) 
	{
		$list_refid	= $tbl_grouplist['refid'];
		$list_committee = $tbl_grouplist['committee'];
		$list_position	= $tbl_grouplist['position'];
		$list_year	= $tbl_grouplist['year'];
		$list_id	= $tbl_grouplist['id'];
  		echo "<tr>"; 
    	echo "<td class=\"ar11_content\">$list_committee</td>";
	 	echo "<td class=\"ar11_content\">$list_position</td>";
	  	echo "<td class=\"ar11_content\">$list_year</td>";
 	 	echo "</tr>";
	}
?>
	</table>
</TD>
</TR>

</TABLE>
</td>
</tr>
<tr> 
</tr>
</table>
</td>
</tr>
</table>
<tr>
<td height="3"></td>
</tr>
<tr> 
<td class="ar11_content"><font class="ar11_content"><?php echo $lang[LAST_UPDATE].' '.($contact->last_update).'.'; ?></font></td>
</tr>
<tr> 
</tr>
<tr>
<td>&nbsp;
</td>
</tr>
</table>
<table width="600">
<tr>
<td background="<? echo $app_absolute_path; ?>images/separator2.gif">&nbsp;
</td>
</tr>
</table>
</div>

<br><br> 
<table width="600" cellspacing="0" cellpadding="1">
<tr>
<td>&nbsp;
</td>
</tr>
<tr>
<td>&nbsp;
<input title="Print this list" type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_print.gif" align="middle" onClick="Clickheretoprint()">&nbsp;
<? 
	$user_browser = browser_detection('browser');
	if ( $user_browser == 'msie' )
	{	echo "<input title=\"Save this list\" type=\"image\" src=\"".$app_absolute_path."images/m5/m5_btn_save.gif\" align=\"middle\" onClick=\"Clickheretosave();\">&nbsp;";	}
?>
<a href="address.php?id=<? echo $id; ?>"><img title="Back to listings" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle" border="0"></a> 
</td>
</tr>
</table>
</body>
</html>
