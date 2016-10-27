<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.04d
 *  
 *  address.php
 *  Displays address book entries.
 *
 *************************************************************/

// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASSES); // test Contact class in this file.
	session_start();
	require_once('local_config.php');
	
// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();

// ** CHECK FOR ID **
	$id = check_id();

// ** END INITIALIZATION *******************************************************

	$error = $_GET['error'];
	
// ** RETRIEVE CONTACT INFORMATION **
	$contact = new Contact($id);
		$r_additionalData = mysql_query("SELECT * FROM " . TABLE_ADDITIONALDATA . " AS additionaldata WHERE additionaldata.id=$id", $db_link);
		$r_address = mysql_query("SELECT * FROM " . TABLE_ADDRESS . " AS address WHERE address.id=$id LIMIT 2", $db_link);
		$r_email = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
		$r_groups = mysql_query("SELECT grouplist.groupid, groupname FROM " . TABLE_GROUPS . " AS groups LEFT JOIN " . TABLE_GROUPLIST . " AS grouplist ON groups.groupid=grouplist.groupid WHERE id=$id", $db_link);
		$r_messaging = mysql_query("SELECT * FROM " . TABLE_MESSAGING . " AS messaging WHERE messaging.id=$id", $db_link);
		$r_otherPhone = mysql_query("SELECT * FROM " . TABLE_OTHERPHONE . " AS otherphone WHERE otherphone.id=$id", $db_link);
		$r_websites = mysql_query("SELECT * FROM " . TABLE_WEBSITES . " AS websites WHERE websites.id=$id", $db_link);
		
// CALCULATE 'NEXT' AND 'PREVIOUS' ADDRESS ENTRIES
	$r_prev = mysql_query("SELECT id, fullname FROM " . TABLE_CONTACT . " AS contact WHERE fullname < \"" . $contact->fullname . "\" AND contact.hidden != 1 AND delflag != 1 ORDER BY fullname DESC LIMIT 1", $db_link)
		or die(reportSQLError());
	$t_prev = mysql_fetch_array($r_prev); 
	$prev = $t_prev['id']; 
	if ($prev<1) $prev = $id; 
	$r_next = mysql_query("SELECT id, fullname FROM " . TABLE_CONTACT . " AS contact WHERE fullname > \"" . $contact->fullname . "\" AND contact.hidden != 1 AND delflag != 1 ORDER BY fullname ASC LIMIT 1", $db_link)
		or die(reportSQLError());
	$t_next = mysql_fetch_array($r_next); 
	$next = $t_next['id']; 
	if ($next<1) $next=$id;

// PICTURE STUFF.
	// do we have a picture?
	if ($contact->picture_url) 
	{ 
		$tableColumnAmt = 3;
		$tableColumnWidth = (540 - $options->picWidth) / 2;
	} 
	else 
	{
		if ($options->picAlwaysDisplay == 1) 
		{
		$tableColumnAmt = 3;
		$tableColumnWidth = (540 - $options->picWidth) / 2;
		}
		else 
		{
			$tableColumnAmt = 2;
			$tableColumnWidth = (540 / 2);
		}
	}
?>
<?php
//error_reporting(E_ALL); 
//ini_set("display_errors", 1); 
include_once("../global_config.php");
include_once("../includes/functions.php");
include_once("../includes/database.php");
include_once("../classes/user.php");
include '../inc/pagehead.php'; ?>
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
                <?php include '../inc/navigation.php'; ?>
                <?php include '../inc/search.php'; ?>
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
	            	<!--************************************************************************-->
	            	<h4>ASM Contact</h4>
	            	<?php require_once('breadcrumb.php'); ?>
	            	<?php require_once('navigation.php'); ?>
	            	<?php require_once('searchform.php'); ?>
	            	<?php
						if ($_GET['mode'] == "edit")
						{
					?>
					<div class="alert alert-info">Contact details has been successfully updated</div>
					<?php if ($error == "yes")
						echo "<div class=\"alert alert-danger\">Permission denied. Image file size exceeding server upload limit</div>";
					elseif ($error == "filetype")
						echo "<div class=\"alert alert-danger\">Invalid file type</div>";
						}
					?>
					<?php if ($_GET['mode'] == "new") { ?>
						<div class="alert alert-info">A contact has been successfully created</div>
					<?php if ($error == "yes")
						echo "<div class=\"alert alert-danger\">Permission denied. Image file size exceeding server upload limit</div>";
						}
					?>
					<?php
						require_once("../classes/user.php");
						$objUser = new User();
						$isUser = $objUser->isUser($id);
						if ($isUser > 0)
						{
							$status = "Member";
						}
						else 
						{
							$status = "Non-member";
						}
					?>
					Status: <? echo $status; ?>
					<?php echo("$contact->title $contact->fullname"); ?>
					Category:
						<?php
							// LIST GROUPS
							$tbl_groups = mysql_fetch_array($r_groups);
							$groupname = stripslashes( $tbl_groups['groupname'] );
							$group_id = $tbl_groups['groupid'];
						
							 // format for group links
							$Groups = "<strong><a href=\"" . FILE_LIST . "?groupid=" . $group_id . "\">" . $groupname . "</a></strong>";
							while ( $tbl_groups = mysql_fetch_array($r_groups) ) 
							{
								$groupname = stripslashes( $tbl_groups['groupname'] );
								$group_id = $tbl_groups['groupid']; 
								$Groups = $Groups . ", <strong><a href=\"" . FILE_LIST . "?groupid=" . $group_id . "\">" . $groupname . "</a></strong>";
							}
							echo($Groups);
						?>
						Image:<img src="<?php if ($contact->picture_url) { echo(PATH_MUGSHOTS . $contact->picture_url); } else { echo("images/nopicture.gif"); } ?>" class="img-thumbnail" title="Contacts">
						<?php		
							if (($_SESSION['usertype'] == "admin")) {
								if ($isUser > 0) {
						?>
						<p><a href="checkuser.php?id=<? echo $id; ?>"><B>Edit user password and groups</B></a>
						<BR><a href="../library/lib_members.php?action=detail&amp;id=<? echo $id; ?>&start=contact"><B>View all books borrowed by this contact</B></a>
						<?php } else { ?>
							<p><a href="checkuser.php?id=<? echo $id; ?>"><B>Convert this contact to user</B></a>
						<?php }
							}
							echo "<dl class=\"dl-horizontal\"><dt>IC Number</dt><dd>".$contact->icnum."</dd>";
							echo "<dt>Title</dt><dd>".$contact->title."</dd>";
							while ($tbl_address = mysql_fetch_array($r_address)) 
							{
								$address_refid = $tbl_address['refid'];
								$address_line1 = stripslashes( $tbl_address['line1'] );
								$address_city = stripslashes( $tbl_address['city'] );
								$address_state = stripslashes( $tbl_address['state'] );
								$address_zip = stripslashes( $tbl_address['zip'] );
								$address_phone1 = stripslashes( $tbl_address['phone1'] );
								$address_phone2 = stripslashes( $tbl_address['phone2'] );
								$address_phone3 = stripslashes( $tbl_address['phone3'] );
								$address_fax1 = stripslashes( $tbl_address['fax1'] );
								$address_fax2 = stripslashes( $tbl_address['fax2'] );
								$address_country = $tbl_address['country'];

								echo "<dt>" . (($contact->primary_address == $address_refid) ? $lang[LBL_PRIMARY_ADDRESS] : $lang[LBL_ADDRESS]);
								echo "</dt><dd>";
								if ($address_line1) { echo "\n<br>".nl2br($address_line1); }
								if ($address_city OR $address_state OR $address_zip) { echo "\n<br>"; }
								if ($address_city) { echo "$address_city"; }
								if ($address_city AND $address_state) { echo " \n<br>"; }
								if ($address_state) { echo "$address_state"; }
								if ($address_zip) { echo " $address_zip"; }
								if ($address_country) 
								{ 
									echo "\n<br>$country[$address_country]";
								}
								if ($address_phone1) { echo "\n<br>(M): $address_phone1"; }
								if ($address_phone2) { echo "\n<br>(H): $address_phone2"; }
								if ($address_phone3) { echo "\n<br>(O): $address_phone3"; }
								if ($address_fax1) { echo "\n<br>Fax: $address_fax1"; }
								if ($address_fax2) { echo " / $address_fax2"; }
								echo "</dd>";
							}
								// ** E-MAIL **
							$tbl_email = mysql_fetch_array($r_email);
							$email_address = stripslashes( $tbl_email['email'] );
							$email_address2 = stripslashes( $tbl_email['email2'] );
							$email_address3 = stripslashes( $tbl_email['email3'] );
							$email_type = stripslashes( $tbl_email['type'] );
							if ($email_address) 
							{
								echo("<dt>E-mail Address</dt><dd>");
									if ($options->useMailScript == 1) 
									{
										echo("<BR><A HREF=\"mailto:$email_address\">$email_address</A>");
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
								//echo("<P>\n<B>$lang[LBL_EMAIL]</B>\n");
									if ($options->useMailScript == 1) 
									{
										echo("<BR><A HREF=\"mailto:$email_address\">$email_address3</A>");
									}
									echo("\n");
							}
							echo "</dd></dl>";

							if (isAllowed(array(401), $_SESSION['permissions']))
							{
						?>
						<h4>Confidential Information</h4>
						<?php
							$confisql = "SELECT * FROM " . TABLE_CONFIDENTIAL . " WHERE con_id = $id";
							$confi_list = mysql_query($confisql, $db_link);
					
							$tbl_grouplist = mysql_fetch_array($confi_list);
									
							$list_con_line1 = $tbl_grouplist['con_line1'];
							$list_con_city = $tbl_grouplist['con_city'];
							$list_con_state = $tbl_grouplist['con_state'];
							$list_con_zip = $tbl_grouplist['con_zip'];
							$list_con_country = $tbl_grouplist['con_country'];
							$list_con_phone1 = $tbl_grouplist['con_phone1'];
							$list_con_phone2 = $tbl_grouplist['con_phone2'];
							$list_con_phone3 = $tbl_grouplist['con_phone3'];

							if ($list_con_line1)
							echo $list_con_line1."<br>";
							if ($list_con_city)
							echo $list_con_city.",";
							if ($list_con_state)
							echo $list_con_state.",";
							if ($list_con_zip)
							echo $list_con_zip."<br>";
							if ($list_con_country) 
							{ 
								echo "$country[$list_con_country]<br/>";
							}
							if ($list_con_phone1)
							echo "(M): ". $list_con_phone1."<br>";
							if ($list_con_phone2)
							echo "(H): ". $list_con_phone2."<br>";
							if ($list_con_phone3)
							echo "(O): ". $list_con_phone3."<br>";
							}
						?>
						<h4>Area of expertise</h4>
						<?php
							$joinsql = "SELECT " . TABLE_EXPERTISE . ".area_id, " . TABLE_EXPERTISE . ".area_name, 
										" . TABLE_EXPERTISE . ".area_desc, " . TABLE_EXPERTLINK . ".id, 
										" . TABLE_EXPERTLINK . ".area_id FROM (" . TABLE_EXPERTISE . " 
										INNER JOIN " . TABLE_EXPERTLINK . " ON 
										" . TABLE_EXPERTISE . ".area_id = " . TABLE_EXPERTLINK . ".area_id) 
										WHERE id = $id";
							$join_list = mysql_query($joinsql, $db_link);
							while ($tbl_grouplist = mysql_fetch_array($join_list)) 
							{
								$list_areaid = $tbl_grouplist['area_id'];
								$list_areaname = $tbl_grouplist['area_name'];
								$list_id = $tbl_grouplist['id'];
						?>
						<?php echo $list_areaname; ?>
						<?php } ?>

						<h4>ASM Contributions</h4>
						<table>
							<tr>
						   		<td><b>Committee</b></td>
								<td><b>Position</b></td>
								<td><b>Year</b></td>
						 	</tr>
							<?php
								$c_list = mysql_query("SELECT * FROM " . TABLE_CONTRIBUTION . " WHERE id = $id ORDER BY year DESC", $db_link);
								while ($tbl_grouplist = mysql_fetch_array($c_list)) 
								{
									$list_refid	= $tbl_grouplist['refid'];
									$list_committee = $tbl_grouplist['committee'];
									$list_position	= $tbl_grouplist['position'];
									$list_year	= $tbl_grouplist['year'];
									$list_id	= $tbl_grouplist['id'];
									//if ( (!empty($list_committee)) && (!empty($list_position)) ) //Commented by Chia Boon 6th July 2006 04:30PM
									if ( (!empty($list_committee)) || (!empty($list_position)) ) //Added by Chia Boon 6th July 2006 04:30PM
									{ 
							?>
							<tr>
								<td><? echo $list_committee; ?></td>
								<td><? echo $list_position; ?></td>
								<td><? echo $list_year; ?></td>
							</tr>
						<?php
								}
							}
						?>
						</table>
						
						<?php if (isAllowed(array(401), $_SESSION['permissions'])) { ?>
							<a href="print.php?id=<?php echo($id); ?>"><i class="fa fa-print"></i> Print</a>
							<a href="<?php echo(FILE_EDIT); ?>?id=<?php echo($id); ?>"><i class="fa fa-pencil"></i> Edit</a>
							<a href="confirm.php?id=<?php echo($id); ?>&amp;mode=confirm"><i class="fa fa-print"></i> </a>
						<?php } ?>
						<a href="<?php echo(FILE_ADDRESS); ?>?id=<?php echo($prev); ?>" class="hyperlink">Previous</a>
						<a href="<?php echo(FILE_ADDRESS); ?>?id=<?php echo($next); ?>" class="hyperlink">Next</a>

						<?php echo 'Last update: '.($contact->last_update).'.'; ?>
					<!--**************************************************************************-->			
					</div>
                    <?php if(isset($_SESSION)):
                        //var_dump($_SESSION);
                    endif; ?>
	            </div>
   			</div>
    <!-- End Body Content -->

	<?php include '../inc/footer.php'; ?>
	
    <!-- End site footer -->
  	<a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include '../inc/js.php'; ?>
</body>
</html>