<? 
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	// ** CHECK FOR LOGIN **
	checkForLogin();

	if ($_SESSION['usertype'] != "admin") 
	{
		header("location: index.php");
	}
	
	include_once("../classes/audit_trail.php");
	$audit_trail = new audit_trail();
	
	// ** END INITIALIZATION *******************************************************	
	
	$mode = $_GET['mode'];
	$id = $_GET['id'];
	$refid = $_GET['refid'];
	if ($mode == 2)
	{
		mysql_query("DELETE FROM " . TABLE_CONTRIBUTION . " WHERE refid = $refid", $db_link);
		header("location: edit.php?id=$id");
	}
	
	if ($mode == 1)
	{
		$data = mysql_query("SELECT * FROM " . TABLE_CONTRIBUTION . " WHERE id=$id AND refid=$refid", $db_link); 
		$t_data = mysql_fetch_array($data);
		$temp_refid = $t_data['refid'];
		$temp_committee = $t_data['committee'];
		$temp_position = $t_data['position'];
		$temp_year  = $t_data['year'];
		$temp_id  = $t_data['id'];
	}
	
	include_once("local_config.php");
	include_once($app_absolute_path."includes/template_header.php");
	
	/*if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		$update_committee = trim($_POST['committee']);
		$update_position = trim($_POST['position']);
		$update_year = $_POST['year'];
		
		if (empty($update_committee))
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
				
            	<div class="ar11_content">Please fill in a comitteee/position in order to create a contribution.</div>
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
				<form action="contriedit.php?id=<? echo $id; ?>&refid=<? echo $refid; ?>&mode=1" method="post" enctype="multipart/form-data">
				<input type="hidden" name="committee" value="<? echo $update_committee; ?>">
				<input type="hidden" name="position" value="<? echo $update_position; ?>">
				
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
		
		$areaupd = "UPDATE " . TABLE_CONTRIBUTION . " SET 
					committee = '$update_committee', 
					position = '$update_position', 
					year = '$update_year' 
					WHERE refid = $refid AND id = $id";

		$execute= mysql_query($areaupd, $db_link);
		if ($execute == 1)
		{
			header("location: edit.php?id=$id");
		}
	}*/
	
 ?>
 <SCRIPT LANGUAGE="JavaScript">
	<!--

	function resetform(x) 
	{
		document.getElementsByName('committee').item(0).value = '';
		document.getElementsByName('position').item(0).value = '';
		document.getElementsByName('year').item(0).value = '';
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
            <td>
			<img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="2" height="13">
			</td>
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
            
    <td height="20">&nbsp; </td>
          </tr>
          <tr>
            <td>
<?
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		$update_committee = trim($_POST['committee']);
		$update_position = trim($_POST['position']);
		$update_year = $_POST['year'];
		
		if ( (empty($update_committee)) || (empty($update_position)) )
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
				
            	<div class="ar11_content">Please fill in a comitteee/position in order to create a contribution.</div>
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
				<form action="contriedit.php?id=<? echo $id; ?>&refid=<? echo $refid; ?>&mode=1" method="post" enctype="multipart/form-data">
				<input type="hidden" name="committee" value="<? echo $update_committee; ?>">
				<input type="hidden" name="position" value="<? echo $update_position; ?>">
				
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
		
		$areaupd = "UPDATE " . TABLE_CONTRIBUTION . " SET 
					committee = '$update_committee', 
					position = '$update_position', 
					year = '$update_year' 
					WHERE refid = $refid AND id = $id";

		$execute= mysql_query($areaupd, $db_link);
		
		$audit = mysql_query("SELECT fullname FROM " . TABLE_CONTACT . " WHERE id = '$id'", $db_link);
		$t_audit = mysql_fetch_array($audit);
		$temp_name = $t_audit['fullname'];
		$audit_trail->writeLog($_SESSION['username'], "contact", "Update contribution on $temp_name");
		//if ($execute == 1)
		//{
			//header("location: edit.php?id=$id");
			echo "<meta http-equiv=\"refresh\" content=\"0;URL=edit.php?id=$id\">";
		//}
	}
?>
			<table width="50%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td colspan="5" class="m5_td_header"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td class="m5_td_header"><strong>Edit Contact Contribution</strong></td>
                <td><div align="right"> &nbsp;</div></td>
              </tr>
            </table></td>
        </tr>
        <form method="post" action="contriedit.php?id=<? echo $id; ?>&refid=<? echo $refid; ?>" enctype="multipart/form-data">
          <tr> 
            <td width="27%" class="m5_td_content">Committee:</td>
            <td width="63%" class="m5_td_content"><input name="committee" type="text" class="inputbox" id="committee" value="<?=$temp_committee?>"></td>
          </tr>
          <tr> 
            <td width="27%" class="m5_td_content">Position:</td>
            <td width="63%" class="m5_td_content"> <input name="position" type="text" class="inputbox" id="position" value="<?=$temp_position?>"> 
            </td>
          </tr>
          <tr>
            <td class="m5_td_content">Year:</td>
            <td class="m5_td_content">
			<select name="year" size="1" id="year" class="inputbox">
		<? 
			$yearList = array("1990", "1991", "1992", "1993", "1993", "1994", "1995", "1996", "1997", "1998", "1999", "2000", "2001", "2002", "2003", "2004", "2005", "2006");
			echo "<option value=''>Please select</option>";
			$x = 0;
			while ($x < 18)
			{
				echo("<option value=$yearList[$x]");
				if ($yearList[$x] == $temp_year) 
				{
					echo(" selected");
				}
				echo(">$yearList[$x]</option>");
				$x++;
			}
		?>
		</select>
		</td>
          </tr>
          <tr> 
            <td width="27%" class="m5_td_content">&nbsp;</td>
            <td width="63%" class="m5_td_content"> <input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_submit.gif" align="middle"> 
              <input type="hidden" name="Submit" value="Submit"> <input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_reset.gif" align="middle" onClick="resetform(); return false;"> 
            </td>
          </tr>
        </form>
      </table>
</td>
          </tr>
          <tr>
            <td>
			<br>
			<a href="edit.php?id=<? echo $id; ?>"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" border="0"></a>
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