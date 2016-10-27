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
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
      <?
		include("class.php");
		include_once($app_absolute_path."includes/functions.php");
		$kat = $_REQUEST['kat'];
	  ?>
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="4%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="24" height="8"></td>
          <td>&nbsp;</td>
          <td width="1%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="12" height="8"></td>
        </tr>
        <tr> 
          <td class="module_title"><? include("breadcrumb.php"); ?></td>
        </tr>
        <tr> 
          <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif" width="2" height="13"></td>
        </tr>
        <tr> 
          <td> <? include("body_nav.php"); ?> </td>
        </tr>
        <tr> 
          <td class="ar11_content">
            <table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td width="74%" valign="top" class="module_sub_title">Category Details<br>
				<?
				$libdb2 = new Modules_sql;
				$qry_str2 = "select * from library_category where id='".$kat."'";
				$libdb2->query($qry_str2);
				$libdb2->next_record();
					
					echo "<br><span class=\"ar11_content\">Category : ".$libdb2->record[2];
					echo "<br>Description : "; if(empty($libdb2->record[3])) { echo "-"; } else { echo $libdb2->record[3]; }
			
				$libdb = new Modules_sql;
				$kat2 = $libdb2->record[1]; //change to $kat2 variable due to conflict in paging - Ganee
								
				$qry_all = "select count(*) from library_books where category_id ='".$kat2."'"; //change to $kat2 variable due to conflict in paging - Ganee
				$libdb->query($qry_all);
				$libdb->next_record();
				$total_rows = $libdb->record[0];
				
				$st = requestNumber($_REQUEST['st'], 0);
				$nh = requestNumber($_REQUEST['nh'], 10);
				$page = ceil($total_rows/$nh);
				$qry_str = "select * from library_books inner join library_category on library_books.category_id=library_category.category_id";
				$qry_str .=" where library_books.category_id=".$kat2.""; //change to $kat2 variable due to conflict in paging - Ganee
				$qry_str .=" group by library_books.book_isbn ORDER BY library_books.date_added DESC";
				$qry_str .=" LIMIT ".$st.", ".$nh."";
				$libdb->query($qry_str);
				echo "<br>Quantity of book(s) : ".$total_rows."</span>";
				
				?>
				</div>
				<br>
				<br>
				<table width="98%" border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
				  <tr> 
					  <td class="m2_td_fieldname" width="6%"><b>No.</b></td>
					  <td class="m2_td_fieldname" width="25%"><b>BookTitle</b></td>
					  <td class="m2_td_fieldname" width="10%"><b>ISBN No.</b></td>
					  <td class="m2_td_fieldname" width="15%"><b>Publisher</b></td>
					  <td class="m2_td_fieldname" width="18%"><b>Author</b></td>
					  <td class="m2_td_fieldname" width="12%"><b>Date Added</b></td>
				  </tr>
				<?
				  if ($libdb->num_rows() == 0) {
						echo "<tr><td class=\"m2_td_content\" colspan=\"6\">No Data Present</td></tr>";
				  }
				  else {
				  $i= $st + 1;	
					while($libdb->next_record()) {
					
				
							echo "<td valign=\"top\" class=\"m2_td_content\">".$i."</td>";
							echo "<td valign=\"top\" class=\"m2_td_content\"><a href=\"pubbookdetails.php?id=".$libdb->record[0]."\">".$libdb->record[2]."</a></td>";
							echo "<td valign=\"top\" class=\"m2_td_content\">".$libdb->record[15]."</td>";
							echo "<td valign=\"top\" class=\"m2_td_content\">".$libdb->record[5]."</td>";
							echo "<td valign=\"top\" class=\"m2_td_content\">".$libdb->record[3]."</td>";								
							echo "<td valign=\"top\" class=\"m2_td_content\" align=\"center\">".DateConvert($libdb->record[23], "j M Y")."</td>";
							echo "</tr>";
						$i++;	
					}
					
					$this_page = $_SERVER['PHP_SELF']."?kat=".$kat."";
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
				</table>
				<br>
				<?
				  if ($total_rows > $nh) {
				  ?>
				  <table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
					<tr> 
					  <td width="200" class="m2_td_content">
					  <div align="right">
					  <?
					  echo "Total Books:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
					<td width="230" valign="top" bgcolor="#F0F0F0"><? include("pubrightbar.php"); ?></td>
              </tr>
            </table> 
			</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
include_once($app_absolute_path."includes/template_footer.php");
?>