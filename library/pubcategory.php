<? 
session_start();

include_once("local_config.php");

require_once($app_absolute_path . "includes/functions.php");

if (!isAllowed(array(502, 503), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}

include_once($app_absolute_path."includes/template_header.php");
?><table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
      <?
		include("class.php");
		include_once($app_absolute_path."includes/functions.php");
	  ?>
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="4%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="24" height="8"></td>
          <td colspan="2">&nbsp;</td>
          <td width="1%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="12" height="8"></td>
        </tr>
        <tr> 
          <td colspan="2" class="module_title"><? include("breadcrumb.php"); ?></td>
        </tr>
        <tr> 
          <td colspan="2" background="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif" width="2" height="13"></td>
        </tr>
        <tr> 
          <td colspan="2"> <? include("body_nav.php"); ?> </td>
        </tr>
        <tr> 
          <td width="79%" class="module_sub_title" valign="top"><br>Category<br><br>
            <?
			switch ($_GET['action']) {
				case "catlist" :
					list_category();
				break;
				default :
					cat_list();
				break;
			}
			?>
          </td>
           <? if (isAllowed(array(502), $_SESSION['permissions'])){ ?>
          <td width="200" valign="top" bgcolor="#F0F0F0"><? include("pubrightbar.php"); ?></td>
		  <? } ?>
        </tr>
      </table>
	  </td>
  </tr>
</table>
<?
include_once($app_absolute_path."includes/template_footer.php");

function cat_list() {
global $app_absolute_path, $root_images_folder;

		$libdb = new Modules_sql;
		$qry_all = "select count(*) from library_category where category_id!='100'";
		$result = $libdb->query($qry_all);
		$row = $libdb->next_record();
		$total_rows = $libdb->record[0];
		
		$st = requestNumber($_REQUEST['st'], 0);
		$nh = requestNumber($_REQUEST['nh'], 10);
		$page = ceil($total_rows/$nh);
		$qry_str = "select * from library_category where category_id!='100' order by category_name asc LIMIT ".$st.", ".$nh."";
		$libdb->query($qry_str);
?>
<table width="98%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="74%" valign="top"> <div class="ar11_content">Please click on a 
        Category to view book(s).</div>
      <br> <table width="100%" border="0" class="m2_table_outline" cellspacing="1">
        <tr>
          <td width="30%"class="m2_td_fieldname"><b>Category</b></td>
          <td width="70%" class="m2_td_fieldname"><b>Description</b></td>
        </tr>
        <?
  if ($libdb->num_rows() == 0) {
	  	echo "<tr><td class=\"m2_td_content\" colspan=\"2\">No Data Present</td></tr>";
  }
  else {
 	while($libdb->next_record()) {
		echo "<tr>";
		echo "<td class=\"m2_td_content\">";
				echo"<a href=\"pubcategorydetails.php?kat=".$libdb->record[0]."\">".$libdb->record[2]."</a>";
		echo "</td>";
		echo "<td class=\"m2_td_content\">"; if(empty($libdb->record[3])) { echo "-"; } else { echo $libdb->record[3]; } echo "</td>";
		echo "</tr>";
		}
		
		$this_page = $_SERVER['PHP_SELF']."?";
		if ($st > 0){
				$prev_st = $st - $nh;
				if ($prev_st < 0) $prev_st = 0;
				$first_link = $this_page.'&st=0&nh=' . $nh;
				$prev_link = $this_page.'&st=' . $prev_st . '&nh=' .$nh;
			}
			else {
				$first_link = '';
				$prev_link = '';
			}
		
			if (($st + $nh) < $total_rows){
				$last_st = (ceil($total_rows / $nh) - 1) * $nh;
				$next_link = $this_page.'&st=' . ($st + $nh)  . '&nh=' . $nh;
				$last_link = $this_page.'&st=' . $last_st . '&nh=' .$nh;
			}
			else {
				$next_link = '';
				$last_link = '';
			}
	}
	?>
      </table></td>
  </tr>
  <tr> 
    <td valign="top"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" height="3" width="1"></td>
  </tr>
  <tr> 
    <td valign="top"> 
      <?
	if ($total_rows > $nh) {
	?>
      <table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
		<tr> 
		  <td width="200" class="m2_td_content">
		  <div align="right">
		  <?
		  echo "Total Categories:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
		  ?>
		  </div></td>
		  <td width="38" class="m2_td_content"><div align="right"><span class="fontcolorblue">&laquo;</span> 
			  <?=generateLink('First', $first_link)?>
			</div></td>
		  <td width="60" class="m2_td_content"><div align="right"><span class="fontcolorblue">&lsaquo;</span> 
			  <?=generateLink('Previous', $prev_link)?>
			</div></td>
		  <td width="40" class="m2_td_content"><div align="right"> 
			  <?=generateLink('Next', $next_link)?>
			  <span class="fontcolorblue">&rsaquo;</span></div></td>
		  <td width="42" class="m2_td_content"><div align="right"> 
			  <?=generateLink('End', $last_link)?>
			  <span class="fontcolorblue">&raquo;</span></div></td>
		</tr>
	  </table>
      <?
	}
	?>
    </td>
  </tr>
</table>
<?
}
?>
