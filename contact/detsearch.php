<?
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);
	session_start();
	require_once('local_config.php');
	require_once($app_absolute_path.'includes/functions.php');
	//include('/languanges/english.php');
	
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}
	
	// ** OPEN CONNECTION TO THE DATABASE **
		$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);
		
	$options = new Options();
		
	// ** END INITIALIZATION *******************************************************
	include_once($app_absolute_path."includes/template_header.php");
?>
<script language="JavaScript" type="text/JavaScript">
<!--
	function resetform(goToEntry) 
	{
		document.getElementsByName('goToName').item(0).value = '';
		document.getElementsByName('goToAdress').item(0).value = '';
		document.getElementsByName('goToEmail').item(0).value = '';
		document.getElementsByName('goToGroup').item(0).value = '';
		document.getElementsByName('arealist').item(0).value = '';
		document.getElementsByName('conlist').item(0).value = '';
		document.getElementsByName('countrylist').item(0).value = '';
	}
// -->
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
                <td><table width="100%"  border="0" cellpadding="0" cellspacing="0">
                    <tr> 
                      <td height="16">
					  <? require_once('navigation.php'); ?>
					  </td>
                    </tr>
                  </table></td>
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
                <td class="ar11_content">Note : Please make sure you enter at least one field in the <b>Detailed Search</b> form below.</td>
              </tr>
	       <tr> 
                <td>&nbsp;</td>
              </tr>
                <td>
	<form name="goToEntry" method="post" action="searchnew.php?from=advan" enctype="multipart/form-data">
		<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td colspan="4" class="m5_td_header">
		  
		  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td class="m5_td_header"><strong>Detailed Search</strong>
				</td>
              </tr>
            </table>
			
			</td>
        </tr>
        <tr> 
          <td colspan="2" class="m5_td_content">&nbsp;</td>
        </tr>

          <tr> 
            <td width="20%" valign="top" class="m5_td_content">&nbsp;Name</td>
            <td valign="top" class="m5_td_content">
			<input name="goToName" type="text" size="30" value="<? echo ($_POST['byname']); ?>" class="m5_formTextbox">
			</td>
          </tr>
          <tr> 
            <td width="20%" valign="top" class="m5_td_content">&nbsp;Address</td>
            <td valign="top" class="m5_td_content"> 
			<input name="goToAdress" type="text" size="30" value="<? echo ($_POST['byaddress']); ?>" class="m5_formTextbox">
			</td>
          </tr>
		  <tr>
		  	<td width="20%" valign="top" class="m5_td_content">&nbsp;Country</td>
			<td valign="top" class="m5_td_content">
			<SELECT NAME="countrylist" CLASS="inputbox">
            <?php
				// -- GENERATE COUNTRY SELECTION LIST --
				foreach ($country as $country_id=>$val) 
				{
					$sortarray[$country_id] = strtr($val,"ÀÁÂÃÄÅÈÉÊ€ËÌÍÎÏÑÒÓÔÕÖÙÚÛÜİàáâãäåèéêëìíîïñòóôõöùúûüıÿ", "AAAAAAAEEEEIIIINOOOOOUUUUYaaaaaaeeeeiiiinooooouuuuyy");
				}
				asort($sortarray);
								
				$addressOK=0;
				foreach(array_keys($sortarray) as $country_id) 
				{
					echo("<option value='$country_id'");										
					if ($country_id == $_POST['address_country'])
					{
						echo(" selected");
					}
					echo ">" . $country[$country_id] . "</option>\n";
				}
			?>
            </SELECT>
			</td>
		  </tr>
            <td valign="top" width="20%" class="m5_td_content">&nbsp;<span>Email</span></td>
            <td valign="top" class="m5_td_content"> 
			<input name="goToEmail" type="text" size="30" value="<? echo ($_POST['byemail']); ?>" class="m5_formTextbox">
			</td>
          </tr>
          <tr> 
            <td valign="top" width="20%" class="m5_td_content">&nbsp;<span>Category</span></td>
            <td valign="top" class="m5_td_content"> 
			<select name="goToGroup" size="1" id="goToGroup" class="inputbox">
			<option value="">(Please Select)</option>
			<?
				$listcategory = "SELECT groupid, groupname FROM ". TABLE_GROUPLIST ." WHERE groupid != 0 AND groupid != 1 AND groupid != 2 ORDER BY groupname ASC";
				$r_listcategory = mysql_query($listcategory, $db_link);
				while ($tbl_listcategory = mysql_fetch_array($r_listcategory)) 
				{
					$selectcategory= $tbl_listcategory['groupname'];
					echo "<option value='$selectcategory'";
					if ($selectcategory == $_POST['bycategory'])
					{	echo " selected";	}
					echo ">$selectcategory</option>";
				}
			
			?>
			</select>
			<!--<input name="goToGroup" type="text" size="30" value="<? echo ($_POST['bycategory']); ?>" class="m5_formTextbox">-->
			</td>
          </tr>
          <tr>
            <td valign="top" class="m5_td_content">&nbsp;Area of expertise</td>
            <td valign="top" class="m5_td_content">
		<select name="arealist" size="1" id="arealist" class="inputbox">
		<option value="">(Please Select)</option>		
		<? 
			$listsql = "SELECT area_name, area_id FROM " . TABLE_EXPERTISE . " AS expertlist ORDER BY area_name";
			$r_list = mysql_query($listsql, $db_link);
			
			while ($tbl_list = mysql_fetch_array($r_list)) 
			{
				$selectname = $tbl_list['area_name'];
				echo "<option value='$selectname'";
				if ($selectname == $_POST['byarea'])
				{	echo " selected";	}
				echo ">$selectname</option>";
			}
		?>
		</select>
			</td>
          </tr>
	  <tr>
            <td valign="top" class="m5_td_content">&nbsp;Contribution</td>
            <td valign="top" class="m5_td_content">
		<select name="conlist" size="1" id="conlist" class="inputbox">
		<option value="">(Please Select)</option>		
		<? 
			$listcontsql = "SELECT committee, id FROM " . TABLE_CONTRIBUTION . "  AS contributelist WHERE committee !='' GROUP BY committee ORDER BY committee";
			$con_list = mysql_query($listcontsql, $db_link);
			
			while ($tbl_listcont = mysql_fetch_array($con_list)) 
			{
				$con_name = $tbl_listcont['committee'];
				echo "<option value='$con_name'";
				if ($selectname == $_POST['bycontribute'])
				{	echo " selected";	}
				echo ">$con_name</option>";
			}
		?>
		</select>
			</td>
          </tr>
          <tr> 
            <td class="m5_td_content">&nbsp;</td>
            <td class="m5_td_content"> 
			<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_submit.gif" align="middle">
			<input type="hidden" name="submit" value="Submit"> 
			<input type="image" src="<? echo $app_absolute_path; ?>images/m5/m5_btn_reset.gif" onClick="resetform();" align="middle">
			</td>
          </tr>
      </table>
	      </form>
		  </td>
              </tr>
              <tr> 
                <td>
<a href="list.php"><img src="<? echo $app_absolute_path; ?>images/m5/m5_btn_back.gif" border="0"></a></td>
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