<?php
error_reporting(E_ALL); 
ini_set("display_errors", 0); 
	// ** GET CONFIGURATION DATA **s
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);
	session_start();

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	// ** CHECK FOR LOGIN **
	//checkForLogin();
	require_once('local_config.php');
	require_once($app_absolute_path.'includes/functions.php');
	if (!isAllowed(array(401, 402), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}
	
	// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();
	
	// ** END INITIALIZATION *******************************************************

	// CREATE THE LIST.	
	$list = &new ContactList();
	
	// THIS PAGE TAKES SEVERAL GET VARIABLES
	if ($_GET['groupid'])$list->group_id = $_GET['groupid'];
	if ($_GET['page'])$list->current_page = $_GET['page'];
	if (isset($_GET['letter']))$list->current_letter = $_GET['letter'];	
	if (isset($_GET['limit']))$list->max_entries = $_GET['limit'];	

	// Set group name (group_id defaults to 0 if not provided)
	$list->group_name();

	// ** RETRIEVE CONTACT LIST BY GROUP **
	$sort = $_POST['sort'];
	$r_contact = $list->retrieve($sort);
?>
<?php include_once($app_absolute_path."inc/pagehead.php"); ?>
<body class="home">
<!--[if lt IE 7]>
	<p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
<![endif]-->
<div class="body">
	<!-- Start Site Header -->
	<div class="site-header-wrapper">
        <header class="site-header">
            <div class="container sp-cont">
                <div class="site-logo">
                    <h1><a href="<?php echo $app_absolute_path ?>index.php"><img src="<?php echo $app_absolute_path ?>images/company_logo.png" alt="Logo"></a></h1>
                </div>
                <div class="header-right">
                    <div class="topnav dd-menu">
                        <ul id="menu-top-menu" class="top-navigation sf-menu sf-js-enabled">
                            <li><a href="<?php echo $app_absolute_path ?>index.php"><i class="fa fa-home"></i> Home</a></li>
                            <?php if(isset($_SESSION['usr_id'])): ?>
                            <li><a href="<?php echo $app_absolute_path ?>index.php?mod=user&amp;obj=user&amp;do=password"><i class="fa fa-key"></i> Change Password</a></li>
                            <li><a href="<?php echo $app_absolute_path ?>index.php?do=logout"><i class="fa fa-lock"></i> Logout</a></li>
                            <?php endif; ?>
                        </ul>                    
                    </div>                
                </div>
            </div>
        </header>
        <!-- End Site Header -->
        <div class="navbar">
            <div class="container sp-cont">
                <div class="search-function">
                    <a href="#" class="search-trigger"><i class="fa fa-search"></i></a>
                </div>
                <a href="#" class="visible-sm visible-xs" id="menu-toggle"><i class="fa fa-bars"></i></a>
                <?php include $app_absolute_path.'inc/navigation.php'; ?>
                <?php include $app_absolute_path.'inc/search.php'; ?>
            </div>
        </div>
   	</div>
    <!-- Start Body Content -->
  	<div class="main" role="main">
    	<div id="content" class="content full">
            <div class="container">
            	<div class="dashboard-wrapper">
	            	<!-- Visitor's View -->
	            	<div class="row">

<script language="JavaScript" type="text/javascript">
function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
	if(!document.forms[FormName])
		return;
		
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}
</script>
<h4>ASM Contact</h4>
<?php require_once('breadcrumb.php'); ?> 
<?php require_once('navigation.php'); ?>
<?php require_once('searchform.php'); ?>
<h4><?php echo $list->title(); ?></h4>
	
	<form name="selectGroup" method="get" action="<?php echo(FILE_LIST); ?>">
		<div class="form-group">
		<label for="groupid">Select category</label>
		<select name="groupid" class="form-control" onChange="document.selectGroup.submit();">
		<?php
		// -- GENERATE GROUP SELECTION LIST -- 
		if (isAllowed(array(401), $_SESSION['permissions']))
		{
			$groupsql = "SELECT groupid, groupname, description FROM " . TABLE_GROUPLIST . " AS grouplist WHERE groupid >= 0 ORDER BY groupname";
		}
		else 
		{
			$groupsql = "SELECT groupid, groupname, description FROM " . TABLE_GROUPLIST . " AS grouplist WHERE groupid >= 0 AND groupid != 2 ORDER BY groupname";
		}
		
		$r_grouplist = mysql_query($groupsql, $db_link);
		while ($tbl_grouplist = mysql_fetch_array($r_grouplist)) 
		{
			$selectGroupID = $tbl_grouplist['groupid'];
			$selectGroupName = $tbl_grouplist['groupname'];
			echo("<option value=$selectGroupID");
			if ($selectGroupID == $list->group_id) 
			{
				echo(" selected");
			}
			if($selectGroupName== "(all entries)")$selectGroupName = "(all entries)"; 		
			if($selectGroupName== "(unassigned entries)")$selectGroupName = "(unassigned entries)";
			if($selectGroupName== "(temporary entries)")$selectGroupName = "(temporary entries)";
			echo(">$selectGroupName</option>");
		}
		?>
		</select>
		</div>
	</form>
	<table class="table table-striped">
        <tr> 
          	<form name="fieldname" method="POST" action="list.php" enctype="multipart/form-data">
          	<td></td>
        	<td><a href="#" onClick="sortbyname();"> 
          	<?php
				if ($_POST['sort'] == "DESC")
				{
					$tempsort = "ASC";
					$img = "<i class=\"fa fa-arrow-up\"></i>";
				}
				else
				{
					$tempsort = "DESC";
					$img = "<i class=\"fa fa-arrow-down\"></i>";
				}
				echo "<input type=\"hidden\" name=\"sort\" value=\"$tempsort\">";
			?>
          	<div align="left"><?php echo $img; ?> <strong>Name</strong></div></a> 
		  	</td>
      		</form>
      		<td><div align="left"><strong>Phone Number</strong></div></td>
      		<td><div align="left"><strong>Address</strong></div></td>
      		<td><div align="left"><strong>Email</strong></div></td>
      		<td><div align="left"><strong>Status</strong></div></td>
      		<td><div align="left"><strong>Action</strong></div></td>
        </tr>
		<form name="print" method="post" action="printlist.php?choose=1" enctype="multipart/form-data">
		<?php
			// DISPLAY IF NO ENTRIES UNDER GROUP
			if (mysql_num_rows($r_contact)<1) 
			{
				echo "<tr>";
				echo "<td colspan=6>No entries.</td>";
				echo "</tr>";
			}
			// DISPLAY ENTRIES
			while ($tbl_contact = mysql_fetch_array($r_contact)) 
			{
				$contact_fullname = stripslashes( $tbl_contact['fullname'] );
				$contact_id = $tbl_contact['id'];
				$contact_line1 = stripslashes( $tbl_contact['line1'] );
				$contact_city = stripslashes( $tbl_contact['city'] );
				$contact_state = stripslashes( $tbl_contact['state'] );
				$contact_zip = stripslashes( $tbl_contact['zip'] );
				$contact_phone1 = stripslashes( $tbl_contact['phone1'] );
				$contact_phone2 = stripslashes( $tbl_contact['phone2'] );
				$contact_phone3 = stripslashes( $tbl_contact['phone3'] );
				$contact_country = $tbl_contact['country'];
				$contact_status = $tbl_contact['status'];
				$contact_whoAdded = $tbl_contact['whoAdded'];     
			
				$list_NewLetter = strtoupper(substr($contact_fullname, 0, 1));
				if ($list_NewLetter != $list_LastLetter) 
				{
		?>
        <tr>
        	<td></td>
            <td colspan="5"><strong><?php echo $list_NewLetter ?></strong><a name="<?php echo $list_NewLetter; ?>"></a></td>
        </tr>
		<?php } ?>
        <tr>
        	<td><input type="checkbox" name="print[]" value="<?php echo $contact_id; ?>" class="checkbox"></td>
            <td><a href="<? echo FILE_ADDRESS; ?>?id=<? echo $contact_id;?>"><? echo $contact_fullname; ?></a></td>
            <td> 
		<?php
		if ($contact_phone1) 
		{ 
			echo("$contact_phone1<br>"); 
		}
	
		if ($contact_phone2) 
		{ 
			echo("$contact_phone2<br>"); 
		}
		
		if ($contact_phone3) 
		{ 
			echo("$contact_phone3"); 
		}
?>
          	</td>
            <td> 
<?
		if ($contact_line1) 
		{ 
			echo("$contact_line1<br>");
			if ($contact_city) 
			{ 
				echo("$contact_city"); 
			}
			if ($contact_city AND $contact_state) 
			{ 
				echo (", "); 
			}
			if ($contact_state) 
			{ 
				echo("$contact_state"); 
			}
			if ($contact_zip) 
			{ 
				echo(" $contact_zip"); 
			}
			if ($contact_country) 
			{ 
				echo("\n<br>$country[$contact_country]");
			}
		}
?>
           	</td>
            <td> 
<?php
		$r_email = mysql_query("SELECT id, email, email2, email3 FROM " . TABLE_EMAIL . " AS email WHERE id=$contact_id", $db_link);
		$tbl_email = mysql_fetch_array($r_email);
		$email_address = stripslashes( $tbl_email['email'] );
		$email_address2 = stripslashes( $tbl_email['email2'] );
		$email_address3 = stripslashes( $tbl_email['email3'] );
		
		if ($email_address) 
		{
			if ($options->useMailScript == 1) 
			{
				echo("<A HREF=\"mailto:$email_address\">$email_address</A>");
			}
			echo("\n");
		}
		if ($email_address2) 
		{
			if ($options->useMailScript == 1) 
			{
				echo("<BR><A HREF=\"mailto:$email_address\">$email_address2</A>");
			}
			echo("\n");
		}
		if ($email_address3) 
		{
			if ($options->useMailScript == 1) 
			{
				echo("<BR><A HREF=\"mailto:$email_address\">$email_address3</A>");
			}
			echo("\n");
		}
?>
            &nbsp;</td>
<?php
		if (isAllowed(array(401), $_SESSION['permissions']))
		{
?>
			<td><?php echo $contact_status ?></td>
            <td>
				<a href="<? echo FILE_EDIT; ?>?id=<? echo $contact_id; ?>&from=main"><i class="fa fa-pencil"></i></a>
				<a href="vcard.php?id=<? echo $contact_id; ?>&vcard=vcard"><i class="fa fa-file"></i></a>
			</td>
            <?
		}
		elseif (isAllowed(array(402), $_SESSION['permissions']))
		{
?>
            <td> 
				<input type="checkbox" name="print[]" value="<? echo $contact_id; ?>" class="form-control">
			</td>
            <?
		}
		else
		{
?>
<td>None</td>
<?
		}
?>
</tr>
<?
		$list_LastLetter = strtoupper(substr($contact_fullname, 0, 1));
	}
?>
</table>

	<?php if (isAllowed(array(401, 402), $_SESSION['permissions'])){ ?>
	<input type="hidden" name="Submit" value="Submit">
	<p>
		<a class="btn btn-primary" href="#" onClick="SetAllCheckBoxes('print', 'print[]', true);"><i class="fa fa-check-square-o"></i> Select All</a>
		<a class="btn btn-default" href="#" onClick="SetAllCheckBoxes('print', 'print[]', false);"><i class="fa fa-square-o"></i> Unselect All</a>
	</p>
	<p>* Select checkbox located on the left to print or save the contacts</p>
	<?php } ?>
	</form>

	<div class="alert alert-primary">
		<?php echo $list->create_nav(); ?><a href="list.php">[Menu]</a>
	</div>
                    <?php if(isset($_SESSION)):
                        //var_dump($_SESSION);
                    endif; ?>
	            </div>
   			</div>
    <!-- End Body Content -->

	<?php include $app_absolute_path.'inc/footer.php'; ?>
	
    <!-- End site footer -->
  	<a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include $app_absolute_path.'inc/js.php'; ?>
</body>
</html>
