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

	if ($_SESSION['usertype'] != "admin") 
	{
		header("location: index.php");
	}

// ** END INITIALIZATION *******************************************************
	if ( ($_GET['id']) && ($_GET['areaid']) )
	{
		$id = $_GET['id'];
		$areaid = $_GET['areaid'];
		
		mysql_query("DELETE FROM " . TABLE_EXPERTLINK . " WHERE id=$id AND area_id=$areaid LIMIT 1",  $db_link);
		header("Location: address.php?id=$id");
		exit();
	}

include_once($app_absolute_path."includes/template_header.php");
?>
<!--*************************************************************************-->
		
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="4%" rowspan="17"><img src="../images/spacer.gif" width="24" height="8"></td>
    <td>&nbsp;</td>
    <td width="1%" rowspan="17"><img src="../images/spacer.gif" width="12" height="8"></td>
  </tr>
  <tr> 
    <td class="module_title">ASM Contact</td>
  </tr>
  <tr> 
    <td class="breadcrumbs">
      <? require_once('breadcrumb.php'); ?>
    </td>
  </tr>
  <tr> 
    <td><img src="../images/spacer.gif" width="2" height="13"></td>
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
<?
	$what = $_GET['what'];
	
	$show = "SELECT * FROM contact_expertise where area_id = '$what'";
	$getshow = mysql_fetch_array(mysql_query($show,  $db_link));
	
	if ($_GET['do'] == "update")
	{
?>
  <tr>
    <td height="20" class="ar11_content">&nbsp;
	<font color="#FF0000">The area of expertise "<? echo $getshow['area_name']; ?>" has been successfully updated</font>
	</td>
  </tr>
<?
	}

	if ($_GET['do'] == "new")
	{
?>
  <tr>
    <td height="20" class="ar11_content">&nbsp;
	<font color="#FF0000">The new area of expertise "<? echo $getshow['area_name']; ?>" has been successfully created</font>
	</td>
  </tr>
<?
	}
	
	if ($_GET['do'] == "delete")
	{
?>
  <tr>
    <td height="20" class="ar11_content">&nbsp;
	<font color="#FF0000">The area of expertise has been successfully deleted</font>
	</td>
  </tr>
<?
	}
?> 	 
	 
  <tr> 
    <td height="20">&nbsp; </td>
  </tr>
  <tr> 
    <td> <img title="Add new area of expertise" src="<? echo $app_absolute_path; ?>images/icon_addexpertise.gif" align="absmiddle">&nbsp; 
      <a href="areanew.php" class="ar11_content"><strong>Add New Area of Expertise</strong></a> 
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td> <table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td colspan="5" class="m5_td_header"> <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td  class="m5_td_header"><strong>Area of Expertise Management</strong> 
                </td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td width="80%" class="m5_td_fieldname"><strong>Area Name</strong></td>
          <td width="20%" class="m5_td_fieldname" align="center"><strong>Number 
            of contacts </strong></td>
          <td width="40" colspan="3" class="m5_td_fieldname"><strong>Actions</strong></td>
        </tr>
        <?

	$checksql = "SELECT * FROM ".TABLE_EXPERTISE." ORDER BY area_id ASC";
	$r_checklist = mysql_query($checksql, $db_link);
	$numrow = mysql_num_rows($r_checklist);
	//*******************************************************************
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 10);

	$areasql = "SELECT * FROM ". TABLE_EXPERTISE ." ORDER BY area_name ASC LIMIT $st, $nh";
	$r_arealist = mysql_query($areasql, $db_link);
	$numArea = mysql_num_rows($r_arealist);
	
	while ($tbl_arealist = mysql_fetch_array($r_arealist)) 
	{
		$area_id = $tbl_arealist['area_id'];
		$area_name = $tbl_arealist['area_name'];
		$delete = "delete";
		
		$count = "SELECT count( contact_expertlink.area_id ) AS sum FROM ( contact_expertlink 
					LEFT JOIN contact_contact ON contact_expertlink.id = contact_contact.id)
					WHERE area_id = $area_id AND delflag !=1 ";
					
		$r_count = mysql_query($count, $db_link);
		$tbl_count = mysql_fetch_array($r_count);
		$sum = $tbl_count['sum'];
		
?>
        <tr> 
          <td class="m5_td_content">&nbsp; 
            <? 
		  	echo $area_name; 
		  ?>
          </td>
          <td class="m5_td_content" align="center"> 
            <?
	echo "<font class=\"breadcrumbs\">(". $sum ." contacts)</font>";
?>
          </td>
          <td width="40" class="m5_td_content"> <a href="areaedit.php<? echo "?id=".$area_id; ?>&mode=1"> 
            <img title="Edit the area of expertise" src="<? echo $app_absolute_path; ?>images/icon_edit.gif" border="0" align="middle"></a> 
          </td>
          <td width="40" class="m5_td_content" colspan="2"> <a href="confirm.php<? echo "?id=".$area_id; ?>&mode=delete"> 
            <img title="Delete the area of expertise" src="<? echo $app_absolute_path; ?>images/icon_dustbin.gif" border="0" align="middle"></a> 
          </td>
        </tr>
        <?
		$x++;
	}
	
	$this_page = $_SERVER['PHP_SELF']."?";
	if ($st > 0)
	{
		$prev_st = $st - $nh;
		if ($prev_st < 0) 
		$prev_st = 0;
		$first_link = $this_page.'&st=0&nh=' . $nh;
		$prev_link = $this_page.'&st=' . $prev_st . '&nh=' .$nh;
	}
	else 
	{
		$first_link = '';
		$prev_link = '';
	}
			
	if (($st + $nh) < $numrow)
	{
		$last_st = (ceil($numrow / $nh) - 1) * $nh;
		$next_link = $this_page.'&st=' . ($st + $nh)  . '&nh=' . $nh;
		$last_link = $this_page.'&st=' . $last_st . '&nh=' .$nh;
	}
	else 
	{
		$next_link = '';
		$last_link = '';
	}
	
?>
      </table>
      <br> 
      <?
	if ($numArea != 0)
	{
?>
      <table width="179"  border="0" align="right" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td width="38" class="m5_td_content"><div align="right"><span class="fontcolorblue">&laquo;</span>
		  <?=generateLink('First', $first_link)?></div></td>
          <td width="60" class="m5_td_content"><div align="right"><span class="fontcolorblue">&lsaquo;&nbsp;</span>
              <?=generateLink('Previous', $prev_link)?>
            </div></td>
          <td width="40" class="m5_td_content"><div align="right">
              <?=generateLink('Next', $next_link)?>
              <span class="fontcolorblue">&nbsp;&rsaquo;</span></div></td>
          <td width="42" class="m5_td_content"><div align="right">
              <?=generateLink('Last', $last_link)?>
              <span class="fontcolorblue">&raquo;</span></div></td>
        </tr>
      </table>
      <?
	}
?>
    </td>
  </tr>
</table>		
<!--*************************************************************************-->	
<? include_once($app_absolute_path."includes/template_footer.php"); ?>
