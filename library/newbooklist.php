<? 
session_start();

include_once("local_config.php");

require_once($app_absolute_path . "includes/functions.php");

if (!isAllowed(array(501), $_SESSION['permissions'])){
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
	  ?>
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="4%" rowspan="17"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="24" height="8"></td>
          <td>&nbsp;</td>
          <td width="1%" rowspan="17"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="12" height="8"></td>
        </tr>
        <tr> 
          <td class="module_title"><? include("breadcrumb.php"); ?></td>
        </tr>
        <tr> 
          <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif" width="2" height="13"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td> <? include("body_nav.php"); ?> </td>
        </tr>
        <tr> 
          <td class="ar11_content"><div class="module_sub_title"><br>New Book(s) Arrival </div>
            <?
			
			$libdb = new Modules_sql;
			$qry_all = "select count(*) from library_books where (DAYOFYEAR(NOW()) - DAYOFYEAR(date_added)) <7";
			$result = $libdb->query($qry_all);
			//$row = $libdb->fetch_row($result);
			$row = $libdb->next_record();
			$total_rows = $libdb->record[0];
			
			$st = requestNumber($_REQUEST['st'], 0);
			$nh = requestNumber($_REQUEST['nh'], 20);
			
			$qry_books = "Select date_added, book_isbn, book_title, book_publisher, book_author, book_recordid";
			$qry_books .=" from library_books where (DAYOFYEAR(NOW()) - DAYOFYEAR(date_added)) <7 group by book_isbn order by date_added desc";
			$qry_books .=" LIMIT ".$st.", ".$nh."";
			$libdb->query($qry_books);		
		?>
          </td>
        </tr>
		<tr>
			<td class="ar11_content"><br>Below are the list of new book(s) arrival. You can view the details of the book by clicking on the book title.</td>
		</tr>
        <tr> 
          <td><br>
            <table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
              <tr> 
                <td colspan="5" class="m2_td_header">New Book(s) Arrival</td>
              </tr>
              <tr> 
                <td width="33%" class="m2_td_fieldname"><strong>Book Title</strong></td>
                <td width="21%" class="m2_td_fieldname"><strong>Publisher</strong></td>
                <td width="18%" class="m2_td_fieldname"><strong>Author</strong></td>
                <td width="12%" class="m2_td_fieldname"><strong>Date Added</strong></td>
                <td width="16%" class="m2_td_fieldname"><strong>Book ISBN</strong></td>
              </tr>
              <?
		if($libdb->num_rows() == 0) {
			echo "<tr><td class=\"m2_td_content\" colspan=\"4\">No data present</td></tr>";
		}
		else  {
				while($libdb->next_record()) {
			?>
              <tr> 
                <td class="m2_td_content">&nbsp;<a href="bookdetails.php?id=<? echo $libdb->record[5]; ?>"><? echo $libdb->record[2]; ?></a></td>
                <td class="m2_td_content">&nbsp;<? echo $libdb->record[3]; ?></td>
                <td class="m2_td_content">&nbsp;<? echo $libdb->record[4]; ?></td>
                <td class="m2_td_content">&nbsp;<? echo DateConvert($libdb->record[0], "j M Y"); ?></td>
                <td class="m2_td_content">&nbsp;<? echo $libdb->record[1]; ?></td>
              </tr>
              <?
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
			<td><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" height="3" width="1"></td>
		  </tr>
		   <?
			  if ($total_rows > $nh) {
			  ?>
		  <tr> 
			<td><table width="179"  border="0" align="right" cellpadding="0" cellspacing="1">
				<tr> 
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
			  </table></td>
		  </tr>
		  <?
			}
			?>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
include_once($app_absolute_path."includes/template_footer.php");
?>
