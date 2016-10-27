<?	
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);
	include_once("local_config.php");

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	// ** CHECK FOR LOGIN **
	checkForLogin();

	require_once('../includes/functions.php');
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}
	// ** END INITIALIZATION *******************************************************	
	// delete category and contacts
	
	$mode = $_GET['mode'];
	$id = $_GET['id'];
	$id2 = $_GET['id2'];
	$from = $_GET['from'];
	
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Yes"))
	{
		header("location: catass.php?id=$id");
	}

	//Delete contacts
	if ((!empty($_POST["Submit2"])) && ($_POST["Submit2"] == "Yes"))
	{
		header("location: save.php?id=$id2&mode=delete");
	}
	
	//Delete area of expertise
	if ((!empty($_POST["Submit3"])) && ($_POST["Submit3"] == "Yes"))
	{
		$id = $_POST['id'];
		//mysql_query("UPDATE " . TABLE_EXPERTLINK . " SET area_id = '' WHERE area_id = '$id'", $db_link);
		//mysql_query("DELETE FROM ". TABLE_EXPERTLINK ." WHERE area_id = $id LIMIT 1", $db_link); //Commented by Chia Boon 6th July 2006 05:16PM
		mysql_query("DELETE FROM ". TABLE_EXPERTLINK ." WHERE area_id = $id", $db_link); //Added by Chia Boon 6th July 2006 05:16PM
		mysql_query("DELETE FROM ". TABLE_EXPERTISE ." WHERE area_id = $id LIMIT 1", $db_link);
		header("location: areaman.php?do=delete");
		exit();
	}

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
<td height="20"><div align="right" class="ar11_content"></div></td>
</tr>
<tr> 
<td>
<table width="50%"  border="0" cellpadding="0" cellspacing="0" class="m5_table_outline">
<tr> 
<td class="m5_td_header">
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
<tr> 
<td class="m5_td_header"><strong>Confirmation</strong></td>
</tr>
</table>
</td>
</tr>
<tr> 
<td colspan="2" valign="top" class="m5_td_content">&nbsp;</td>
</tr>
<?
// for category deletion
if ($mode != "confirm" && $mode != "delete")
{
?>
        <tr> 
          <td colspan="2" class="m5_td_content" align="center"><strong>Are you sure to delete 
            this category?</strong> </td>
        </tr>
        <tr> 
          <td colspan="2" class="m5_td_content" align="center"> 
		  	<form method="post" action="confirm.php<? echo "?id=".$id; ?>" enctype=\"multipart/form-data\">
			  <input type="image" name="Submit" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_delete.gif" align="middle">
              <input type="hidden" name="Submit" value="Yes">
			  <a href="catman.php"><IMG SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_cancel.gif" BORDER=0 align="middle"></a>
            </form>
			</td>
        </tr>
        <?
}
// for contacts deletion
if ($mode == "confirm")
{
?>
        <tr> 
          <td colspan="2" class="m5_td_content" align="center"><strong>Are you sure to delete this contacts?</strong>
		  </td>
        </tr>
        <tr> 
		<td class="m5_td_content" colspan="2" align="center">
		<form method="post" action="confirm.php<? echo "?id2=".$id; ?>" enctype="multipart/form-data"> 
			<input name="Submit2" type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_delete.gif" align="middle" width="58" height="18">
  			<input type="hidden" name="Submit2" value="Yes">
			<? 
			if ($from == 'main')
			{
			?>
				<a href="edit.php<? echo "?id=".$id; ?>"><IMG SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_cancel.gif" BORDER=0 align="middle"></a>
			<?
			}
			else
			{
			?>
				<a href="address.php<? echo "?id=".$id; ?>"><IMG SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_cancel.gif" BORDER=0 align="middle"></a>
			<?
			}
			?>		
		</form>	
        </td>
		</tr>
 <?
}
// for area of expertise deletion
if ($mode == "delete")
{
?>
	<tr> 
	<td colspan="2" class="m5_td_content" align="center"><strong>Are you sure to delete this area of expertise?</strong></td>
	</tr>
	<tr> 
	<td class="m5_td_content" colspan="2" align="center">
	<form method="post" action="confirm.php" enctype="multipart/form-data"> 
	          <input name="Submit3" type="image" id="Submit3" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_delete.gif" align="middle" width="58" height="18">
	<input type="hidden" name="Submit3" value="Yes">
	<a href="areaman.php"><IMG SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_cancel.gif" BORDER=0 align="middle"></a>
	<input type="hidden" name="id" value="<? echo $id; ?>">
	</form>	
	</td>
	</tr>
<?
}
?>
</table></td>
</tr>
</table>
<!--*************************************************************************-->	
<? include_once($app_absolute_path."includes/template_footer.php"); ?>