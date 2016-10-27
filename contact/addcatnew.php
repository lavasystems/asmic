<? 
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);
	include_once("local_config.php");
	require_once('../includes/functions.php');

	
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}
	
	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	// ** CHECK FOR LOGIN **
	//checkForLogin();

	// ** END INITIALIZATION *******************************************************	
	
	include_once($app_absolute_path."includes/template_header.php");
?>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	function resetform(x) 
	{
		document.getElementsByName('groupname').item(0).value = '';
		document.getElementsByName('groupdesc').item(0).value = '';
	}
	// -->
</SCRIPT>
<!--*************************************************************************-->		
		<table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="4%" rowspan="16"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="24" height="8"></td>
            <td>&nbsp;</td>
            <td width="1%" rowspan="16"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="12" height="8"></td>
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
            <td height="20">&nbsp;
			</td>
          </tr>
          <tr>
            <td>
<?
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		$groupname = trim($_POST['groupname']);
		$groupdesc = trim($_POST['groupdesc']);
		
		$check = "SELECT groupname FROM " . TABLE_GROUPLIST . " WHERE groupname = '$groupname'";
		$r_check = mysql_query($check, $db_link);
		$tbl_check = mysql_fetch_array($r_check);
		$c_group_name = $tbl_check['groupname'];

		if (empty($groupname))
		{
			?>
				<table align="left">
				<tr>
				<td>
				<table align="center" width="400" border="1" bordercolor="#FF0000" cellpadding="1" cellspacing="0">
				<tr>
				<td align="center">
				<font style="color:#FF0000;"><?php echo $lang['ERROR_ENCOUNTERED'] ?></font> 
				<p><font class="ar11_content">The following error occurred:</font><br><br>
				
            	<div class="ar11_content">The character you have entered is invalid. Please fill in only alphanumeric value to create a new Contact Category.</div>
				<p><font class="ar11_content">Please press on the "Back" button shown below to return to the previous screen and correct any possible mistake.</font>
				<p>
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
				<br>
				<form action="addcatnew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddengroupname" value="<? echo $groupname; ?>">
				<input type="hidden" name="hiddengroupdesc" value="<? echo $groupdesc; ?>">
				
				<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle">
 				<input type="hidden" name="Submithidden" value="Submit">
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
				<td width="95%">&nbsp;</td>
				</tr>
				</table>
			<?
				include_once($app_absolute_path."includes/template_footer.php");
				exit();
		}

		if (account_namevalid($groupname) == false)
		{
			?>
				<table align="left">
				<tr>
				<td>
				<table align="center" width="400" border="1" bordercolor="#FF0000" cellpadding="1" cellspacing="0">
				<tr>
				<td align="center">
				<font style="color:#FF0000;"><?php echo $lang['ERROR_ENCOUNTERED'] ?></font> 
				<p><font class="ar11_content">The following error occurred:</font><br><br>
				
            	<div class="ar11_content">The character you have entered is invalid. Please fill in only alphanumeric value to create a new Contact Category.</div>
				<p><font class="ar11_content">Please press on the "Back" button shown below to return to the previous screen and correct any possible mistake.</font>
				<p>
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
				<br>
				<form action="addcatnew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddengroupname" value="<? echo $groupname; ?>">
				<input type="hidden" name="hiddengroupdesc" value="<? echo $groupdesc; ?>">
				
				<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle">
 				<input type="hidden" name="Submithidden" value="Submit">
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
				<td width="95%">&nbsp;</td>
				</tr>
				</table>
			<?
				include_once($app_absolute_path."includes/template_footer.php");
				exit();
		}
		
		if (!empty($c_group_name))
		{
			//reportScriptError("Duplicate category names");
			?>
				<table align="left">
				<tr>
				<td>
				<table align="center" width="400" border="1" bordercolor="#FF0000" cellpadding="1" cellspacing="0">
				<tr>
				<td align="center">
				<br>
				<font style="color:#FF0000;"><?php echo $lang['ERROR_ENCOUNTERED'] ?></font> 
				<p><font class="ar11_content">The following error occurred:</font><br><br>
				<div class="ar11_content">Duplicated Category name.</div>
				<p><font class="ar11_content">Please press on the "Back" button shown below to return to the previous screen and correct any possible mistake.</font>
				<p>
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
				<br>
				<form action="addcatnew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddengroupname" value="<? echo $groupname; ?>">
				<input type="hidden" name="hiddengroupdesc" value="<? echo $groupdesc; ?>">
				
				<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle">
 				<input type="hidden" name="Submithidden" value="Submit">
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
				<td width="95%">&nbsp;</td>
				</tr>
				</table>
			<?
				include_once($app_absolute_path."includes/template_footer.php");
				exit();
		}
		
		if (empty($groupname))
		{
			//reportScriptError("<font align=\"center\" class=\"ar11_content\">Please provide a name for a category to exist<br><br><a href=\"addcatnew.php\"><img src=\"../images/m5/m5_btn_back.gif\" border=\"0\"></a></font>");
			?>
				<table align="left">
				<tr>
				<td>
				<table align="center" width="400" border="1" bordercolor="#FF0000" cellpadding="1" cellspacing="0">
				<tr>
				<td align="center">
				<font style="color:#FF0000;"><?php echo $lang['ERROR_ENCOUNTERED'] ?></font> 
				<p><font class="ar11_content">The following error occurred:</font><br><br>
				
            <div class="ar11_content">Please fill in a category name in order 
              to create a new Contact Category.</div>
				<p><font class="ar11_content">Please press on the "Back" button shown below to return to the previous screen and correct any possible mistake.</font>
				<p>
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
				<br>
				<form action="addcatnew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddengroupname" value="<? echo $groupname; ?>">
				<input type="hidden" name="hiddengroupdesc" value="<? echo $groupdesc; ?>">
				
				<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" align="middle">
 				<input type="hidden" name="Submithidden" value="Submit">
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
				<td width="95%">&nbsp;</td>
				</tr>
				</table>
			<?
				include_once($app_absolute_path."includes/template_footer.php");
				exit();
		}
		
		$r_newGroupID = mysql_query("SELECT groupid FROM " . TABLE_GROUPLIST . " ORDER BY groupid DESC LIMIT 1", $db_link);
		$t_newGroupID = mysql_fetch_array($r_newGroupID);
		$newGroupID = $t_newGroupID['groupid'];
		$newGroupID = $newGroupID + 1;
		
		$groupupd = "INSERT INTO " . TABLE_GROUPLIST . " VALUES ($newGroupID, '$groupname', '$groupdesc')";
		$execute= mysql_query($groupupd, $db_link);
		if ($execute == 1)
		{
			//header("location: catman.php");
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=catman.php?do=new&what=$newGroupID\">";
		}
	}
?>
<form method="post" action="addcatnew.php" enctype="multipart/form-data">
<table width="50%" border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
<tr> 
<td colspan="5" class="m5_td_header">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<tr> 
<td class="m5_td_header"> <strong> Add Category </strong> 
</td>
</tr>
</table>
</td>
</tr>
<tr>
<td width="27%" class="m5_td_content">Category name:</td>
<td width="100%" class="m5_td_content"><input type="text" name="groupname" value="<? echo $_POST['hiddengroupname']; ?>" class="m5_formTextbox">
</td>
</tr>
<tr>
<td width="27%" class="m5_td_content" valign="top">Category description:</td>
<td width="100%" class="m5_td_content"><textarea name="groupdesc" style="width:150px;" class="m5_formTextarea"><? echo $_POST['hiddengroupdesc']; ?></textarea>
</td>
</tr>
<tr>
<td width="27%" class="m5_td_content">&nbsp;</td>
<td width="100%" class="m5_td_content">
<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_submit.gif" align="middle">
<input type="hidden" name="Submit" value="Submit">
<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_reset.gif" align="middle" onClick="resetform(); return false;">
</td>
</tr>
</table>
</form>

</td>
</tr>
<tr>
<td>
<br>
<a href="catman.php"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" border="0"></a></td>
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
</table>
<!--*************************************************************************-->	
<? include_once($app_absolute_path."includes/template_footer.php"); ?>
