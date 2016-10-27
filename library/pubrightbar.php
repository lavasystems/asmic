<script type="text/javascript">
function FlipStatus(){
    var image_src = new String(document.getElementById("clickhow_switch").src).valueOf();

    var clickhow_switch_url = "../images/m2/m2_btn_clickhow.gif";
    var closehow_switch_url = "../images/m2/m2_btn_closehow.gif";
    //alert(image_src);
    if(image_src.indexOf("clickhow",image_src) != -1){
        //alert("y");
        document.getElementById("HowToBorrowBook").style.visibility = "visible";
        document.getElementById("clickhow_switch").src = closehow_switch_url;
    }
    else{
        //alert("n");
        document.getElementById("HowToBorrowBook").style.visibility = "hidden";
        document.getElementById("clickhow_switch").src = clickhow_switch_url;
    }
}
</script>
<?
$lib_id = new Modules_sql;
$qry_id = "select usr_contactid from user_users where usr_id=".$_SESSION['usr_id']."";
$lib_id->query($qry_id);
$lib_id->next_record();

$id = $lib_id->record[0];
?>
<table width="230"  border="0" cellpadding="0" cellspacing="0">
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
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="2">
              <tr> 
                <td width="7%"><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                <td width="93%"><a href="index.php" class="hyperlink">Main</a></td>
              </tr>
              <tr> 
                <td width="7%"><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                <td width="93%"><a href="search_books.php" class="hyperlink">Search 
                  Books</a></td>
              </tr>
              <tr> 
                <td><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                <td><a href="pubcategory.php" class="hyperlink">Browse Books</a></td>
              </tr>
              <?
			  if (is_array($permissions) && in_array(502, $_SESSION['permissions'])){
			  ?>
              <tr> 
                <td><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                <td><a href="bookreserve.php?action=reserve_confirm&id=<? echo $id; ?>" class="hyperlink">Reservation 
                  List</a></td>
              </tr>
              <tr>
                <td><div align="right"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_arrow.gif" width="7" height="11"></div></td>
                <td><a class="hyperlink" href="pubhistoryborrowed.php?id=<? echo $id; ?>">History of borrowed books</a></td>
              </tr>
              <?
			  }
			  ?>
            </table></td>
        </tr>
      </table></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
      <table width="200"  border="0" align="center" cellpadding="0" cellspacing="0">
        <tr>
          <td class="ar11_content">
            <a href="#"><img src="../images/m2/m2_btn_clickhow.gif" onClick="FlipStatus()" border="0" id="clickhow_switch"></a><br><br>
            <div align="justify" style="width:95%;visibility:hidden" id="HowToBorrowBook">
                <table width="100%"  border="0" align="right" cellpadding="0" cellspacing="0" class="m2_table3_outline" bgcolor="#FFFFFF">
                  <tr>
                    <td class="ar11_content"><strong><u>Borrow a Book </u></strong><br>
                      <br>
                      To borrow a book, you must first reserve the book and wait for
                      a librarian to approve your reservation. <br> <br>
                      To reserve a book, you must first <a href="pubcategory.php" class="hyperlink">search</a>
                      for your desired book. If the book is available for reservation,
                      icon <img src="<?=$app_absolute_path?><?=$root_images_folder?>/icon_reservebook.gif" width="18" height="18">
                      (Reserve Book) will appear. Click on the icon to reserve.<br>
                      <br>
                      To search for a book, please <a href="search_books.php" class="hyperlink">click
                      here</a>. <br>
                      <br>
                      To view your list of books reservation, <a class="hyperlink" href="bookreserve.php?action=reserve_confirm&id=<? echo $id; ?>">click
                      here</a>. You can confirm your reservation or cancel your reservation.</td>
                  </tr>
                </table>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
    <tr>
    <td>&nbsp;</td>
  </tr>
</table>
