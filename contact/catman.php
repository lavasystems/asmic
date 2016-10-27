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

	// ** END INITIALIZATION *******************************************************
	require_once('../includes/functions.php');
	if (!isAllowed(array(401, 501, 101), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}
	

	include_once($app_absolute_path."includes/template_header.php");
?>
<!--*************************************************************************-->
		
<table width="100%"  border="0" cellpadding="0" cellspacing="0">
  <tr> 
    <td width="4%" rowspan="17"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="24" height="8"></td>
    <td>&nbsp;</td>
    <td width="1%" rowspan="17"><img src="<? echo $app_absolute_path; ?>images/spacer.gif" width="12" height="8"></td>
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
<?
	$what = $_GET['what'];	
	$show = "SELECT * FROM contact_grouplist where groupid = '$what'";
	$getshow = mysql_fetch_array(mysql_query($show,  $db_link));
	
	if ($_GET['do'] == "new")
	{
?>
  <tr>
    <td height="20" class="ar11_content">&nbsp;
	<font color="#FF0000">The new category "<? echo $getshow['groupname']; ?>" has been successfully created</font></td>
  </tr>
<?
	}
	
	if ($_GET['do'] == "update")
	{
?>
  <tr>
    <td height="20" class="ar11_content">&nbsp;
	<font color="#FF0000">The category "<? echo $getshow['groupname']; ?>" has been successfully updated</font></td>
  </tr>
<?
	}
	
	if ($_GET['do'] == "delete")
	{
?>
  <tr>
    <td height="20" class="ar11_content">&nbsp;
	<font color="#FF0000">The category has been successfully deleted</font></td>
  </tr>
<?
	}
?>
  <tr> 
    <td height="20">&nbsp; </td>
  </tr>
  <tr> 
    <td> <img title="Add a new category" src="<? echo $app_absolute_path; ?>images/icon_addcategory.gif" align="absmiddle">&nbsp; 
      <a href="addcatnew.php" class="ar11_content"><strong>Add New Category</strong></a> 
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td> <table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td colspan="5" class="m5_td_header"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td  class="m5_td_header"><strong>Category Management</strong> 
                </td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td width="80%" class="m5_td_fieldname"><strong>Category Name </strong></td>
          <td width="20%" class="m5_td_fieldname" align="center"><strong>Number 
            of contacts </strong></td>
          <td colspan="2" class="m5_td_fieldname"><strong>Actions</strong></td>
        </tr>
        <tr> 
          <td class="m5_td_content">&nbsp;&nbsp;<a href="list.php?groupid=1">Unassigned 
            (contacts without a category)</a></td>
          <td class="m5_td_content" align="center"> 
            <?
		$count = "SELECT count(contact_groups.groupid) as sum 
							FROM (contact_groups left join contact_contact on  
							contact_groups.id = contact_contact.id) where groupid = 1 and delflag != 1";
				$r_count = mysql_query($count, $db_link);
				$tbl_count = mysql_fetch_array($r_count);
				$sum = $tbl_count['sum'];
				
				echo "<font class=\"breadcrumbs\">(". $sum ." contacts)</font>";

?>
          </td>
          <td colspan="2" class="m5_td_content">None</td>
        </tr>
        <?
	$checksql = "SELECT * FROM " . TABLE_GROUPLIST . " WHERE groupid >= 3
				ORDER BY groupid DESC";
				 
	$r_checklist = mysql_query($checksql, $db_link);
	$numrow = mysql_num_rows($r_checklist);
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 10);
	//*************************************************

	$groupsql = "SELECT * FROM " . TABLE_GROUPLIST . " WHERE groupid >= 3
				ORDER BY groupname ASC LIMIT $st, $nh";
				 
	$r_grouplist = mysql_query($groupsql, $db_link);
	$numGroups = mysql_num_rows($r_grouplist);
	
	while ($tbl_grouplist = mysql_fetch_array($r_grouplist)) 
	{
		$group_id = $tbl_grouplist['groupid'];
		$group_name = $tbl_grouplist['groupname'];
		
		$count = "SELECT count(contact_groups.groupid) as sum 
					FROM (contact_groups left join contact_contact on  
					contact_groups.id = contact_contact.id) where groupid = $group_id and delflag != 1";
		$r_count = mysql_query($count, $db_link);
		$tbl_count = mysql_fetch_array($r_count);
		$sum = $tbl_count['sum'];
?>
        <tr> 
          <td class="m5_td_content">&nbsp; <a href="list.php?groupid=<? echo $group_id; ?>">
            <?	echo $group_name;	?>
            </a> </td>
          <td class="m5_td_content" align="center"> 
            <?
	echo "<font class=\"breadcrumbs\">(". $sum ." contacts)</font>";
?>
          </td>
          <td width="18" class="m5_td_content"><a href="addcat.php<? echo "?id=".$group_id; ?>&mode=1"> 
            <img title="Edit the category" src="<? echo $app_absolute_path; ?>images/icon_edit.gif" border="0" align="middle"></a> 
          </td>
          <td width="18" class="m5_td_content"><a href="confirm.php<? echo "?id=".$group_id; ?>"a> 
            <img title="Delete the category" src="<? echo $app_absolute_path; ?>images/icon_dustbin.gif" border="0" align="middle"></a> 
          </td>
        </tr>
        <?
		$x++;
	}
	
	$this_page = $_SERVER['PHP_SELF']."?";
	
	// ** IF THE ID IS NOT EMPTY, THERE IS VALUE TO BE SHOWN
	
		
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
	if ($numGroups != 0)
	{
?>
      <table width="179"  border="0" align="right" cellpadding="0" cellspacing="1" class="m5_table_outline">
        <tr> 
          <td width="38" class="m5_td_content"><div align="right"><span class="fontcolorblue">&laquo;</span>
              <?=generateLink('First', $first_link)?>
            </div></td>
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
