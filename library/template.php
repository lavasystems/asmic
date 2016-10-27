<? 
session_start();

$permissions = $_SESSION['permissions'];
if (is_array($permissions) && !(in_array(501, $permissions) || in_array(502, $permissions))){
  session_destroy();
  header("Location: ../index.php");
  exit();
}
include_once("local_config.php");
include_once($app_absolute_path."includes/template_header.php");
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
      <?
		include("class.php");
		include($app_absolute_path."includes/functions.php");	
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
          <td>
		 <?
		  $permissions = $_SESSION['permissions'];

			if (is_array($permissions) && in_array(501, $permissions)){
				echo admin_index();
			}
			else {
				echo user_index();
			}
		  ?>
		  </td>
        </tr>
        <tr> 
          <td>
		  
		  </td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
include_once($app_absolute_path."includes/template_footer.php");

function admin_index() {
		global $app_absolute_path, $root_images_folder;
		$libdb = new Modules_sql;
		$qry_all = "select count(*) from library_books where (DAYOFYEAR(NOW()) - DAYOFYEAR(date_added)) <6";
		$result = $libdb->query($qry_all);
		$row = $libdb->next_record();
		$total_rows = $libdb->record[0];
		
		$st = requestNumber($_REQUEST['st'], 0);
		$nh = requestNumber($_REQUEST['nh'], 5);
		
		$qry_books = "Select date_added, book_isbn, book_title, book_publisher, book_author";
		$qry_books .=" from library_books where (DAYOFYEAR(NOW()) - DAYOFYEAR(date_added)) <6 group by book_isbn order by date_added desc";
		$qry_books .=" LIMIT ".$st.", ".$nh."";
		$libdb->query($qry_books);
?>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td><strong>Welcome to Book Library</strong></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td><form name="form1" method="post" action="search_result.php">
        <table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr> 
            <td width="43%"><input name="keyword" type="text" id="keyword2" size="50" class="inputbox"></td>
            <td width="57%"><input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_search.gif" width="61" height="18" name="action" border="0"></td>
          </tr>
          <tr> 
            <td class="ar11_content"><a href="search_books.php?page=advance"><strong><font size="1">Advanced 
              Search</font></strong></a></td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
  <tr> 
    <td>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
        <tr> 
          <td colspan="5" class="m2_td_header"><strong>Latest Books Addition</strong></td>
        </tr>
        <tr> 
          <td width="33%" class="m2_td_fieldname"><strong>Book Title <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></td>
          <td width="21%" class="m2_td_fieldname"><strong>Publisher <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
          <td width="18%" class="m2_td_fieldname"><strong>Author <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
          <td width="12%" class="m2_td_fieldname"><strong>Date Added <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
          <td width="16%" class="m2_td_fieldname"><strong>Book ISBN<strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
        </tr>
        <?
		if($libdb->num_rows() == 0) {
			echo "<tr><td class=\"m2_td_content\" colspan=\"5\">No Latest Books</td></tr>";
		}
		else  {
				while($libdb->next_record()) {
			?>
        <tr> 
          <td class="m2_td_content">&nbsp;<a href="bookdetails.php?isbn=<? echo $libdb->record[1]; ?>"><? echo $libdb->record[2]; ?></a></td>
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
      </table>
	 </td>
  </tr>
  <tr> 
    <td><div align="right"><a href="newbooklist.php">More on Latest Books</a>&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
}
function user_index() {
global $app_absolute_path, $root_images_folder;
?>
<br>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td width="74%" valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td width="49%">&nbsp;</td>
          <td width="48%">&nbsp;</td>
          <td width="3%">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="3"><span class="ar11_content"><strong>Welcome to Book Library, 
            Ika Murni!</strong></span></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td valign="top"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td><table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_search">
                    <tr> 
                      <td class="ar11_content"><strong><u>Search Books </u></strong></td>
                    </tr>
                    <tr> 
                      <td><table width="200"  border="0" align="center" cellpadding="0" cellspacing="0">
                          <tr> 
                            <td><input name="textfield" type="text" class="inputbox"></td>
                            <td><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_search.gif" width="61" height="18"></td>
                          </tr>
                          <tr> 
                            <td colspan="2" class="ar11_content"><a href="search_books.php">Advanced 
                              search</a></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
              </tr>
              <tr> 
                <td>&nbsp;</td>
              </tr>
              <tr> 
                <td><table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
                    <tr> 
                      <td colspan="3" class="m2_td_header"><strong>Books Category 
                        </strong></td>
                    </tr>
                    <tr> 
                      <td width="26%" class="m2_td_fieldname"><strong>Category 
                        ID </strong></td>
                      <td width="74%" class="m2_td_fieldname"><strong>Category 
                        </strong></td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                    <tr> 
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                      <td valign="top" class="m2_td_content">&nbsp;</td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
          <td valign="top"><table width="90%"  border="0" align="right" cellpadding="0" cellspacing="0" class="m2_table3_outline">
              <tr> 
                <td class="ar11_content"><strong><u>Borrow a Book </u></strong><br> 
                  <br>
                  To borrow a book, you must first reserve the book and wait for 
                  a librarian to approve your reservation. <br> <br>
                  To reserve a book, you must first <a href="#" class="hyperlink">search</a> 
                  for your desired book. If the book is available for reservation, 
                  icon <img src="<?=$app_absolute_path?><?=$root_images_folder?>/icon_reservebook.gif" width="18" height="18"> 
                  (Reserve Book) will appear. Click on the icon to reserve.<br> 
                  <br>
                  To search for a book, please <a href="#" class="hyperlink">click 
                  here</a>. </td>
              </tr>
            </table></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2"><hr size="1" color="#999999"></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2"><table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
              <tr> 
                <td colspan="5" class="m2_td_header"><strong>Latest Books Addition</strong></td>
              </tr>
              <tr> 
                <td width="30%" class="m2_td_fieldname"><strong>Book Title <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></td>
                <td width="19%" class="m2_td_fieldname"><strong>Publisher <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
                <td width="18%" class="m2_td_fieldname"><strong>Author <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
                <td width="17%" class="m2_td_fieldname"><strong>Date Added <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
                <td width="16%" class="m2_td_fieldname"><strong>Book ISBN <strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_sortby.gif" width="13" height="11"></strong></strong></td>
              </tr>
              <tr> 
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
              </tr>
              <tr> 
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
              </tr>
              <tr> 
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
                <td valign="top" class="m2_td_content">&nbsp;</td>
              </tr>
            </table></td>
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </table></td>
    <td width="230" valign="top" bgcolor="#F0F0F0"><table width="90%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td> <table width="200"  border="0" align="center" cellpadding="0" cellspacing="0">
              <tr> 
                <td class="ar11_content"><strong>What would you like to do?</strong></td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td><table width="200"  border="0" align="center" cellpadding="0" cellspacing="0" class="m2_table2_outline">
              <tr> 
                <td><table width="200"  border="0" cellpadding="0" cellspacing="0">
                    <tr> 
                      <td><table width="100%"  border="0" cellspacing="0" cellpadding="2">
                          <tr> 
                            <td width="7%"><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                            <td width="93%"><a href="#" class="hyperlink">Search 
                              Books</a></td>
                          </tr>
                          <tr> 
                            <td><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                            <td><a href="#" class="hyperlink">Browse Books</a></td>
                          </tr>
                        </table></td>
                    </tr>
                  </table></td>
              </tr>
            </table></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
<?
}
?>
