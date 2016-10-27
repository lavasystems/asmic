<style>
a.index {
	color:#000;
}
</style>
<?php
function moduleMain(){
    //GET VALUE FROM "global_config.php"
	global $site_name, $app_absolute_path1, $app_absolute_path, $url_prefix;	
    global $root_images_folder, $mod_usr_name, $mod_doc_name, $mod_lib_name, $mod_img_name, $mod_con_name;
	
	if ($_REQUEST['mod'] == 'user'){
		include_once("user/main.php");
	}
	elseif ($_REQUEST['mod'] == 'document'){
		include_once("document/main.php");
	}
	else 
	{
		showMenu();
	}
}

function showMenu(){
	global $site_name, $app_absolute_path;

	$link = db_mysql_connect();
	if (!$link)
		die('connection failed' . $MYSQL_ERROR);

	$member = new User($_SESSION['usr_id']);
?>

<?php
	include_once("classes/contact.php");
	$objContact = new contact();
	$member_name = $objContact->getName($member->usr_contactid);
	if (empty($member_name))
		$member_name = ucfirst($member->usr_username);
?>
	<h4>Welcome, <?=$member_name?></h4>
<?php
	$permissions = $_SESSION['permissions'];
	$perm_count = count($permissions);
?>

<?php if ($member -> isPermitted(101)){ ?>
	<div class="col-lg-4">
		<div class="icon-box ibox-light ibox-effect ibox-rounded">
			<div class="ibox-icon"><i class="fa fa-users"></i></div>
			<h3>Group and User Management</h3>
			<ul>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=group">Manage Group</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=user">Manage User</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=search">Search</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=audittrail">Audit Trail</a>
	            </li>
	        </ul>
		</div>
	</div>
<?php } ?>

<?php if ($member -> isPermitted(501)) { ?>
	<div class="col-lg-4">
		<div class="icon-box ibox-light ibox-effect ibox-rounded">
			<div class="ibox-icon"><i class="fa fa-book"></i></div>
			<h3>Book Library</h3>
			<ul>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/category.php">Category</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/bookissue.php">Issue Book</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/bookreturn.php">Return Book</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/search_book.php">Search</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/report.php">Report</a>

	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/settings.php">Loan Duration</a>
	        </ul>
		</div>
	</div>
<?php }
	elseif ($member -> isPermitted(502) || $member -> isPermitted(503))
	{
?>
	<div class="col-lg-4">
		<div class="icon-box ibox-light ibox-effect ibox-rounded">
			<div class="ibox-icon"><i class="fa fa-book"></i></div>
			<h3>Book Library</h3>
			<ul>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/category.php">Category</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/bookissue.php">Issue Book</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/bookreturn.php">Return Book</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/search_book.php">Search</a>
	            </li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/report.php">Report</a>

	            <li><a class="index" href="<?php echo $app_absolute_path ?>library/settings.php">Loan Duration</a>
	        </ul>
		</div>
	</div>
<?php
	}
	if ($member -> isPermitted(501) || $member -> isPermitted(502)){

	}

	if ($member -> isPermitted(201)) {
?>
	<div class="col-lg-4">
		<div class="icon-box ibox-light ibox-effect ibox-rounded">
			<div class="ibox-icon"><i class="fa fa-file"></i></div>
			<h3>Document Management</h3>
			<ul>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=browse">Browse</a></li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=search">Search</a></li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=xbrowse">Pending Document</a></li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=index">Index</a>
	        </ul>
		</div>
	</div>
<?php
	}

	if ($member -> isPermitted(201) || $member -> isPermitted(202)){
	}

	if ($member -> isPermitted(301))
	{
?>
	<div class="col-lg-4">
		<div class="icon-box ibox-light ibox-effect ibox-rounded">
			<div class="ibox-icon"><i class="fa fa-image"></i></div>
			<h3>Image Gallery</h3>
			<ul>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=browse">Browse</a></li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=search">Add Category</a></li>
	            <li><a class="index" href="<?php echo $app_absolute_path ?>index.php?mod=document&amp;obj=xbrowse">Edit/Delete Category</a></li>
	        </ul>
		</div>
	</div>
<?php
	}
	elseif ($member -> isPermitted(302))
	{
?>
		Main Menu
<?php
	}

	if ($member -> isPermitted(301) || $member -> isPermitted(302)){

	}

	if ($member -> isPermitted(401))
	{
?>
		<div class="col-lg-4">
		<div class="icon-box ibox-light ibox-effect ibox-rounded">
			<div class="ibox-icon"><i class="fa fa-user"></i></div>
			<h3>ASM Contact</h3>
			<ul>
                <li><a class="index" href="javascript:void(0)">Add Contact</a>                                                 
                </li>
                <li><a class="index" href="javascript:void(0)">Manage Contact</a></li>
                <li><a class="index" href="javascript:void(0)">Add Category</a>                                                               
                </li>
                <li><a class="index" href="javascript:void(0)">Manage Category</a></li>
                <li><a class="index" href="javascript:void(0)">Add Area of Expertise</a>
                </li>
                <li><a class="index" href="contactmanageexpertise.php">Manage Area of Expertise</a></li>
                <li><a class="index" href="javascript:void(0)">Search</a></li>
                <li><a class="index" href="javascript:void(0)">Export</a></li>
            </ul>
		</div>
	</div>
<?php
	}
	elseif ($member -> isPermitted(402))
	{
?>
		Contact Menu
<?php
	}

	if ($member -> isPermitted(401) || $member -> isPermitted(402)){

	}
}
moduleMain();
?>
