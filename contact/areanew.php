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
	checkForLogin();

	// ** END INITIALIZATION *******************************************************	
	
	include_once($app_absolute_path."includes/template_header.php");
?>
<SCRIPT LANGUAGE="JavaScript">
	<!--
	function resetform(x) 
	{
		document.getElementsByName('areaname').item(0).value = '';
		document.getElementsByName('areadesc').item(0).value = '';
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
		$areaname = trim($_POST['areaname']);
		$areadesc = $_POST['areadesc'];
		
		$check = "SELECT area_name FROM " . TABLE_EXPERTISE . " WHERE area_name = '$areaname'";
		$r_check = mysql_query($check, $db_link);
		$tbl_check = mysql_fetch_array($r_check);
		$c_area_name = $tbl_check['area_name'];

		if (empty($areaname))
		{
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
				<div class="ar11_content">The character you have entered is invalid. Please fill in only alphanumeric value to create a new Area of Expertise.</div>
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
				<form action="areanew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddenareaname" value="<? echo $areaname; ?>">
				<input type="hidden" name="hiddenareadesc" value="<? echo $areadesc; ?>">
				
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
		
		if (account_namevalid($areaname) == false)
		{
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
				<div class="ar11_content">The character you have entered is invalid. Please fill in only alphanumeric value to create a new Area of Expertise.</div>
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
				<form action="areanew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddenareaname" value="<? echo $areaname; ?>">
				<input type="hidden" name="hiddenareadesc" value="<? echo $areadesc; ?>">
				
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
		
		if (!empty($c_area_name))
		{
			//reportScriptError("<font class\"ar11_content\">Duplicate area names</font>");
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
				<div class="ar11_content">Duplicated name of Area of Expertise.</div>
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
				<form action="areanew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddenareaname" value="<? echo $areaname; ?>">
				<input type="hidden" name="hiddenareadesc" value="<? echo $areadesc; ?>">
				
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
		
		if (empty($areaname))
		{
			//reportScriptError("<font class=\"ar11_content\">Please provide a name for an area to exist</font>");
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
				
            <div class="ar11_content">Please fill in a name in order to create 
              a new Area of Expertise.</div>
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
				<form action="areanew.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="hiddenareaname" value="<? echo $areaname; ?>">
				<input type="hidden" name="hiddenareadesc" value="<? echo $areadesc; ?>">
				
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
		
		$r_newAreaID = mysql_query("SELECT area_id FROM contact_expertise ORDER BY area_id DESC LIMIT 1", $db_link);
		$t_newAreaID = mysql_fetch_array($r_newAreaID);
		$newAreaID = $t_newAreaID['area_id'];
		$newAreaID = $newAreaID + 1;
		
		$areaupd = "INSERT INTO contact_expertise VALUES ($newAreaID, '$areaname', '$areadesc')";
		$execute= mysql_query($areaupd, $db_link);
		echo "<meta http-equiv=\"refresh\" content=\"0;URL=areaman.php?do=new&what=$newAreaID\">";
	}
?>
<table width="50%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
<tr> 
<td colspan="5" class="m5_td_header">

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<tr> 
<td class="m5_td_header"> <strong> Add New Area of Expertise</strong> 
</td>
</tr>
</table>

</td>
</tr>
<form method="post" action="areanew.php" enctype="multipart/form-data">
<tr>
<td width="27%" class="m5_td_content">Area name:</td>
<td width="100%" class="m5_td_content"><input name="areaname" type="text" id="areaname" value="<? echo $_POST['hiddenareaname']; ?>" class="m5_formTextbox"></td>
</tr>
<tr>
<td width="27%" class="m5_td_content" valign="top">Area description:</td>
<td width="100%" class="m5_td_content"><textarea name="areadesc" class="m5_formTextarea" id="areadesc" style="width:150px;"><? echo $_POST['hiddenareadesc']; ?></textarea></td>
</tr>
<tr>
<td width="27%" class="m5_td_content">&nbsp;</td>
<td width="100%" class="m5_td_content">
<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_submit.gif" align="middle">
<input type="hidden" name="Submit" value="Submit">
<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_reset.gif" align="middle" onClick="resetform(); return false;">
</td>
</tr>
</form>
</table>

</td>
</tr>
<tr>
<td>
<br>
<a href="areaman.php"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" border="0"></a></td>
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
