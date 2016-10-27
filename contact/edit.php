<?php
	ini_set('display_errors', 0);
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	include_once("local_config.php");

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	require_once($app_absolute_path.'includes/functions.php');
	if (!isAllowed(array(401, 501, 101), $_SESSION['permissions']))
	{
	  session_destroy();
	  header("Location: ".$app_absolute_path."index.php");
	  exit();
	}
	// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();

	// ** CHECK FOR ID **
	$start = $_GET['start'];
	if (empty($start))
	{
		$start = "contact";
	}
		
	$mode = $_GET['mode'];
	$pass = $_GET['pass'];
	$libic = $_GET['ic'];
	$book_code = $_GET['bookcode'];
	$to = $_GET['to'];
	$from = $_GET['from'];
	
	if ($pass == 'confirm')
	{
		$string = "<div class=\"danger\">Please enter your IC number in order to create a user</div>";
	}	

	if ($mode == 'new') 
	{
		$id = '0'; // this is to create empty variables from the database
	}
	else 
	{
		$mode = 'edit';
		$id = check_id();
	}
 
 	// ** AUDIT TRAIL **
	include_once("../classes/audit_trail.php");
	$audit_trail = new audit_trail();
	
	// ** END INITIALIZATION *******************************************************

	if (isset($id)) 
	{
		$r_contact = mysql_query("SELECT * FROM " . TABLE_CONTACT . " AS contact WHERE contact.id=$id", $db_link) or die(reportSQLError());
		$r_additionalData = mysql_query("SELECT * FROM " . TABLE_ADDITIONALDATA . " AS additionaldata WHERE additionaldata.id=$id", $db_link);
		$r_address = mysql_query("SELECT * FROM " . TABLE_ADDRESS . " AS address WHERE address.id=$id LIMIT 2", $db_link);
		$r_email = mysql_query("SELECT * FROM " . TABLE_EMAIL . " AS email WHERE email.id=$id", $db_link);
		$r_messaging = mysql_query("SELECT * FROM " . TABLE_MESSAGING . " AS messaging WHERE messaging.id=$id", $db_link);
		$r_otherPhone = mysql_query("SELECT * FROM " . TABLE_OTHERPHONE . " AS otherphone WHERE otherphone.id=$id", $db_link);
		$r_websites = mysql_query("SELECT * FROM " . TABLE_WEBSITES . " AS websites WHERE websites.id=$id", $db_link);
		$r_confidential = mysql_query("SELECT * FROM " . TABLE_CONFIDENTIAL . " AS confidential WHERE confidential.con_id=$id", $db_link);
		$r_medium = mysql_query("SELECT * FROM " . TABLE_MEDIUM . " AS medium WHERE contact_medium.medium_contact_id=$id", $db_link);
		$r_nextofkin = mysql_query("SELECT * FROM " . TABLE_NEXT_OF_KIN . " WHERE contact_nextofkin.contact_id=$id", $db_link);

		$r_lastUpdate = mysql_query("SELECT DATE_FORMAT(lastUpdate, \"%W, %M %e %Y (%h:%i %p)\") AS lastUpdate FROM " . TABLE_CONTACT . " AS contact WHERE contact.id=$id", $db_link);
				
		$tbl_contact = mysql_fetch_array($r_contact); 
		$tbl_lastUpdate = mysql_fetch_array($r_lastUpdate); 
		$tbl_confidential = mysql_fetch_array($r_confidential); 
		$tbl_medium= mysql_fetch_array($r_medium);
		$tbl_kin= mysql_fetch_array($r_nextofkin); 
	
		$contact_fullname = stripslashes( $tbl_contact['fullname'] );
		$contact_icnum = stripslashes( $tbl_contact['icnum'] ); 
		$contact_title = stripslashes( $tbl_contact['title'] ); 
		$contact_primaryAddress = stripslashes( $tbl_contact['primaryAddress'] );
		$contact_pictureURL = stripslashes( $tbl_contact['pictureURL'] );
		$contact_lastUpdate = stripslashes( $tbl_lastUpdate['lastUpdate'] );
		$contact_hidden = $tbl_contact['hidden'];
		$contact_whoAdded = stripslashes( $tbl_contact['whoAdded'] );
		$contact_status = stripslashes( $tbl_contact['status'] );
		
		$conf_refid = stripslashes( $tbl_confidential['con_refid'] );
		$conf_line1 = stripslashes( $tbl_confidential['con_line1'] );
		$conf_city = stripslashes( $tbl_confidential['con_city'] );
		$conf_state = stripslashes( $tbl_confidential['con_state'] );
		$conf_zip = stripslashes( $tbl_confidential['con_zip'] );
		$conf_country = stripslashes( $tbl_confidential['con_country'] );
		$conf_phone1 = stripslashes( $tbl_confidential['con_phone1'] );
		$conf_phone2 = stripslashes( $tbl_confidential['con_phone2'] );
		$conf_phone3 = stripslashes( $tbl_confidential['con_phone3'] );
		$conf_resume1 = stripslashes( $tbl_confidential['con_resume1'] );
		$conf_resume2 = stripslashes( $tbl_confidential['con_resume2'] );

		$kin_date_of_deceased = $tbl_kin['date_of_deceased']; 
		$kin_full_name = $tbl_kin['full_name'];
		$kin_ic_passport = $tbl_kin['ic_passport'];
		$kin_address = $tbl_kin['address'];
		$kin_city = $tbl_kin['city'];
		$kin_postcode = $tbl_kin['postcode'];
		$kin_country = $tbl_kin['country'];
		$kin_email = $tbl_kin['email'];
		$kin_relationship = $tbl_kin['relationship'];
		$kin_contact_no = $tbl_kin['contact_no'];

		if ((($contact_whoAdded != $_SESSION['username']) AND ($_SESSION['usertype'] != 'admin') AND ($mode != 'new')) OR ($_SESSION['usertype'] == 'guest'))
		{
			$_SESSION = array();
		 	session_destroy();
			reportScriptError("URL tampering detected. You have been logged out.");
		}
	}
		
	// BEGIN OUTPUT BUFFER
	ob_start("callback");
	 
	include_once($app_absolute_path."inc/pagehead.php");
?>
<!--*************************************************************************-->
<SCRIPT LANGUAGE="JavaScript">
<!--
function deleteAddress(x) 
{
	document.getElementsByname('address_line1_'+x).item(0).value = '';
	document.getElementsByname('address_city_'+x).item(0).value = '';
	document.getElementsByname('address_state_'+x).item(0).value = '';
	document.getElementsByname('address_zip_'+x).item(0).value = '';
	document.getElementsByname('address_phone1_'+x).item(0).value = '';
	document.getElementsByname('address_phone2_'+x).item(0).value = '';
	document.getElementsByname('address_phone3_'+x).item(0).value = '';
	document.getElementsByname('address_fax1_'+x).item(0).value = '';
	document.getElementsByname('address_fax2_'+x).item(0).value = '';
	document.getElementsByname('address_country_'+x).item(0).value = '';
}

function saveEntry() 
{
	document.EditEntry.submit();
}
// -->
</SCRIPT>
<style>
.ms-options-wrap,
.ms-options-wrap * {
    box-sizing: border-box;
}

.ms-options-wrap > button:focus,
.ms-options-wrap > button {
    position: relative;
    width: 100%;
    text-align: left;
    border: 1px solid #aaa;
    background-color: #fff;
    padding: 5px 20px 5px 5px;
    margin-top: 1px;
    font-size: 13px;
    color: #aaa;
    outline: none;
    white-space: nowrap;
}

.ms-options-wrap > button:after {
    content: ' ';
    height: 0;
    position: absolute;
    top: 50%;
    right: 5px;
    width: 0;
    border: 6px solid rgba(0, 0, 0, 0);
    border-top-color: #999;
    margin-top: -3px;
}

.ms-options-wrap > .ms-options {
    position: absolute;
    left: 0;
    width: 100%;
    margin-top: 1px;
    margin-bottom: 20px;
    background: white;
    z-index: 2000;
    border: 1px solid #aaa;
}

.ms-options-wrap > .ms-options > .ms-search input {
    width: 100%;
    padding: 4px 5px;
    border: none;
    border-bottom: 1px groove;
    outline: none;
    
}

.ms-options-wrap > .ms-options .ms-selectall {
    display: inline-block;
    font-size: .9em;
    text-transform: lowercase;
    text-decoration: none;
}
.ms-options-wrap > .ms-options .ms-selectall:hover {
    text-decoration: underline;
}

.ms-options-wrap > .ms-options > .ms-selectall.global {
    margin: 4px 5px;
}

.ms-options-wrap > .ms-options > ul > li.optgroup {
    padding: 5px;
}
.ms-options-wrap > .ms-options > ul > li.optgroup + li.optgroup {
    border-top: 1px solid #aaa;
}

.ms-options-wrap > .ms-options > ul > li.optgroup .label {
    display: block;
    padding: 5px 0 0 0;
    font-weight: bold;
}

.ms-options-wrap > .ms-options > ul label {
    position: relative;
    display: inline-block;
    width: 100%;
    padding: 4px;
    margin: 1px 0;
}

.ms-options-wrap > .ms-options > ul li.selected label,
.ms-options-wrap > .ms-options > ul label:hover {
    background-color: #efefef;
}

.ms-options-wrap > .ms-options > ul input[type="checkbox"] {
    margin-right: 5px;
    position: absolute;
    left: 4px;
    top: 7px;
}
</style>
<body class="home">
	<div class="body">
		<!-- Start Site Header -->
		<div class="site-header-wrapper">
	        <header class="site-header">
	            <div class="container sp-cont">
	                <div class="site-logo">
	                    <h1><a href="index.php"><img src="<?php echo $app_absolute_path ?>images/company_logo.png" alt="Logo"></a></h1>
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
		            		<h2>ASM Contact</h2>
		            		<?php require_once('navigation.php'); ?>
		            		<?php require_once('searchform.php'); ?>
		            		<h4><?php
								if ($mode == 'new') {
									echo($lang['EDIT_TITLE_ADD']);
								} else { 
									echo($lang['EDIT_TITLE_EDIT']." $contact_fullname \n");
								}
							?></h4>
		            		<form name="EditEntry" action="<?php echo(FILE_SAVE)."?mode=$mode&start=$start&bookcode=$book_code"; ?>" method="post" enctype="multipart/form-data">
		            			<input type="hidden" name="id" value="<?php echo($id); ?>">
								
								<div class="alert alert-info">Full name, IC number and address are needed for contact to exist. All field marked <font color="#FF0000">*</font> are mandatory.</div>
								<?php echo $string; ?>

								<div class="row">
									<div class="col-lg-4">
										<div class="form-group">
											<label>Title</label>
											<input type="text" class="form-control" name="title" value="<?php if (empty($contact_title)){ echo ($_POST['title']);}else{ echo($contact_title); } ?>">
										</div>
										<div class="form-group">
											<label>Full name <font color="#FF0000">*</font></label>
											<input type="text" class="form-control" name="fullname" value="<?php if (empty($contact_fullname)) { echo ($_POST['fullname']);	} else { echo($contact_fullname); } ?>" required>
										</div>
										<div class="form-group">
											<label>IC Number/Passport <font color="#FF0000">*</font></label>
											<input type="text" class="form-control" name="icnum" value="<?php if (!empty($contact_icnum)){ echo $contact_icnum; } if (!empty($libic)){ echo $libic; } if ( (empty($contact_icnum)) && (empty($libic)) ) { echo ($_POST['icnum']); } ?>" required>
										</div>
									</div>
									<?php while ($tbl_email = mysql_fetch_array($r_email)) {
										$email_address = stripslashes( $tbl_email['email']);
										$email_address2 = stripslashes( $tbl_email['email2']);
										$email_address3 = stripslashes( $tbl_email['email3']);
									}?>
									<div class="col-lg-4">
										<div class="form-group">
											<label>E-mail Address</label>
											<input name="email" type="text" class="form-control" value="<?php if (empty($email_address)) { echo $_POST['email']; } else { echo("$email_address"); } ?>">
										</div>

										<div class="form-group">
											<label>E-mail Address 2</label>
											<input name="email2" type="text" class="form-control" value="<?php if (empty($email_address2)) { echo $_POST['email2']; } else { echo("$email_address2"); } ?>">
										</div>

										<div class="form-group">
											<label>E-mail Address 3</label>
											<input name="email3" type="text" class="form-control" value="<?php if (empty($email_address3)) { echo $_POST['email3']; } else { echo("$email_address3"); } ?>">
										</div>
									</div>
									<div class="col-lg-4">
										<div class="form-group">
											<?php if (empty($contact_pictureURL)) { $contact_pictureURL = "nopicture.gif"; } ?>
											<img src="mugshots/<?php echo $contact_pictureURL; ?>" class="img-thumbnail" >
											<input name="userfile2" type="file">
											<input type="hidden" name="pics" value="<?php echo $contact_pictureURL; ?>">
											<div class="alert alert-info">Please upload your photo in JPG, JPEG or GIF format, with the file extension as .jpg, .jpeg or .gif. Maximum file size is 4MB. The ideal dimension to save your image is 126 x 160 pixels.</div>
										</div>
									</div>
								</div>

								<h4>Remarks</h4>
								<div class="row">
									<div class="col-lg-6">
										<div class="checkbox">
											<label>
											<input type="checkbox" name="status" value="deceased" <?php if($contact_status == 'deceased') echo "checked"; ?>> Deceased</label>
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Date of deceased</label>
											<input id="kin_date_of_deceased" name="kin_date_of_deceased" type="text" class="form-control" value="<?php if (empty($kin_date_of_deceased)) { echo $_POST['kin_date_of_deceased']; } else { echo("$kin_date_of_deceased"); } ?>">
										</div>
									</div>
								</div>

								<h4>Next of Kin Details</h4>
								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Full Name</label>
											<input name="kin_full_name" type="text" class="form-control" value="<?php if (empty($kin_full_name)) { echo $_POST['kin_full_name']; } else { echo("$kin_full_name"); } ?>">
										</div>
										<div class="form-group">
											<label>IC/Passport Number</label>
											<input name="kin_ic_passport" type="text" class="form-control" value="<?php if (empty($kin_ic_passport)) { echo $_POST['kin_ic_passport']; } else { echo("$kin_ic_passport"); } ?>">
										</div>
										<div class="form-group">
											<label>E-mail Address</label>
											<input name="kin_email" type="email" class="form-control" value="<?php if (empty($kin_email)) { echo $_POST['kin_email']; } else { echo("$kin_email"); } ?>">
										</div>
										<div class="form-group">
											<label>Relationship</label>
											<input name="kin_relationship" type="text" class="form-control" value="<?php if (empty($kin_relationship)) { echo $_POST['kin_relationship']; } else { echo("$kin_relationship"); } ?>">
										</div>
										<div class="form-group">
											<label>Contact No.</label>
											<input name="kin_contact_no" type="text" class="form-control" value="<?php if (empty($kin_contact_no)) { echo $_POST['kin_contact_no']; } else { echo("$kin_contact_no"); } ?>">
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Address</label>
											<textarea name="kin_address" class="form-control">
											<?php if (empty($kin_address)) { echo $_POST['kin_address']; } else { echo("$kin_address"); } ?>
											</textarea>
										</div>
										<div class="form-group">
											<label>City</label>
											<input name="kin_city" type="text" class="form-control" value="<?php if (empty($kin_city)) { echo $_POST['kin_city']; } else { echo("$kin_city"); } ?>">
										</div>
										<div class="form-group">
											<label>Postcode</label>
											<input name="kin_postcode" type="text" class="form-control" value="<?php if (empty($kin_postcode)) { echo $_POST['kin_postcode']; } else { echo("$kin_postcode"); } ?>">
										</div>
										<div class="form-group">
											<label>Country</label>
											<select name="kin_country" class="form-control">
											<?php
												// -- GENERATE COUNTRY SELECTION LIST --
												// This sort routine can handle country names with special characters
												foreach ($country as $country_id=>$val) 
												{
													$sortarray[$country_id] = strtr($val,"ÀÁÂÃÄÅÈÉÊ€ËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåèéêëìíîïñòóôõöùúûüýÿ", "AAAAAAAEEEEIIIINOOOOOUUUUYaaaaaaeeeeiiiinooooouuuuyy");
												}
												asort($sortarray);
												$addressOK=0;
												foreach(array_keys($sortarray) as $country_id) 
												{
													echo("<option value=$country_id");
													if ($mode == 'new' AND $country_id == $options->countryDefault)
													{
													echo(" selected");
													}
													if ($country_id == $kin_country AND $mode=='edit') 
													{
														echo(" selected");
														$addressOK=1;
													}
													elseif ($country_id == $options->countryDefault AND $addressOK==0) 
													{
														echo(" selected");
													}
													if ($country_id == $_POST['kin_country'])
													{
														echo(" selected");
													}
													echo ">" . $country[$country_id] . "</option>\n";
												}
											?></select>
										</div>
									</div>
								</div>

								<h4>Primary Address</h4>
								<?php
								// ADDRESSES
								$tbl_address = mysql_fetch_array($r_address);
								$addnum = 0;
								do { // start
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
									$address_country = stripslashes( $tbl_address['country'] );
								
								if ($addnum == 0) {
									echo "<div class=\"alert alert-warning\">Primary address is required in order for a contact to exist</div>";
								}else{
									echo "<h4>Secondary Address</h4>";
								} ?>

								<input type="hidden" name="address_refid_<?php echo $addnum ?>" value="<?php echo $address_refid ?>">

								<div class="row">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Full Address <font color="#FF0000">*</font></label>
											<textarea name="address_line1_<?php echo($addnum); ?>" cols="20" rows="3" class="form-control">
											<?php if (empty($address_line1)) { echo ($_POST['address_line1_0']); } else { echo($address_line1); } ?>
											</textarea>
										</div>

										<div class="form-group">
											<label>City</label>
											<input type="text" class="form-control" name="address_city_<?php echo($addnum); ?>" value="<?php if (empty($address_city)) { echo ($_POST['address_city_0']); } else { echo($address_city); } ?>">
										</div>

										<div class="form-group">
											<label>State</label>
											<input type="text" class="form-control" name="address_state_<?php echo($addnum); ?>" value="<?php if (empty($address_state)){echo ($_POST['address_state_0']);}else{echo($address_state);}?>">
										</div>

										<div class="form-group">
											<label>Postcode</label>
											<input type="text" class="form-control" name="address_zip_<?php echo($addnum); ?>" value="<?php if (empty($address_zip)){echo ($_POST['address_zip_0']);}echo($address_zip);?>">
										</div>

										<div class="form-group">
											<label>Country</label>
											<select name="address_country_<?php echo($addnum); ?>" class="form-control">
											<?php
												// -- GENERATE COUNTRY SELECTION LIST --
												// This sort routine can handle country names with special characters
												foreach ($country as $country_id=>$val) 
												{
													$sortarray[$country_id] = strtr($val,"ÀÁÂÃÄÅÈÉÊ€ËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåèéêëìíîïñòóôõöùúûüýÿ", "AAAAAAAEEEEIIIINOOOOOUUUUYaaaaaaeeeeiiiinooooouuuuyy");
												}
												asort($sortarray);
												$addressOK=0;
												foreach(array_keys($sortarray) as $country_id) 
												{
													echo("<option value=$country_id");
													if ($mode == 'new' AND $country_id == $options->countryDefault)
													{
													echo(" selected");
													}
													if ($country_id == $address_country AND $mode=='edit') 
													{
														echo(" selected");
														$addressOK=1;
													}
													elseif ($country_id == $options->countryDefault AND $addressOK==0) 
													{
														echo(" selected");
													}
													if ($country_id == $_POST['address_country_0'])
													{
														echo(" selected");
													}
													echo ">" . $country[$country_id] . "</option>\n";
												}
											?></select>
										</div>
									</div>

									<div class="col-lg-6">
										<div class="form-group">
											<label>Telephone</label>
											<input type="text" class="form-control" name="address_phone1_<?php echo($addnum); ?>" value="<?php if (empty($address_phone1)){echo ($_POST['address_phone1_0']);}else{echo($address_phone1);}?>">
										</div>
										<div class="form-group">
											<label>Phone (Home)</label>
											<input type="text" class="form-control" name="address_phone2_<?php echo($addnum); ?>" value="<?php if (empty($address_phone2)){echo ($_POST['address_phone2_0']); }else{echo($address_phone2);}?>">
										</div>
										<div class="form-group">
											<label>Phone (Office)</label>
											<input type="text" class="form-control" name="address_phone3_<?php echo($addnum); ?>" value="<?php if (empty($address_phone3)){echo ($_POST['address_phone3_0']); }else{echo($address_phone3);}?>">
										</div>
										<?php if ($addnum == 0) { ?>
										<div class="form-group">
											<label>Fax 1</label>
											<input type="text" class="form-control" name="address_fax1_0" value="<?php if (empty($address_fax1)){echo ($_POST['address_fax1_0']); }else{echo($address_fax1);}?>">
										</div>
										<div class="form-group">
											<label>Fax 2</label>
											<input type="text" class="form-control" name="address_fax2_0" value="<?php if (empty($address_fax2)){echo ($_POST['address_fax2_0']); }else{echo($address_fax2);}?>">
										</div>
										<?php } else {
											echo "<input type=\"hidden\"\name=\"address_fax1_1\" value=\"";
											if (empty($address_fax1))
											{
												echo ($_POST['address_fax1_1']);
												echo "\">";
											}
											else
											{
												echo($address_fax1);
											}
											echo "<input type=\"hidden\"\name=\"address_fax2_1\" value=\"";
											if (empty($address_fax2))
											{
												echo ($_POST['address_fax2_1']);
												echo "\">";
											}
											else
											{
												echo($address_fax2);
											}
										} ?>
									</div>
								</div>
								
								<?php // drop back into PHP mode and close off the loop
									$addnum++;
								} while ($tbl_address = mysql_fetch_array($r_address));
								$count = mysql_fetch_array(mysql_query("SELECT COUNT(refid) FROM " . TABLE_ADDRESS . " WHERE id = $id", $db_link));
								$stat = $count[0];
								if ( (empty($id)) || ($stat <= 1) ) {
									echo "<h4>Secondary Address</h4>";
								} ?>
								<div class="row">
									<input type="hidden" name="address_primary_select" value="address_primary_<?php echo($addnum); ?>"> 
									<input type="hidden" name="address_refid_<?php echo $addnum; ?>" value="">
									<div class="col-lg-6">
										<div class="form-group">
											<label>Full Address</label>
											<textarea name="address_line1_<?php echo($addnum); ?>" cols="20" rows="3" class="form-control"><?php echo ($_POST['address_line1_1']); ?></textarea>
										</div>

										<div class="form-group">
											<label>City</label>
											<input type="text" class="form-control" name="address_city_<?php echo($addnum); ?>" value="<?php echo ($_POST['address_city_1']); ?>">
										</div>

										<div class="form-group">
											<label>State</label>
											<input type="text" class="form-control" name="address_state_<?php echo($addnum); ?>" value="<?php echo ($_POST['address_state_1']); ?>">
										</div>

										<div class="form-group">
											<label>Postcode</label>
											<input type="text" class="form-control" name="address_zip_<?php echo($addnum); ?>" value="<?php echo ($_POST['address_zip_1']); ?>">
										</div>

										<div class="form-group">
											<label>Country</label>
											<select name="address_country_<?php echo($addnum); ?>" class="form-control">
				                            <?php
												// -- GENERATE COUNTRY SELECTION LIST --
												foreach ($country as $country_id=>$val) 
												{
													$sortarray[$country_id] = strtr($val,"ÀÁÂÃÄÅÈÉÊ€ËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåèéêëìíîïñòóôõöùúûüýÿ", "AAAAAAAEEEEIIIINOOOOOUUUUYaaaaaaeeeeiiiinooooouuuuyy");
												}
												asort($sortarray);
											
												$addressOK=0;
												foreach(array_keys($sortarray) as $country_id) 
												{
													echo("<option value=$country_id");
													//$conf_country
													//if ($country_id == $options->countryDefault)
													if ($country_id == $options->countryDefault)
													{
														echo(" selected");
													}
													
													if ($country_id == $_POST['address_country_1'])
													{
														echo(" selected");
													}
													echo ">" . $country[$country_id] . "</option>";
												}
											?></select>
											<input type="hidden" name="addnum" value="<?php echo($addnum); ?>">
										</div>
									</div>
									<div class="col-lg-6">
										<div class="form-group">
											<label>Telephone</label>
											<input type="text" class="form-control" name="address_phone1_<?php echo($addnum); ?>" value="<?php echo ($_POST['address_phone1_1']); ?>">
										</div>

										<div class="form-group">
											<label>Phone 2</label>
											<input type="text" class="form-control" name="address_phone2_<?php echo($addnum); ?>" value="<?php echo ($_POST['address_phone2_1']); ?>">
										</div>

										<div class="form-group">
											<label>Phone (Office)</label>
											<input type="text" class="form-control" name="address_phone3_<?php echo($addnum); ?>" value="<?php if (empty($address_phone3)){echo ($_POST['address_phone3_0']); }else{echo($address_phone3);}?>">
										<?php //} ?>
										<!--END OF PRINT BLANK ADDRESS FIELD -->
										</div>
									</div>
								</div>

								
								<h4>Confidential Information</h4>
								<div class="alert alert-info">* Personal information that can only be accessed by Admin</div>
								<div class="row">
								<input type="hidden" name="address_refid" value="<? echo $conf_refid; ?>">
								<div class="col-lg-6">

								<div class="form-group">
									<label>Full Address</label>
									<textarea name="con_line1" cols="20" rows="3" class="form-control"><?php
										if (empty($conf_line1)) 
										{
											echo $_POST['con_line1'];
										}
										else
										{
											echo $conf_line1;
										} 
									?></textarea>
								</div>

								<div class="form-group">
									<label>City</label>
									<input type="text" class="form-control" name="con_city" value="<?php if (empty($conf_city)){echo $_POST['con_city'];}else{echo $conf_city;} ?>">
								</div>

								<div class="form-group">
									<label>State</label>
									<input type="text" class="form-control" name="con_state" value="<?php if (empty($conf_state)){echo $_POST['con_state'];}else{echo $conf_state;} ?>">
								</div>

								<div class="form-group">
									<label>Postcode</label>
									<input type="text" class="form-control" name="con_zip" value="<?php if (empty($conf_zip)){echo $_POST['con_zip'];}else{echo $conf_zip;} ?>">
								</div>

								<div class="form-group">
									<label>Country</label>
									<select name="con_country" class="form-control">
									<?php
										// -- GENERATE COUNTRY SELECTION LIST --
										foreach ($country as $country_id=>$val) 
										{
											$sortarray[$country_id] = strtr($val,"ÀÁÂÃÄÅÈÉÊ€ËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝàáâãäåèéêëìíîïñòóôõöùúûüýÿ", "AAAAAAAEEEEIIIINOOOOOUUUUYaaaaaaeeeeiiiinooooouuuuyy");
										}
										asort($sortarray);

										$addressOK=0;
										foreach(array_keys($sortarray) as $country_id) 
										{
											echo("<option value=$country_id");
											if ($country_id == $conf_country)
											{
												echo(" selected");
											}
											
											if ($country_id == $_POST['address_country'])
											{
												echo(" selected");
											}
											echo ">" . $country[$country_id] . "</option>";
										}
									?></select>
								</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group">
										<label>Telephone</label>
										<input type="text" class="form-control" name="con_phone1" value="<?php if (empty($conf_phone1)){echo $_POST['con_phone1'];}else{echo $conf_phone1; }?>">
									</div>

									<div class="form-group">
										<label>Phone 2</label>
										<input type="text" class="form-control" name="con_phone2" value="<?php if (empty($conf_phone2)){echo $_POST['con_phone2'];}else{ echo $conf_phone2; }?>">
									</div>

									<div class="form-group">
										<label>Phone (Office)</label>
										<input type="text" class="form-control" name="con_phone3" value="<?php if (empty($conf_phone3)){echo $_POST['con_phone3'];}else{ echo $conf_phone3; }?>">
									</div>

									<div class="form-group">
										<label>CV Upload 1</label>
										<input type="file" class="form-control" name="con_resume1">
										<input type="hidden" name="resume1" value="<? echo $conf_resume1; ?>">
										<?php if (!empty($conf_resume1)) { ?>
											<a class="btn btn-info" href="resumes/<? echo $conf_resume1; ?>">Download CV 1</a> 
										<?php } ?>
									</div>

									<div class="form-group">
										<label>CV Upload 2</label>
										<input type="file" class="form-control" name="con_resume2">
										<input type="hidden" name="" value="<? echo $conf_resume2; ?>">
										<?php if (!empty($conf_resume2)) { ?>
											<a class="btn btn-info" href="resumes/<? echo $conf_resume2; ?>">Download CV 2</a>
										<?php } ?>
									</div>
								</div>
							</div>

							
							<h4>Preferable Medium</h4>
							<?php
								$medium_list = mysql_query("SELECT * FROM " . TABLE_MEDIUM . " WHERE medium_contact_id = $id ORDER BY medium_id DESC", $db_link);
								while ($tbl_medium = mysql_fetch_array($medium_list)) 
								{
									$list_mid	= $tbl_medium['medium_id'];
									$list_fax = $tbl_medium['medium_fax'];
									$list_email = $tbl_medium['medium_email'];
									$list_hand = $tbl_medium['medium_hand'];
									$list_post = $tbl_medium['medium_post'];
									$list_minform	= $tbl_medium['medium_inform'];
									$list_minvite	= $tbl_medium['medium_invite'];
								}
							?>
								
							<div class="row">
								<dl class="dl-horizontal">
								
									<dt>Contact By :</dt>
									<dd>
										<label class="checkbox-inline">
											<input type="checkbox" name="m_fax" value="1" <?php if($list_fax==1) echo "checked"; ?>> Fax
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="m_email" value="1" <?php if($list_email==1) echo "checked"; ?>> E-Mail
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="m_hand" value="1" <?php if($list_hand==1) echo "checked"; ?>> By-Hand
										</label>
										<label class="checkbox-inline">
											<input type="checkbox" name="m_post" value="1" <?  if($list_post ==1) echo "checked"; ?>> Post
										</label>
									</dd>

								
									<dt>To Be Informed :</dt>
									<dd>
										<input type="radio" value="1" name="m_inform" <? if($list_minform == 1) echo "checked"; ?>> Yes
										<input type="radio" value="2" name="m_inform" <? if($list_minform == 2) echo "checked"; ?>> No
									</dd>

									<dt>To Be Invited :</dt>
									<dd>
										<input type="radio" value="1" name="m_invite" <? if($list_minvite == 1) echo "checked"; ?>> Yes
										<input type="radio" value="2" name="m_invite" <? if($list_minvite == 2) echo "checked"; ?>> No
									</dd>
								</dl>
								
							</div>

							<h4>Area of Expertise</h4>

							<div class="row">
								<div class="col-lg-12">
									<?php
										// Display Group Checkboxes.
										$areasql = "SELECT expertise.area_id, area_name, id FROM ". TABLE_EXPERTISE ." AS expertise
													LEFT JOIN ". TABLE_EXPERTLINK ." AS expertlink ON expertise.area_id = expertlink.area_id
													AND id =$id WHERE expertise.area_id >=0 ORDER BY area_name";
										
										$r_arealist = mysql_query($areasql, $db_link);
										echo "<select class=\"form-control\" name=\"areas[]\" multiple=\"multiple\">";
										while ($tbl_arealist = mysql_fetch_array($r_arealist))
										{
											$area_id = $tbl_arealist['area_id'];
											$area_name = $tbl_arealist['area_name'];
											echo("<option value=\"$area_id\">$area_name</option>");
										}
										echo "</select>";
									?>
								</div>
							</div>
                        <h4>ASM Contributions (Applicable to fellows only)</h4>
                        <dl class="dl-horizontal">
                      		<dt>Committee/Task force</dt>
                      		<dd><input name="committee" type="text" value="<? 
										if (empty($temp_committee))
										{
											echo $_POST['committee'];
										}
										else
										{
											echo $temp_committee;
										} 
									?>" class="form-control">
							</dd>
							<dt>Position</dt>
							<dd><input name="position" type="text" id="position" value="<? 
									if (empty($temp_position))
									{
										echo $_POST['position'];
									}
									else
									{
										echo $temp_position;
									} 
									?>" class="form-control">
							</dd>
							<dt>Year</dt>
							<dd><select name="year" id="year" class="form-control">
                        <?php
							$yearList = array("1990", "1991", "1992", "1993", "1993", "1994", "1995", "1996", "1997", "1998", 
								"1999", "2000", "2001", "2002", "2003", "2004", "2005", "2006","2007","2008","2009","2010","2011","2012","2013","2014","2015","2016");
								$x = 0;
								echo "<option value=''>(Please select)</option>";
								while ($x < 18)
								{
									echo("<option value=$yearList[$x]");
									if ($yearList[$x] == $_POST['year'])
									{
										echo(" selected");
									}
									echo(">$yearList[$x]</option>");
									$x++;
								}
							?>
                        </select>
                        </dd>
                        </dl>
                        <?php
							$checkic = mysql_query("SELECT id FROM contact_contribution where id = $id LIMIT 1", $db_link);
							$t_id = mysql_fetch_array($checkic);
							$temp_id = $t_id['id'];
							if (!empty($temp_id)){
						?>
                        <table class="table table-striped">
                            <tr> 
                              <td><b>Committee</b></td>
                              <td><b>Position</b></td>
                              <td><b>Year</b></td>
                              <td colspan="2"><b>Action</b></td>
                            </tr>
                        <?php
						// -- GET ALL CONTRIBUTION DATA ON CONTACTS --
						$c_list = mysql_query("SELECT * FROM " . TABLE_CONTRIBUTION . " WHERE id = $id ORDER BY year DESC", $db_link);
					
						while ($tbl_grouplist = mysql_fetch_array($c_list)) 
						{
							$list_refid	= $tbl_grouplist['refid'];
							$list_committee = $tbl_grouplist['committee'];
							$list_position	= $tbl_grouplist['position'];
							$list_year	= $tbl_grouplist['year'];
							$list_id	= $tbl_grouplist['id'];
							//if ( (!empty($list_committee)) && (!empty($list_position)) ) //Commented by Chia Boon 6th July 2006 04:35PM
							if ( (!empty($list_committee)) || (!empty($list_position)) ) //Added by Chia Boon 6th July 2006 04:35PM
							{  
						?>
                            <tr> 
                              <td><?php echo $list_committee; ?></td>
                              <td><?php echo $list_position; ?></td>
                              <td><?php if (!empty($list_year)) { echo $list_year; } else { echo "&nbsp;"; } ?></td>
                              <td><a href="contriedit.php?id=<?php echo $id; ?>&refid=<? echo $list_refid; ?>&mode=1">Edit</a></td>
                              <td><a href="confirmation.php?id=<?php echo $id; ?>&refid=<? echo $list_refid; ?>&mode=2">Delete</a></td>
                            </tr>
                        <?php
							}
						}
					?>
                        </table>
                        <?php } ?>
                        
                        <h4>Category</h4>
                        <div class="alert alert-info">*Only one are category allowed per contact</div>
                        <div class="row">
                        	<div class="col-lg-6">
							<?php
								// Display Group Checkboxes.
								$groupsql = "SELECT grouplist.groupid, groupname, id 
											 FROM " . TABLE_GROUPLIST . " AS grouplist
											 LEFT JOIN " . TABLE_GROUPS . " AS groups
											 ON grouplist.groupid=groups.groupid AND id=$id
											 WHERE grouplist.groupid >= 3
											 ORDER BY groupname";
											 
								$r_grouplist = mysql_query($groupsql, $db_link);
								echo "<select name=\"groups\" class=\"form-control\">";
								while ($tbl_grouplist = mysql_fetch_array($r_grouplist))
								{
									$group_id = $tbl_grouplist['groupid'];
									$group_name = $tbl_grouplist['groupname'];
									echo("<option value=\"$group_id\">$group_name</option>");
								}
								echo "</select>";
							?>
							</div>
						</div>
		    <?php if (empty($contact_icnum)){	
				if ( ($start == "library") || ($start == "user") || ($to == "to") )
				{
			?>
    <h4>Member Option</h4>
    <div class="checkbox">
    <input type="checkbox" name="" value="checking" checked disabled>
    <input type="hidden" name="membercheck" value="checking"> Add as member
    </div>
    <?php
		}
		else
		{
	?>
    
    <h4>Become Member Option</h4>
    <div class="checkbox">
    <label>
    <input type="checkbox" name="membercheck" value="checking" 			
	<?php
		if ($_POST['membercheck'])
		{
			echo "checked";
		}
	?>>Add as member</label></div>
    <?php
		}
	}
?>

<a href="#" onClick="saveEntry();" class="btn btn-primary">Save</a>
		<?php
			if ($mode == 'new') 
			{
		?>
				<a href="<?php echo FILE_LIST; ?>" class="btn btn-default">Cancel</a>
		<?php
			}
			else 
			{ 
		?>
				<A HREF="confirm.php?id=<? echo $id; ?>&mode=confirm&from=<?php echo $from; ?>" class="btn btn-primary">Delete</A>
				<A HREF="<? echo FILE_ADDRESS; ?>?id=<? echo $id; ?>" class="btn btn-default">Cancel</A>
		<?php
			}
		?>
<?php
	if ($mode == 'new') 
	{
		echo("&nbsp;");
	}
	else 
	{ 
		echo "<br>".$lang['LAST_UPDATE']." ". $contact_lastUpdate;
	}
?>

					</div>
	            </div>
   			</div>
    <!-- End Body Content -->

	<?php include $app_absolute_path.'inc/footer.php'; ?>
	
    <!-- End site footer -->
  	<a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include $app_absolute_path.'inc/js.php'; ?>
<script src="<?php echo $app_absolute_path ?>js/jquery.multiselect.js"></script>
<script>
	$('select[multiple]').multiselect({
	    columns: 2,
	    placeholder: 'Select options'
	});
</script>
</body>
</html>
<?php
	function callback($buffer) 
	{
		global $addnum;
		return (str_replace("VAR_ADDNUM", $addnum, $buffer));
	}
	// OUTPUT BUFFER
	ob_end_flush();
?>