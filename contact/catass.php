<?php
// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	session_start();

// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** CHECK FOR LOGIN **
	checkForLogin("admin","user");

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
 
// ** END INITIALIZATION *******************************************************
	$id = $_GET['id'];

	$check = "SELECT * FROM contact_groups WHERE groupid !=0 AND groupid !=1 AND groupid != 2 AND groupid = $id";
	$r_check = mysql_query($check, $db_link);
	$tbl_check = mysql_fetch_array($r_check);
	
	echo $c_id = $tbl_check['id'];
	
	if (empty($c_id))
	{
		header("location: catman.php?do=delete");
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=catman.php\">";
	}
	
	mysql_query("UPDATE " . TABLE_GROUPS . " SET groupid = 1 WHERE groupid = '$id'", $db_link);
	mysql_query("DELETE FROM " . TABLE_GROUPLIST . " WHERE groupid = '$id' AND groupid != 0 LIMIT 1", $db_link);
	
	/*$check = "SELECT * FROM contact_groups WHERE groupid !=0 AND groupid !=1 AND groupid != 2 AND groupid = $id";
	$r_check = mysql_query($check, $db_link);
	$tbl_check = mysql_fetch_array($r_check);
	
	echo $c_id = $tbl_check['id'];
	
	if (empty($c_id))
	{
		echo "xxxxxxxxxxxxxxxxxxxxxxxx";
		header("location: catman.php");
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=catman.php\">";
	}
	die();*/
	
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		while (list ($x_key, $x_gid) = each ($_POST['groups'])) 
		{	
			mysql_query("UPDATE " . TABLE_GROUPS . " SET groupid = '$x_gid' WHERE groupid = 1", $db_link);
			header("location: catman.php?do=delete");
		}
	}
?>
<? 
include_once("local_config.php");
include_once($app_absolute_path."includes/template_header.php");
?>
<!--*************************************************************************-->	
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
                <td class="breadcrumbs">&nbsp;<? require_once('breadcrumb.php'); ?></td>
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
                <td height="20">
				<div align="right" class="ar11_content"></div></td>
              </tr>
              <tr> 
                <td> 
				
<form action="catass.php" method="post" enctype="multipart/form-data">
<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
<tr> 
<td colspan="2" class="m5_td_header">
<table width="100%"  border="0" cellspacing="0" cellpadding="0" class="m5_td_header">
<tr> 
<td><strong>Re-assign Categories</strong></td>
<td><div align="right">&nbsp; </div></td>
</tr>
</table>
</td>
</tr>
<tr> 
                        <td colspan="2" valign="top" class="m5_td_content"><strong>Please 
                          select a new category to reassign the contacts. Click 
                          cancel to reassign to no categories </strong></td>
</tr>
<tr> 
<td valign="top" class="m5_td_content" width="27%"> 
<?php
	$id = '0';
	// Display Group Checkboxes.
	$groupsql = "SELECT grouplist.groupid, groupname, id 
				 FROM contact_grouplist AS grouplist
				 LEFT JOIN contact_groups AS groups
				 ON grouplist.groupid=groups.groupid AND id=$id
				 WHERE grouplist.groupid >= 3
				 ORDER BY groupname";
	$r_grouplist = mysql_query($groupsql, $db_link);
	$numGroups = mysql_num_rows($r_grouplist);
	$numGroups = round($numGroups/2);  // assigns to $numGroups the number of Groups to display in the first column.
	$x = 0;
	$groupCheck = ""; 

	// COLUMN 1
	// $x is checked FIRST because if that fails, $tbl_grouplist will have already been evaluated
	while ( ($x < $numGroups) && ($tbl_grouplist = mysql_fetch_array($r_grouplist)) ) 
	{
		$group_id = $tbl_grouplist['groupid'];
		$group_name = $tbl_grouplist['groupname'];
		if ( $tbl_grouplist['id'] == $id ) {
			$groupCheck = " CHECKED";
		}
		echo("<INPUT TYPE=\"radio\" NAME=\"groups[]\" VALUE=\"$group_id\"$groupCheck><B>$group_name</B>\n<BR>");
		//reset $groupCheck so that it doesn't stay set if the next ID does not equal $id.
		$groupCheck = "";
		$x++;
	}
?>
</td>
<td class="m5_td_content"> 
<?php
	// COLUMN 2
	while ($tbl_grouplist = mysql_fetch_array($r_grouplist)) 
	{
		$group_id = $tbl_grouplist['groupid'];
		$group_name = $tbl_grouplist['groupname'];
		if ( $tbl_grouplist['id'] == $id ) 
		{
			$groupCheck = " CHECKED";
		}
		echo("<INPUT TYPE=\"radio\" NAME=\"groups[]\" VALUE=\"$group_id\"$groupCheck><B>$group_name</B>\n<BR>");
		//reset $groupCheck so that it doesn't stay set if the next ID does not equal $id.
		$groupCheck = "";
	}
?>
</td>
</tr>
<tr>
<td valign="top" class="m5_td_content">
<!--<input type="submit" name="Submit" value="Submit">-->
<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_submit.gif" align="middle">
<input type="hidden" name="Submit" value="Submit">
<!--<input type="submit" name="Submit2" id="Submit2" value="Cancel">-->
<a href="catman.php"><IMG SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_cancel.gif" BORDER=0 align="middle"></a>
</td>
<td class="m5_td_content">&nbsp;</td>
</tr>
</table>
 </form>
</td>
</tr>
<tr> 
<td>&nbsp;</td>
</tr>
<tr> 
<td>&nbsp;</td>
</tr>
<tr> 
<td>&nbsp;</td>
</tr>
<tr> 
<td width="95%">&nbsp;</td>
</tr>
</table>
<!--*************************************************************************-->	
<? include_once($app_absolute_path."includes/template_footer.php"); ?>
