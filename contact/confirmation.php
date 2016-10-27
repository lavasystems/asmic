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
	$list_refid = $_GET['refid'];
	$from = $_GET['from'];
	
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Yes"))
	{
		//echo "<br>".$id;
		//echo "<br>".$mode;
		//echo "<br>".$list_refid;
		header("location: contriedit.php?id=$id&refid=$list_refid&mode=2");
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

<tr> 
	<td colspan="2" class="m5_td_content" align="center"><strong>Are you sure to delete this ASM Contribution?</strong> 
	</td>
</tr>
<tr> 
	<td colspan="2" class="m5_td_content" align="center"> 
		<form method="post" action="confirmation.php<? echo "?id=".$id; ?>&refid=<? echo $list_refid; ?>&mode=2" enctype=\"multipart/form-data\">
		<input type="image" name="Submit" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_delete.gif" align="middle">
		<input type="hidden" name="Submit" value="Yes">
		<a href="edit.php?id=<? echo $id; ?>"><IMG SRC="<? echo $app_absolute_path; ?>images/m5/m5_btn_cancel.gif" BORDER=0 align="middle"></a>
		</form>
	</td>
</tr>

</table></td>
</tr>
</table>
<!--*************************************************************************-->	
<? include_once($app_absolute_path."includes/template_footer.php"); ?>