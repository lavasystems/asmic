<?php
// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASSES);
	session_start();
	require_once('local_config.php');
	
// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);
	
	require_once('../includes/functions.php');
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}

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

include_once($app_absolute_path."includes/template_header.php");
?>
<!--************************************************************************************-->
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
<tr>
<td width="4%" rowspan="16"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="24" height="8"></td>
<td>&nbsp;</td>
<td width="1%" rowspan="16"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="12" height="8"></td>
</tr>
<tr>
<td class="module_title">Contact Management</td>
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
<td>&nbsp;</td>
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
<td>
<?
	require_once($app_absolute_path . "classes/user.php");
	$objUser = new User();
	$isUser = $objUser->isUser($id);
	if ($isUser > 0)
	{
		$status = "Member";
	}
	else 
	{
		$status = "Non-member";
	}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="1" class="ar11_content">
<tr>
<td valign="top" align="right" width="100%">
<b>Status: <? echo $status; ?></b></td>
</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
<tr>
<td>

	<table width="100%"  border="0" cellspacing="0" cellpadding="0">
	<tr> 
	<td class="m5_td_fieldname">
	<strong>
	<?php 
		echo("$contact->fullname");
	?>
	</strong>
	</td>
	<td class="m5_td_fieldname">
	<div align="right"><b>Category:</b>  
	<?php
		// LIST GROUPS
		$tbl_groups = mysql_fetch_array($r_groups);
		$groupname = stripslashes( $tbl_groups['groupname'] );
		$group_id = $tbl_groups['groupid'];
	
		 // format for group links
		$Groups = "<strong><A HREF=\"" . FILE_LIST . "?groupid=" . $group_id . "\" CLASS=\"link_menumodule\">" . $groupname . "</A></strong>";
		while ( $tbl_groups = mysql_fetch_array($r_groups) ) 
		{
			$groupname = stripslashes( $tbl_groups['groupname'] );
			$group_id = $tbl_groups['groupid'];
			$Groups = $Groups . ", <strong><A HREF=\"" . FILE_LIST . "?groupid=" . $group_id . "\" CLASS=\"link_menumodule\">" . $groupname . "</A></strong>";
		}
		echo($Groups);
	?>
	</div>
	</td>
	</tr>
	<tr> 
	<td colspan="2" class="m5_td_content">
	<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=10 WIDTH="100%">
	<TR VALIGN="top">
	<td>
	
		<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=10 WIDTH="100%">
		<TR VALIGN="top">
		<TD ROWSPAN="4" class="m5_td_content">
		<TD ROWSPAN="4" class="m5_td_content">
		<?
			echo("<IMG SRC=\"");
			if ($contact->picture_url) 
			{ 
				echo(PATH_MUGSHOTS . $contact->picture_url); 
			} 
			else 
			{ 
				echo("images/nopicture.gif"); 
			}
			echo("\" BORDER=\"1\" ALT=\"\">\n");
		?>
		</TD>
		
		<tr valign="top" align="right">
		<td colspan="3" class="m5_td_content">&nbsp;
<?		
	if (($_SESSION['usertype'] == "admin")) 
	{
		if ($isUser > 0)
		{
?>
			<P><a href="checkuser.php?id=<? echo $id; ?>"><B>Edit user password and groups</B></a>
			<BR><a href="<? echo $app_absolute_path; ?>library/lib_members.php?action=detail&id=<? echo $id; ?>&start=contact"><B>View all books borrowed by this contact</B></a>
<?
		}
		else
		{
?>
			<P><a href="checkuser.php?id=<? echo $id; ?>"><B>Convert this contact to user</B></a>
<?
		}
	}
?>
		</td>
		</tr>
		
		<tr>
		<td colspan=3 valign="top" style="font-family: Tahoma, sans-serif; font-size: 1px; color: #FFFFFF; border-bottom: 1px solid #000000;">&nbsp;
		</td>
		</tr>
		<TD WIDTH="40%" class="m5_td_content" VALIGN="top">
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
				$address_country = $tbl_address['country'];
		
				echo "<P>\n<B>" . (($contact->primary_address == $address_refid) ? $lang[LBL_PRIMARY_ADDRESS] : $lang[LBL_ADDRESS]);
				echo "</B>\n";
				if ($address_line1) { echo "\n<BR>$address_line1"; }
				if ($address_city OR $address_state OR $address_zip) { echo "\n<BR>"; }
				if ($address_city) { echo "$address_city"; }
				if ($address_city AND $address_state) { echo ", "; }
				if ($address_state) { echo "$address_state"; }
				if ($address_zip) { echo " $address_zip"; }
				if ($address_phone1) { echo "\n<BR>$address_phone1"; }
				if ($address_phone2) { echo "\n<BR>$address_phone2"; }
				if ($address_country) 
				{ 
					echo "\n<br>$country[$address_country]";
				}
				//echo "$address_country";
			}
			// ** E-MAIL **
			$tbl_email = mysql_fetch_array($r_email);
			$email_address = stripslashes( $tbl_email['email'] );
			$email_type = stripslashes( $tbl_email['type'] );
			if ($email_address) 
			{
				echo("<P>\n<B>$lang[LBL_EMAIL]</B>\n");
					if ($options->useMailScript == 1) 
					{
						echo("<BR><A HREF=\"mailto:$email_address\">$email_address</A>");
					}
					echo("\n");
			}
		?>
		</TD>
		<td style="font-family: Tahoma, sans-serif; font-size: 1px; color: #FFFFFF; border-left: 1px solid #000000;">&nbsp;
		</td>
		<TD WIDTH=400 class="m5_td_content" VALIGN="top">
		<?
			if (isAllowed(array(401), $_SESSION['permissions']))
			{
		?>
				<table width="100%" border="0">
					<tr>
					              <td class="m5_td_content" colspan="3"><b>Confidential 
                                    Information</b></td>
					<td class="m5_td_content" colspan="2">&nbsp;</td>
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
					?>
					<tr>
					<td class="m5_td_content" colspan="3">
					<? 
						if ($list_con_line1)
						echo $list_con_line1."<br>";
						if ($list_con_city)
						echo $list_con_city.",";
						if ($list_con_state)
						echo $list_con_state.",";
						if ($list_con_zip)
						echo $list_con_zip."<br>";
						if ($list_con_phone1)
						echo $list_con_phone1."<br>";
						if ($list_con_phone2)
						echo $list_con_phone2."<br>";
						if ($list_con_country) 
						{ 
							echo "$country[$list_con_country]";
						}
					?>
					</td>
					<td class="m5_td_content" width="20">&nbsp;</td>
					<td class="m5_td_content" width="20">&nbsp;</td>
					</tr>
					<?
						}
					?>
				</table>
		<?
			}
		?>
		<!--TABLE BORDER-->
		<table width="100%" border="0" cellspacing="1" class="m5_table_inside">
			<tr>
			<td class="m5_td_hdrtbl" colspan="3">Area of expertise</td>
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
			<td class="m5_td_ctntbl" colspan="3"><? echo $list_areaname; ?></td>
			</tr>
			<?
				}
			?>
		</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td class="m5_td_content" height="30" valign="bottom">
<b>ASM Contributions</b>
</td>
</tr>
</table>
		<!--TABLE DIVIDER-->
		<table width="100%" border="0" cellspacing="1" class="m5_table_inside">
			<tr>
			<td class="m5_td_hdrtbl"><b>Committee</b></td>
			<td class="m5_td_hdrtbl"><b>Position</b></td>
			<td class="m5_td_hdrtbl"><b>Year</b></td>
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
				if ( (!empty($list_committee)) && (!empty($list_position)) )
				{ 
		?>
					<tr>
					<td class="m5_td_ctntbl"><? echo $list_committee; ?></td>
					<td class="m5_td_ctntbl"><? echo $list_position; ?></td>
					<td class="m5_td_ctntbl"><? echo $list_year; ?></td>
					</tr>
		<?
				}
			}
		?>
		</table>
		</TD>
		</TR>
		</TABLE>
	
	
	</td>
	</tr>
	</table>
	
	</td>
	</tr>
	</table>
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td>
<table width="100%" border="0">
<tr>
<td>
<table width="100%" border="0">
<tr>
<td width="50%" align="left">
<?
	if (isAllowed(array(401), $_SESSION['permissions'])) 
	{
?>
		<a href="print.php?id=<?php echo($id); ?>"><IMG title="Print this contact" SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_print.gif" BORDER=0></a>
		<A HREF="<?php echo(FILE_EDIT); ?>?id=<?php echo($id); ?>"><IMG title="Edit this contact" SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_edit.gif" BORDER=0></A> 
		<A HREF="confirm.php?id=<?php echo($id); ?>&mode=confirm"><IMG title="Delete this contact" SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_delete.gif" BORDER=0></A> 
<?
	}
?>
</td>
<td width="50%" align="right">
	<table width="100"  border="0" align="right" cellpadding="0" cellspacing="1" class="m5_table_outline">
	  <tr>
		<td width="60" class="m5_td_content"><div align="right"><span class="fontcolorblue">&lsaquo;</span> <a href="<?php echo(FILE_ADDRESS); ?>?id=<?php echo($prev); ?>" class="hyperlink">Previous</a></div></td>
		<td width="40" class="m5_td_content"><div align="right"><a href="<?php echo(FILE_ADDRESS); ?>?id=<?php echo($next); ?>" class="hyperlink">Next</a> <span class="fontcolorblue">&rsaquo;</span></div></td>
	  </tr>
	</table>
</td>
</tr>
</table>

</td>
<tr>
<td class="ar11_content">
<?php echo $lang[LAST_UPDATE].' '.($contact->last_update).'.'; ?>
</td>
</tr>

</table>

</td>
</tr>
</table>
<!--************************************************************************************-->		
<? include_once($app_absolute_path."includes/template_footer.php"); ?>

