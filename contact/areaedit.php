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

	// ** END INITIALIZATION *******************************************************	
	
	$mode = $_GET['mode'];
	$id = $_GET['id'];
	
	if ($mode = 1)
	{
		$areasql = "SELECT * FROM contact_expertise WHERE area_id = '$id' LIMIT 1";
		$r_arealist = mysql_query($areasql, $db_link);
		$tlink = mysql_fetch_array($r_arealist);
		
		$area_id = $tlink['area_id'];
		$area_name = trim($tlink['area_name']);
		$area_desc = trim($tlink['area_desc']);
	}
		
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
            <td>
<?
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		$areaname = trim($_POST['areaname']);
		$areadesc = trim($_POST['areadesc']);

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
				
            	<div class="ar11_content">Please fill in a name in order to create a new Area of Expertise.</div>
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
				<form action="areaedit.php?id=<? echo $id; ?>&mode=1" method="post" enctype="multipart/form-data">
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
			//reportScriptError("Please provide a name for an area to exist");
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
				
            	<div class="ar11_content">The character you have entered is invalid. Please fill in only alphanumeric value to edit the Area of Expertise.</div>
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
				<form action="areaedit.php?id=<? echo $id; ?>&mode=1" method="post" enctype="multipart/form-data">
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
				
		$areaupd = "UPDATE contact_expertise SET area_name='$areaname', area_desc='$areadesc' WHERE area_id = '$id'";
		$execute= mysql_query($areaupd, $db_link);
		if ($execute == 1)
		{
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=areaman.php?do=update&what=$id\">";
		}
	}
?>			
<table width="50%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
<tr> 
<td colspan="5" class="m5_td_header">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<tr> 
<td class="m5_td_header"><strong>Edit Area of Expertise</strong></td>
</tr>
</table>
</td>
</tr>
<form method="post" action="areaedit.php<? echo "?id=".$id; ?>" enctype="multipart/form-data">
<tr>
            <td width="27%" class="m5_td_content">Area name:</td>
<td width="63%" class="m5_td_content">
<input name="areaname" type="text" id="areaname" value="<?=$area_name?>" class="m5_formTextbox"></td>
</tr>
<tr>
<td width="27%" class="m5_td_content" valign="top">Area description:</td>
<td width="63%" class="m5_td_content">
<textarea name="areadesc" class="m5_formTextarea" id="areadesc" style="width:150px;"><? echo ($area_desc); ?></textarea>
</td>
</tr>
<tr>
<td width="27%" class="m5_td_content">&nbsp;</td>
<td width="63%" class="m5_td_content">
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
<a href="areaman.php"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" border="0"></a>
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
<!--*************************************************************************-->	
<? 
include_once($app_absolute_path."includes/template_footer.php");
 ?>