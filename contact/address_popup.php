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
	
// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

// ** CHECK FOR LOGIN **
//	checkForLogin();

// ** RETRIEVE OPTIONS THAT PERTAIN TO THIS PAGE **
	$options = new Options();

// ** CHECK FOR ID **
	$id = check_id();

// ** END INITIALIZATION *******************************************************

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
	$r_prev = mysql_query("SELECT id, fullname FROM " . TABLE_CONTACT . " AS contact WHERE fullname < \"" . $contact->fullname . "\" AND contact.hidden != 1 ORDER BY fullname DESC LIMIT 1", $db_link)
		or die(reportSQLError());
	$t_prev = mysql_fetch_array($r_prev); 
	$prev = $t_prev['id']; 
	if ($prev<1) $prev = $id; 
	$r_next = mysql_query("SELECT id, fullname FROM " . TABLE_CONTACT . " AS contact WHERE fullname > \"" . $contact->fullname . "\" AND contact.hidden != 1 ORDER BY fullname ASC LIMIT 1", $db_link)
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
include("../global_config.php");
include("../inc/pagehead.php");
?>
<style>
.user-row {
    margin-bottom: 14px;
}

.user-row:last-child {
    margin-bottom: 0;
}

.dropdown-user {
    margin: 13px 0;
    padding: 5px;
    height: 100%;
}

.dropdown-user:hover {
    cursor: pointer;
}

.table-user-information > tbody > tr {
    border-top: 1px solid rgb(221, 221, 221);
}

.table-user-information > tbody > tr:first-child {
    border-top: 0;
}


.table-user-information > tbody > tr > td {
    border-top: 0;
}
.toppad
{margin:10px;
}

</style>
<body class="home">
	<dsiv class="container">
		<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 toppad">
				<div class="panel panel-info">
				<div class="panel-heading">
  					<h3 class="panel-title"><?php echo("$contact->fullname"); ?></h3>
				</div>
				<div class="panel-body">
  					<div class="row">
  					<?php
					if ($contact->picture_url) { 
						$img = (PATH_MUGSHOTS . $contact->picture_url); 
					} 
					else { 
						$img = $app_absolute_path."images/nopicture.gif"; 
					} ?>
    				<div class="col-md-2 col-lg-2 " align="center"> <img alt="User Pic" src="<?php echo $img ?>" class="img-thumbnail img-responsive">
    				</div>
    				<div class="col-md-10 col-lg-10"> 
      					<table class="table table-user-information">
        					<tbody>
          						<tr>
            						<td>Category</td>
            						<td><?php
										// LIST GROUPS
										$tbl_groups = mysql_fetch_array($r_groups);
										$groupname = stripslashes( $tbl_groups['groupname'] );
										$group_id = $tbl_groups['groupid'];
										$Groups = $groupname;
										while ( $tbl_groups = mysql_fetch_array($r_groups) ) 
										{
											$groupname = stripslashes( $tbl_groups['groupname'] );
											$group_id = $tbl_groups['groupid'];
										}
										echo($Groups);
									?></td>
          						</tr>
          						<tr>
            						<td>IC No</td>
            						<td><?php echo $contact->icnum ?></td>
          						</tr>
          						<tr>
            						<td>Title</td>
            						<td><?php echo $contact->title ?></td>
          						</tr>
          						<?php
          							while ($tbl_address = mysql_fetch_array($r_address)){
										$address_refid = $tbl_address['refid'];
										$address_line1 = stripslashes( $tbl_address['line1'] );
										$address_city = stripslashes( $tbl_address['city'] );
										$address_state = stripslashes( $tbl_address['state'] );
										$address_zip = stripslashes( $tbl_address['zip'] );
										$address_phone1 = stripslashes( $tbl_address['phone1'] );
										$address_phone2 = stripslashes( $tbl_address['phone2'] );
										$address_country = $tbl_address['country'];

										echo "<tr><td>" . (($contact->primary_address == $address_refid) ? $lang[LBL_PRIMARY_ADDRESS] : $lang[LBL_ADDRESS]);
										echo "</td><td>";
										if ($address_line1) { echo "$address_line1"; }
										if ($address_city OR $address_state OR $address_zip) { echo "\n<BR>"; }
										if ($address_city) { echo "$address_city"; }
										if ($address_city AND $address_state) { echo ", "; }
										if ($address_state) { echo "$address_state"; }
										if ($address_zip) { echo " $address_zip"; }
										if ($address_phone1) { echo "\n<BR>$address_phone1"; }
										if ($address_phone2) { echo "\n<BR>$address_phone2"; }
										if ($address_country) 
										{ 
											echo "\n<br>$country[$address_country]";
										}
										echo "</td></tr>";
									}
          						?>
          						<tr>
          							<td>Email</td>
          							<td><?php
										$tbl_email = mysql_fetch_array($r_email);
										$email_address = stripslashes( $tbl_email['email'] );
										$email_type = stripslashes( $tbl_email['type'] );
										if ($email_address) {
											if ($options->useMailScript == 1) {
												echo("<a href=\"mailto:$email_address\">$email_address</a>");
											}
											while ($tbl_email = mysql_fetch_array($r_email)) {
												$email_address = stripslashes( $tbl_email['email'] );
												$email_type = stripslashes( $tbl_email['type'] );
												if ($options->useMailScript == 1) {
													echo("<a href=\"mailto:$email_address\">$email_address</a>");
												}
												if ($email_type) {
													echo(" ($email_type)");
												}
											}
										}
									?></td>
          						</tr>
          							<td>Confidential Information</td>
          							<td><?php
										$confisql = "SELECT * FROM " . TABLE_CONFIDENTIAL . " WHERE con_id = $id";
										$confi_list = mysql_query($confisql, $db_link);
										while ($tbl_grouplist = mysql_fetch_array($confi_list)) 
										{
											$list_con_line1 = $tbl_grouplist['con_line1'];
											$list_con_city = $tbl_grouplist['con_city'];
											$list_con_state = $tbl_grouplist['con_state'];
											$list_con_zip = $tbl_grouplist['con_zip'];
											$list_con_country = $tbl_grouplist['con_country'];
											$list_con_phone1 = $tbl_grouplist['con_phone1'];
											$list_con_phone2 = $tbl_grouplist['con_phone2'];

											if ($list_con_line1)
											echo $list_con_line1."<br>";
											if ($list_con_city)
											echo $list_con_city.",";
											if ($list_con_state)
											echo $list_con_state.",";
											if ($list_con_zip)
											echo $list_con_zip."<br>";
											if ($list_con_phone1)
											echo $list_con_phone1."<br>";
											if ($list_con_phone2)
											echo $list_con_phone2."<br>";
											if ($list_con_country) 
											{ 
												echo "$country[$list_con_country]";
											}
										}
									?></td>
          						</tr>
          						<tr>
          							<td>Area of Expertise</td>
          							<td>
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
											echo "<button class=\"btn btn-primary\" disabled=\"disabled\">".$list_areaname."</button>&nbsp;";
									 	}
									?>
          							</td>
          						</tr>
          					</tbody>
          				</table>
          				<h3>ASM Contributions</h3>
          				<table class="table table-user-information">
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
									if ( (!empty($list_committee)) && (!empty($list_position)) )
									{ 
										echo "<tr>"; 
										echo "<td>$list_committee</td>";
										echo "<td>$list_position</td>";
										echo "<td>$list_year</td>";
										echo "</tr>";
									}
								}
							?>
          				</table>
          			</div>
          		</div>
          	</div>
          	<div class="panel-footer">
          		<?php echo $lang[LAST_UPDATE].' '.($contact->last_update).'.'; ?>
                <a data-original-title="close" id="close" data-toggle="tooltip" type="button" class="btn btn-sm btn-danger pull-right"><i class="fa fa-times"></i> Close</a></span>
            </div>
        </div>
    </div>
</div>
<?php include_once("../inc/js.php"); ?>
<script>
$(document).ready(function() {
    var panels = $('.user-infos');
    var panelsButton = $('.dropdown-user');
    panels.hide();

    //Click dropdown
    panelsButton.click(function() {
        //get data-for attribute
        var dataFor = $(this).attr('data-for');
        var idFor = $(dataFor);

        //current button
        var currentButton = $(this);
        idFor.slideToggle(400, function() {
            //Completed slidetoggle
            if(idFor.is(':visible'))
            {
                currentButton.html('<i class="glyphicon glyphicon-chevron-up text-muted"></i>');
            }
            else
            {
                currentButton.html('<i class="glyphicon glyphicon-chevron-down text-muted"></i>');
            }
        })
    });
    $('#close').click(function(){
    	window.close();
    });

    $('[data-toggle="tooltip"]').tooltip();
});
</script>