<?
	session_start();
	$permissions = $_SESSION['permissions'];
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td><img src="<?=$app_absolute_path.$root_images_folder?>/logo_asm.gif" width="203" height="102"></td>
      </tr>
      <tr>
        <td><img src="<?=$app_absolute_path.$root_images_folder?>/tab_master.gif" width="203" height="59"></td>
      </tr>
      <tr>
        <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
<?
	if (is_array($permissions) && (in_array(101, $permissions))){
?>
          <tr>
            <td><a href="<?=$url_prefix?>index.php?mod=user" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image4','','<?=$app_absolute_path.$root_images_folder?>/menu_group_over.gif',1)"><img src="<?=$app_absolute_path.$root_images_folder?>/menu_group.gif" name="Image4" width="203" height="36" border="0"></a></td>
          </tr>
<?
	}
	if (is_array($permissions) && (in_array(501, $permissions) || in_array(502, $permissions) || in_array(503, $permissions))){
?>
          <tr>
            <td><a href="<?=$url_prefix?><?=$mod_lib_name?>/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image5','','<?=$app_absolute_path.$root_images_folder?>/menu_book_over.gif',1)"><img src="<?=$app_absolute_path.$root_images_folder?>/menu_book.gif" name="Image5" width="203" height="36" border="0"></a></td>
          </tr>
<?
	}
	if (is_array($permissions) && (in_array(201, $permissions) || in_array(202, $permissions))){
?>
          <tr>
            <td>
			<a href="<?=$url_prefix?>index.php?mod=document" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image6','','<?=$app_absolute_path.$root_images_folder?>/menu_document_over.gif',1)"><img src="<?=$app_absolute_path.$root_images_folder?>/menu_document.gif" name="Image6" width="203" height="36" border="0"></a>
			<!--<a href="#" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image6','','<?=$app_absolute_path.$root_images_folder?>/menu_document_over.gif',1)"><img src="<?=$app_absolute_path.$root_images_folder?>/menu_document.gif" name="Image6" width="203" height="36" border="0"></a>-->
			</td>
          </tr>
<?
	}
	if (is_array($permissions) && (in_array(301, $permissions) || in_array(302, $permissions))){
?>
          <tr>
            <td><a href="<?=$url_prefix?><?=$mod_img_name?>/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image7','','<?=$app_absolute_path.$root_images_folder?>/menu_image_over.gif',1)"><img src="<?=$app_absolute_path.$root_images_folder?>/menu_image.gif" name="Image7" width="203" height="36" border="0"></a></td>
          </tr>
<?
	}
	if (is_array($permissions) && (in_array(401, $permissions) || in_array(402, $permissions))){
?>
          <tr>
            <td><a href="<?=$url_prefix?><?=$mod_con_name?>/" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('Image8','','<?=$app_absolute_path.$root_images_folder?>/menu_contact_over.gif',1)"><img src="<?=$app_absolute_path.$root_images_folder?>/menu_contact.gif" name="Image8" width="203" height="36" border="0"></a></td>
          </tr>
<?
	}
?>
        </table></td>
      </tr>
    </table>