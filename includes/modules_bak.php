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

function showMenu()
{
	global $site_name;
	$link = db_mysql_connect();
	if (!$link)	die('connection failed<br>' . $MYSQL_ERROR);
	$member = new User($_SESSION['usr_id']);
?>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<td width="4%" rowspan="11"><img src="images/spacer.gif" width="24" height="8"></td>
		<td width="95%"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td>&nbsp;</td>
            </tr>
          <tr>
            <td class="module_title">Home</td>
		  </tr>
          <tr>
            <td background="images/separator.gif"><img src="images/separator.gif" width="2" height="13"></td>
            </tr>
          <tr>
		  <tr><td>
<?
	if ($member -> isPermitted(101))
	{
?>
		<a href="<?=$_SERVER['PHP_SELF']?>?mod=user" class="ar11_content">User Management</a><br>
<?
	}
	if ($member -> isPermitted(201) || $member -> isPermitted(202))
	{
?>
		<a href="<?=$_SERVER['PHP_SELF']?>?mod=document" class="ar11_content">Document Management</a><br>
<?
	}
	if ($member -> isPermitted(301) || $member -> isPermitted(302)){
?>
		<a href="image/" class="ar11_content">Image Gallery</a><br>
<?
	}
	if ($member -> isPermitted(401) || $member -> isPermitted(402)){
?>
		<a href="contact/" class="ar11_content">Contact Management</a><br>
<?
	}
	if ($member -> isPermitted(501) || $member -> isPermitted(502)){
?>
		<a href="library/" class="ar11_content">Book Library</a><br>
<?
	}

	mysql_close($link);

?>
	<p class="ar11_content"><strong>Welcome to <?=$site_name?></strong></p>
	<p class="ar11_content">user (<?=$_SESSION['usr_username']?>) logged in</p>
	<a href="index.php?do=logout" class="ar11_content">logout</a>
		</td></tr></table></td>
		<td width="1%" rowspan="11"><img src="images/spacer.gif" width="12" height="8"></td>
	  </tr></table>
<?
}

moduleMain();
?>