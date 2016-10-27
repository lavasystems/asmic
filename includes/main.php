<?
function moduleMain(){
	global $site_name;
	//GET VALUE FROM "global_config.php"
    global $root_images_folder, $mod_usr_name, $mod_doc_name, $mod_lib_name, $mod_img_name, $mod_con_name;
	
	if ($_REQUEST['mod'] == 'user'){
		include_once("user/main.php");
	}
	elseif ($_REQUEST['mod'] == 'document'){
		include_once("document/main.php");
	}
/*	elseif ($_REQUEST['mod'] == 'contact'){
		echo("Contact Management<br>");
	}
	elseif ($_REQUEST['mod'] == 'library'){		
		//header("location: library/index.php");
		include_once("library/index.php");
	}
	elseif ($_REQUEST['mod'] == 'image'){
		echo("Image<br>");
	}	*/
	else {
//		$body_tag_properties = ' background="images/login_bg.gif"';
		$body_tag_properties = " bgcolor=\"#D2D4D3\" leftmargin=\"0\" topmargin=\"0\" onLoad=\"MM_preloadImages('$root_images_folder/menu_group_over.gif','$root_images_folder/menu_book_over.gif','$root_images_folder/menu_document_over.gif','$root_images_folder/menu_image_over.gif','$root_images_folder/menu_contact_over.gif')\"";
		include_once("includes/header.php");
		include_once("includes/body_header2.php");
		showMenu();
		include_once("includes/body_footer2.php");
		include_once("includes/footer.php");
	}
}

function showMenu(){
	global $site_name;
?>
<script type="text/javascript" src="js/treemenu.js"></script>
<script type="text/javascript" src="js/config.js"></script>
		<table width="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td width="4%" rowspan="8"><img src="../images/spacer.gif" width="24" height="8"></td>
            <td>&nbsp;</td>
            <td width="1%" rowspan="8"><img src="../images/spacer.gif" width="12" height="8"></td>
          </tr>
          <tr>
            <td class="ar12_content">Welcome, Kartika Murni! </td>
            </tr>
          <tr>
            <td>&nbsp;</td>
            </tr>
          <tr>
            <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td width="196" valign="top"><table width="196"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><img src="../images/main_group.gif" width="196" height="33"></td>
                  </tr>
                </table>                                    
				
<table width="196"  border="0" cellpadding="0" cellspacing="0" class="main_tbl_menu">
<tr> 
<td>
<script type="text/javascript">
<!--
   tree = new treemenu('tree', true, true, true, false);

   tree.put(0,'User Group Management','','','', '', '');
   tree.add(0,'Main','','','', '', '');
   tree.add(0,'Manage Groups','','','','','');
   tree.add(0,' Add Group','','','','','');
   tree.add(0,'Manage User','','','','','');
   tree.add(0,' Add User','','',' ','','');
   tree.add(0,'Search','',    '','','','');
   tree.add(0,'Audit Trail','','','','','');
   
   document.write(tree);
//-->
</script>
</td>
</tr>
</table>
				
				
				</td>
                <td width="20"><img src="../images/spacer.gif" width="20" height="8"></td>
                <td width="196" valign="top"><table width="196"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><img src="../images/main_book.gif" width="196" height="33"></td>
                  </tr>
                </table>                  
<table width="196"  border="0" cellpadding="0" cellspacing="0" class="main_tbl_menu">
<tr> 
<td>
<script type="text/javascript">
<!--
   library = new treemenu('library', true, true, true, false);

   library.put(0,'Book Library','','','', '', '');
   library.add(0,'Main','','','', '', '');
   library.add(0,'Category','','','','','');
   library.add(0,' Add New Category','','','','','');
   library.add(0,'Issue Books','','','','','');
   library.add(0,'Return Books','','',' ','','');
   library.add(0,'Search Books','',    '','','','');
   library.add(0,'Reports','','','','','');
   library.add(0,'Loan Duration','','','','','');
   
   document.write(library);
//-->
</script>
</td>
</tr>
</table>
						
						</td>
                <td width="8"><img src="../images/spacer.gif" width="20" height="8"></td>
                <td width="444" valign="top"><table width="196"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><img src="../images/main_document.gif" width="196" height="33"></td>
                  </tr>
                </table>                  
				
<table width="196"  border="0" cellpadding="0" cellspacing="0" class="main_tbl_menu">
<tr>
<td>
<script type="text/javascript">
<!--
   docMan = new treemenu('docMan', true, true, true, false);

   docMan.put(0,'Document Management','','','', '', '');
   docMan.add(0,'Main','','','', '', '');
   docMan.add(0,'Browse','','','','','');
   docMan.add(0,'Search','','','','','');
   
   document.write(docMan);
//-->
</script>
</td>
</tr>
</table>
				
				</td>
              </tr>
              <tr>
                <td colspan="5">&nbsp;</td>
                </tr>
              <tr>
                <td valign="top"><table width="196"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><img src="../images/main_image.gif" width="196" height="33"></td>
                  </tr>
                </table>                  
<table width="196"  border="0" cellpadding="0" cellspacing="0" class="main_tbl_menu">
<tr>
<td>
<script type="text/javascript">
<!--
   image = new treemenu('image', true, true, true, false);

   image.put(0,'Image Management','','','', '', '');
   image.add(0,'Main','','','', '', '');
   image.add(0,'Add New Category','','','','','');
   image.add(0,'Edit Category','','','','','');
   
   document.write(image);
//-->
</script>
</td>
</tr>
</table>
				</td>
                <td>&nbsp;</td>
                <td valign="top"><table width="196"  border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td><img src="../images/main_contact.gif" width="196" height="33"></td>
                  </tr>
                </table>                  
<table width="196"  border="0" cellpadding="0" cellspacing="0" class="main_tbl_menu">
<tr>
<td>
<script type="text/javascript">
<!--
   contact = new treemenu('contact', true, true, true, false);

   contact.put(0,'Contact Management','','','', '', '');
   contact.add(0,'Main','','','', '', '');
   contact.add(0,'Add New Entry','','','','','');
   contact.add(0,'Export','','','','','');
   contact.add(0,'Category Management','','','','','');
   contact.add(0,' Add New Category','','','','','');
   contact.add(0,'Area of Expertise','','','','','');
   contact.add(0,' Add New Area of Expertise','','','','','');
   contact.add(0,'Advanced Search','','','','','');
   
   document.write(contact);
//-->
</script>
</td>
</tr>
</table>
				</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
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
            <td width="95%">&nbsp;</td>
            </tr>
        </table>
<?
}

moduleMain();
?>