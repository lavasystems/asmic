<?php
	global $member;
	global $root_images_folder, $mod_usr_name, $mod_doc_name, $mod_lib_name, $mod_img_name, $mod_con_name, $breadcrumbs, $site_name;
	include_once("classes/user.php");
	if (empty($member))
		$member = new user($_SESSION['usr_id']);
?>
	<div class="col-md-12">
		<h3>Group &amp; User Management</h3>
		<ol class="breadcrumb">
		<?php if ($member -> isPermitted(101)){ ?>
			<li><a href="<?=$_SERVER['PHP_SELF']?>?mod=user">Main</a></li>
			<?php
				if (count($breadcrumbs) > 0){
					foreach ($breadcrumbs as $breadcrumb){
						if (!empty($breadcrumb[0])){
							$a_tag_open = "<li><a href=\"".$breadcrumb[0]."\">";
							$a_tag_close = "</a></li>";
						}else{
							$a_tag_open = "<li>";
							$a_tag_close = "</li>";
						}
						echo($a_tag_open . $breadcrumb[1] . $a_tag_close);
					}
				}
			} // end if administrator
		?>
		</ol>