<?php
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASSES);
	require_once(FILE_CONTACTNEW);

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

	// CREATE THE LIST.	
	$list = &new ContactList();
	
	// THIS PAGE TAKES SEVERAL GET VARIABLES
	// ie. list.php?group_id=6&page=2&letter=c&limit=20
	if ($_GET['groupid'])         
	$list->group_id = $_GET['groupid'];
	if ($_GET['page'])            
	$list->current_page = $_GET['page'];
	if (isset($_GET['letter']))   
	$list->current_letter = $_GET['letter'];	
	if (isset($_GET['limit']))    
	$list->max_entries = $_GET['limit'];
	
	$from = $_GET['from'];	

	// Set group name (group_id defaults to 0 if not provided)
	$list->group_name();

	// ** RETRIEVE CONTACT LIST BY GROUP **
	$r_contact = $list->retrieve();

	// See if search terms have been passed to this page.
	if ($_POST['goToName'])
	{
		$goTo = trim($_POST['goToName']);
	}
	
	if ($_POST['goTo'])
	{
		$goTo = trim($_POST['goTo']);
	}
	
	$goToAdress = trim($_POST['goToAdress']);
	$goToEmail = trim($_POST['goToEmail']);
	$goToGroup = trim($_POST['goToGroup']);
	$goToArea = trim($_POST['arealist']);
	$goToContribute = trim($_POST['conlist']);
	$goToCountry = trim($_POST['countrylist']);
		
	$searchmain =  "SELECT contact_address.line1, contact_address.city, contact_address.state, 
					contact_address.zip, contact_address.country, contact_address.phone1, contact_address.phone2, contact_address.phone3,
					contact_contact.id, contact_contact.fullname, contact_contact.icnum, contact_contact.title, 
					contact_contact.primaryAddress, contact_contact.pictureURL, contact_contact.lastupdate, 
					contact_contact.hidden, contact_contact.whoadded, contact_email.email, contact_email.email2, contact_email.email3, contact_groups.groupid, 
					contact_grouplist.groupname, contact_expertise.area_name FROM (((((((contact_contact LEFT JOIN 
					contact_address ON contact_contact.id = contact_address.id) LEFT JOIN contact_email ON 
					contact_contact.id = contact_email.id) LEFT JOIN contact_groups ON contact_contact.id = contact_groups.id) 
					LEFT JOIN contact_grouplist ON contact_groups.groupid = contact_grouplist.groupid)LEFT JOIN 
					contact_expertlink ON contact_contact.id = contact_expertlink.id) LEFT JOIN contact_expertise ON 
					contact_expertise.area_id = contact_expertlink.area_id) LEFT JOIN contact_contribution ON 
					contact_contact.id = contact_contribution.id)WHERE 1";
	
    if (!$goTo AND !$goToAdress AND !$goToEmail AND !$goToGroup AND !$goToArea AND !$goToContribute AND !$goToCountry) 
	{
		header("location: detsearch.php");
        exit();
    }
		
	if (!empty($goTo))
	{
		$search1 = " AND contact_contact.fullname LIKE '%$goTo%'";
	}
	else 
	{	
		$search1 = "";
	}
				
	if (!empty($goToAdress))
	{
		//$search2 = " AND contact_address.line1 LIKE '%$goToAdress%'";
		$search2 = " AND CONCAT(contact_address.line1, '', contact_address.city, '', contact_address.state, '', contact_address.zip) LIKE '%$goToAdress%'";
	}
	else
	{
		$search2 = "";
	}
			
	if (!empty($goToEmail))
	{
		$search3 = " AND contact_email.email LIKE '%$goToEmail%'";
		$search3 .= " OR contact_email.email2 LIKE '%$goToEmail%'";
		$search3 .= " OR contact_email.email3 LIKE '%$goToEmail%'";
	}
	else
	{
		$search3 = "";
	}
			
	if (!empty($goToGroup))
	{
		$search4 = " AND contact_grouplist.groupname LIKE '%$goToGroup%'";
		//$search4 = " AND contact_grouplist.groupname LIKE '$goToGroup'";
	}
	else
	{
		$search4 = "";
	}
	
	if (!empty($goToArea))
	{
		$search5 = " AND contact_expertise.area_name LIKE '%$goToArea%'";
		//$search5 = " AND contact_expertise.area_name LIKE '$goToArea'";
	}
	else
	{
		$search5 = "";
	}
	
	if (!empty($goToCountry))
	{
		$search6 = " AND contact_address.country LIKE '%$goToCountry%'";
	}
	else
	{
		$search6 = "";
	}
	
	if (!empty($goToContribute))
	{
		$search7 = " AND contact_contribution.committee LIKE '%$goToContribute%'";
		//$search5 = " AND contact_expertise.area_name LIKE '$goToArea'";
	}
	else
	{
		$search7 = "";
	}
	
	$sqlsearch = $searchmain . $search1 . $search2 . $search3 . $search4 . $search5 . $search6 .$search7 . " AND contact_contact.delflag != 1 AND contact_contact.hidflag != 1 GROUP BY id";
	$querylink = mysql_query($sqlsearch, $db_link);
	$numGoTo = mysql_num_rows($querylink);

//***************************************************************************************
// print results
    if ($numGoTo == 1) 
	{
       	$t_goto = mysql_fetch_array($querylink); 
        $contact_id = $t_goto['id'];
		//header("Location: " . FILE_ADDRESSNEW . "?id=$contact_id"); 
		header("Location: " . FILE_ADDRESS . "?id=$contact_id");
    }

	include_once($app_absolute_path."includes/template_header.php");
?>
<!--*****************************************************************************-->
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function checkAll(field)
{
for (i = 0; i < field.length; i++)
	field[i].checked = true ;
}

function uncheckAll(field)
{
for (i = 0; i < field.length; i++)
	field[i].checked = false ;
}
//  End -->
</script>
<script language="JavaScript" type="text/javascript">
function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
	if(!document.forms[FormName])
		return;
		
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}
</script>
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
<tr> 
<td width="4%" rowspan="15"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="24" height="8"></td>
<td>&nbsp;</td>
<td width="1%" rowspan="15"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="12" height="8"></td>
</tr>
<tr> 
<td class="module_title">ASM Contact</td>
</tr>
<tr>
<td class="breadcrumbs"><? require_once('breadcrumb.php'); ?></td>
</tr>
<tr> 
<td><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="2" height="13"></td>
</tr>
<tr>
<td>
<? require_once('navigation.php'); ?>
</td>
</tr>
<tr>
<td>&nbsp;
</td>
</tr>
<tr> 
<td>
<? require_once('searchform.php'); ?>
</td>
</tr>
<tr> 
<td>&nbsp;</td>
</tr>
<tr> 
<td height="20"><div align="right" class="ar11_content"></div></td>
</tr>
<tr> 
<td>
<?
    if ($numGoTo == 0 AND $numGoToAdd == 0 AND $numGoToEmail == 0 AND $numGoToGroup == 0) 
	{
	?>
		<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
		<tr>
		<td width="100%" class="m5_td_header">
			<table width="100%"  border="0" cellspacing="0" cellpadding="0">
			<tr> 
			<td class="m5_td_header"><strong><?php echo ("LIST OF SEARCH"); ?></strong> 
			</td>
			<td>&nbsp;</td>
			</tr>
			</table>
		</td>
		</tr>
		<tr>
		<td>
		<table width="100%"  border="0" cellspacing="0" cellpadding="0">
		<tr> 
		<td class="m5_td_content">
		<? echo("<P>".$lang['SEARCH_NONE']." "); ?>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH="100%">
		<TR>
		<td>
		<form method="post" action="detsearch.php" enctype="multipart/form-data">
		<input name="byname" type="hidden" value="<? echo $goTo; ?>">
		<input name="byaddress" type="hidden" value="<? echo $goToAdress; ?>">
		<input name="byemail" type="hidden" value="<? echo $goToEmail; ?>">
		<input name="bycategory" type="hidden" value="<? echo $goToGroup; ?>">
		<input name="byarea" type="hidden" value="<? echo $goToArea ?>">
		<input name="bycontribute" type="hidden" value="<? echo $goToContirbute ?>">
		<input name="address_country" type="hidden" value="<? echo $goToCountry ?>">
		<br>
		<?
		if ($from == 'advan')
		{
		?>			
			<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle">
			<input type="hidden" name="Submit" value="Submit">
		<?
		}
		else
		{
		?>
			<a href="javascript:history.go(-1)"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" border="0"></a>
		<?
		}
		?>
		</form>
		</td>
		</tr>
		</table>
		</td>
		</tr>
		</table>
		
	<?
		include_once($app_absolute_path."includes/template_footer.php");
		die();
    }
?>

<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
<tr> 
<td width="100%" class="m5_td_header">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr> 
	<td class="m5_td_header"><strong><?php echo ("LIST OF SEARCH"); ?></strong> 
	</td>
	</tr>
	</table>
</td>
</tr>

<tr> 
<td class="m5_td_content">

<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 WIDTH="100%"> 
<tr>
<td width="27%" class="m5_td_fieldname" align="left"><strong>Name&nbsp;&nbsp;</strong></td> 
<td width="14%" class="m5_td_fieldname" align="left"><strong>Phone Number&nbsp;&nbsp;</strong></td>
<td width="26%" class="m5_td_fieldname" align="left"><strong>Address&nbsp;&nbsp;</strong></td>
<td width="23%" class="m5_td_fieldname" align="left"><strong>Email&nbsp;&nbsp;</strong></td>
<td width="40%" class="m5_td_fieldname" align="right"><strong>&nbsp;Action</strong></td>
</tr>
<?  
	if ($numGoTo > 1)
	{
		echo "<tr><td colspan=\"5\" class=\"m5_td_content\"><b>";		
        echo $lang['SEARCH_MULTIPLE'];
		echo "</b></td></tr>";
				
		while ($t_goto = mysql_fetch_array($querylink))
		{
            $contact_id = $t_goto['id'];
            $contact_name = $t_goto['fullname'];
        	$contact_line1 = $t_goto['line1'];
        	$contact_city = $t_goto['city'];
        	$contact_state = $t_goto['state'];
        	$contact_zip = $t_goto['zip'];
			$contact_phone1 = $t_goto['phone1'];
        	$contact_phone2 = $t_goto['phone2'];
			$contact_phone3 = $t_goto['phone3'];
			$contact_country = $t_goto['country'];
	
?>
<tr>
<td>&nbsp;

</td>
</tr>
<form name="print" method="post" action="printlist.php?from=search" enctype="multipart/form-data">
<TR VALIGN="top"> 
<TD CLASS="m5_td_content">
<A HREF="<? echo FILE_ADDRESS; ?>?id=<? echo $contact_id; ?>"><? echo $contact_name; ?></A>
</TD>
<TD CLASS="m5_td_content"> 
<?

		
        	if ($contact_phone1) 
		{ 
			echo("$contact_phone1<BR>"); 
		}
	
		if ($contact_phone2) 
		{ 
			echo("$contact_phone2<BR>"); 
		}
		
		if ($contact_phone3) 
		{ 
			echo("$contact_phone3"); 
		}
?>
</TD>
<TD CLASS="m5_td_content">
<?
		if ($contact_line1) 
		{ 
			echo("$contact_line1<BR>");
			if ($contact_city) 
			{ 
				echo("$contact_city"); 
			}
			if ($contact_city AND $contact_zip) 
			{ 
				echo (", "); 
			}
			if ($contact_state) 
			{ 
				echo(" $contact_zip ");
			}
			if ($contact_state) 
			{ 
				echo("$contact_state"); 
			}
			if ($contact_country) 
			{ 
				echo("\n<br>$country[$contact_country]");
			}
		}
?>
</TD>
<TD CLASS="m5_td_content">
<?

	$r_email = mysql_query("SELECT id, email, email2, email3 FROM " . TABLE_EMAIL . " AS email WHERE id=$contact_id", $db_link);
	$tbl_email = mysql_fetch_array($r_email);
	$email_address = $tbl_email['email'];
	$email_address2 = $tbl_email['email2'];
	$email_address3 = $tbl_email['email3'];
	if (!empty($email_address))
	{
?>
	<A HREF="mailto:<? echo $email_address; ?>"><? echo $email_address; ?></A><br/>	 
<?
	}
	else
	{
		echo "&nbsp;";
	}
	
	if (!empty($email_address2))
	{
?>
<A HREF="mailto:<? echo $email_address2; ?>"><? echo $email_address2; ?></A><br/> 
<?
	}
	else
	{
		echo "&nbsp;";
	}
	
	if (!empty($email_address3))
	{
?>
<A HREF="mailto:<? echo $email_address3; ?>"><? echo $email_address3; ?></A>
<?
	}
	else
	{
		echo "&nbsp;";
	}
?>
<td align="right" valign="middle">
<?
	if (isAllowed(array(401), $_SESSION['permissions']))
	{
?>
		<table>
		<tr>
		<td>
		<input type="checkbox" name="print[]" value="<? echo $contact_id; ?>">
		</td>
		<td>
		<A HREF="<? echo FILE_EDIT; ?>?id=<? echo $contact_id; ?>"><img src="<? echo $app_absolute_path; ?>images/icon_edit.gif" border="0" align="absmiddle"></A>
		</td>
		<td>
		<a href="vcard.php?id=<? echo $contact_id; ?>&vcard=vcard"><img title="Create vcard" src="<? echo $app_absolute_path; ?>images/icon_addvcard.gif" border="0" align="absmiddle"></a>
		</td>
		</tr>
		</table>
<?
	}
	elseif (isAllowed(array(402), $_SESSION['permissions']))
	{
?>
		<table>
		<tr>
		<td>
		<input type="checkbox" name="print[]" value="<? echo $contact_id; ?>">
		</td>
		<td></td>
		<td></td>
		</tr>
		</table>
<?
	}
?>
</td>
<tr> 
<td colspan="5" valign="top" style="font-family: Tahoma, sans-serif; font-size: 1px; color: #cccccc; border-bottom: 1px solid #cccccc;">&nbsp; 
</td>
</tr>
<?
        }
    }
?>
</TABLE>
</table>
<br>
<?
	if (isAllowed(array(401, 402), $_SESSION['permissions']))
	{
?>
	<table width="100%" cellpadding="1" cellspacing="0">
	<tr>
		<td class="ar11_content" align="right">
		* Select checkbox located on the right to print or save the contacts  
		<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_print.gif" align="middle"> 
		<input type="hidden" name="Submit" value="Submit">&nbsp;
		<a href="#"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_selectall.gif" onclick="SetAllCheckBoxes('print', 'print[]', true);" align="middle" border="0"></a>&nbsp;
		<a href="#"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_deselectall.gif" onclick="SetAllCheckBoxes('print', 'print[]', false);" align="middle" border="0"></a> 
		</td>
	</tr>
	</table>
<?
	}
?>
</form>
</td>
</tr>
</table>
<!--************************************************************************-->		
<? include_once($app_absolute_path."includes/template_footer.php"); ?>