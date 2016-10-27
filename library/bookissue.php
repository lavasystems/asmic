<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0); 
session_start();
include("class.php");
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");

if (!isAllowed(array(501), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include '../inc/pagehead.php';
?>
<script language="javascript">
function Clickheretoprint(){ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25"; 
  var content_vlue = document.getElementById("print_content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('<html><head><title>ASMIC : Issue Books</title>');
   docprint.document.write('<link href="../asmic.css" rel="stylesheet" type="text/css">'); 
   docprint.document.write('</head><body onLoad="window.print()"><center>');          
   docprint.document.write(content_vlue);          
   docprint.document.write('</center></body></html>'); 
   docprint.document.close(); 
   docprint.focus(); 
}
</script>
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
                <? include("breadcrumb.php"); ?>
                <p></p>
                <h4>Issue Book</h4>
          		  <?php
            		  switch ($_GET['action']){
              			case "issue" :
              				issuebook($book_code,$member_ic);
              			break;
              			case "details" :
              				issuedetails($bookid);
              			break;
              			case "error" :
              				error_issue();
              			break;
              			case "success" :
              				issue_done();
              			break;
              			case "list" :
              				issue_list();
              			break;
              			default :
              				to_confirm();
              			break;
            			}
                ?>
                <?php
                function to_confirm(){
                  global $app_absolute_path,$root_images_folder;
                  if(isset($_REQUEST["member_ic"])) $ic = $_REQUEST["member_ic"];
                  else $ic ="";
                  if(isset($_REQUEST["book_code"])) $code = $_REQUEST["book_code"];
                  else $code = $_GET['code'];
                ?>
                <div class="alert alert-info">Enter Book Code and Member IC to issue new book</div>
                <form name="entercode" method="post" action="bookissue.php?action=issue" onSubmit="return validateField();">
                <dl class="dl-horizontal">
                  <dt>I/C Number</dt>
                  <dd><input class="form-control" name="member_ic" type="text" id="member_ic" value="<? echo $ic; ?>"></dd>
                  <dt>Book Code</dt>
                  <dd><input class="form-control" name="book_code" type="text" id="book_code" value="<? echo $code; ?>"></dd>
                  <dt></dt>
                  <dd>
                    <input type="submit" name="Submit" id="Submit" class="btn btn-primary">
                    <a class="btn btn-default" href="bookissue.php?action=list">View List of Issued Book(s)</a>
                  </dd>
                </form>

                <?php echo list_active(); ?>
                <?php echo list_pending(); ?>
                <?php echo check_list(); ?>
              <?php } /** --> END FUNCTION ENTER BOOK TO ISSUE <-- **/ ?>

              <?php
              /** --> START FUNCTION ISSUE BOOK <-- **/

              function issuebook($book_code,$member_ic) {

              	global $app_absolute_path,$root_images_folder;
              	$libdb = new Modules_sql;
              	$book_code = $_REQUEST["book_code"];  
              	$member_ic = $_REQUEST["member_ic"];

              	//To get user ID from contact table
              	$qry_id = "select id from contact_contact where icnum = '".$member_ic."' and delflag=0";
              	$libdb->query($qry_id);
              	$libdb->next_record();
              	$mbr_id = $libdb->record[0];
              	
              	$qry = "select library_books.book_recordid, library_books.book_title, library_books_unit.book_status, library_books_unit.accession_no,";
              	$qry .=" contact_contact.id, contact_contact.fullname, library_member.* from";
              	$qry .=" ((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid),";
              	$qry .=" (contact_contact left join library_member on contact_contact.id=library_member.contact_id))";
              	$qry .=" where library_member.contact_id = '".$mbr_id."' and library_books_unit.accession_no='".$book_code."'";	
              	$libdb->query($qry);
              	
              	if ($libdb->num_rows() == 0) {
              	$libdb->next_record();
              		echo "<div class=\"alert alert-danger\">Sorry we don't have any record of the book and the member as you have entered</div>";
              		echo "<a class=\"btn btn-default\" href=\"bookissue.php?member_ic=".$member_ic."&book_code=".$book_code."\">Back</a>";
              		echo "<a class=\"btn btn-primary\" href=\"../contact/edit.php?mode=new&start=library&ic=".$member_ic."&bookcode=".$book_code."\">Create New User</a>";
              	}
              	else {
              	$libdb->next_record();
              		if($libdb->record[8] < 1) {
                		echo "<div class=\"alert alert-danger\">Sorry, The person doesn't have any card left to issue book.</div>";
              			echo "<a class=\"btn btn-default\" href=\"bookissue.php?member_ic=".$member_ic."&book_code=".$book_code."\">Back</a>";
              		}
              		else if($libdb->record[2] == 'n') {
              			echo "<div class=\"alert alert-danger\">Sorry, The book Has been Issued</div>";
              			echo "<a class=\"btn btn-default\" href=\"bookissue.php?member_ic=".$member_ic."&book_code=".$book_code."\">Back</a>";
              		}
              		else { ?>

                  <div class="alert alert-info">The record has been validated. Make sure the below info is correct and Click 'Issue' to confirm.</div>
                  <?php
            			if(isset($_POST['submit'])) {
            		
            			//POSTED VALUE FROM FORM
            			$id = $_REQUEST['id'];
            			$date_issue = $_REQUEST["date_issue"];
            			$date_due = $_REQUEST["date_due"];
            			$book_code = $_REQUEST["book_code"];
            			$member_code = $_REQUEST["member_ic"];
            			$member_name = $_REQUEST["member_name"];
            			$book_title = $_REQUEST["book_title"];
            			$date1 = changeDate ($date_issue);
            			$date2 = changeDate ($date_due);
            			$dt_added = date("Y-m-d H:i:s");	
            			
            				if(!empty($_POST['date_due'])) {				  
            					$dd = changeDate($_POST['date_due']);
            					if($dd < date('Y-m-d')) {
            						$date_due = FALSE;
            						echo"<div class=\"alert alert-danger\">Plese make sure that you have entered the correct due date</div>";
            					}
            					else {
            						$date_due = TRUE;
            					}
            				}
            				else {
            					$date_due = FALSE;
            					echo"<div class=\"alert alert-danger\">Plese make sure that you have entered the correct due date</div>";
            				}
            				
            				if($date_due) {
            				  $qry_str = "insert into library_issue (issue_id, accession_no, contact_id, get_action, approve, member_name, book_title, date_issue, date_due, date_added) values";
            				  $qry_str .=" ('NULL', '".$book_code."','".$id."', '3', 'No', '".$member_name."', '".$book_title."','".$date1."','".$date2."','".$dt_added."');";
            				  $libdb->query($qry_str);
            				  
            				  /* reduce the cards of the member */
            				  $qry_str = "update library_member set member_cards = member_cards - 1 where contact_id = '".$id."';";
            				  $libdb->query($qry_str);
            				  
            				  /* change status of the book in the library to "not available" */
            				  $qry_str = "update library_books_unit set book_status = 'n' where accession_no = '".$book_code."';";
            				  $libdb->query($qry_str);
            				  
            				  include_once("../classes/audit_trail.php");
            				  $audit_trail = new audit_trail();
            				  $audit_trail->writeLog($_SESSION['usr_username'], "library", "Issue Books : ".$book_code."");
            				  echo'<meta http-equiv="refresh" content="0;URL=bookissue.php?action=success&id='.$id.'">';
            				}
            			}
            			?>
                  <form name="entercode" method="post" action="<?php echo $PHP_SELF ?>" class="form-horizontal">
                    <input name="id" type="hidden" id="id" value="<?php echo $mbr_id; ?>">
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Number</label>
                        <div class="col-sm-9">
                          <p class="form-control-static"><?php echo $member_ic; ?></p>
                        </div>
                      </div>
                      <input name="member_ic" type="hidden" id="member_ic" value="<? echo $member_ic; ?>">
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Member Name</label>
                        <div class="col-sm-9">
                          <p class="form-control-static"><?php echo $libdb->record[5]; ?></p>
                        </div>
                      </div>
                      <input name="member_name" type="hidden" id="member_name" size="30" value="<? echo $libdb->record[5]; ?>">
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Book Code</label>
                        <div class="col-sm-9">
                          <p class="form-control-static"><?php echo $book_code; ?></p>
                        </div>
                      </div>
                      <input class="inputbox" name="book_code" type="hidden" id="book_code" value="<? echo $book_code; ?>">
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Title</label>
                        <div class="col-sm-9">
                          <p class="form-control-static"><?php echo $libdb->record[1]; ?></p>
                        </div>
                      </div>
                      <input name="book_title" type="hidden" id="book_title" value="<? echo $libdb->record[1]; ?>">
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Date Issue</label>
                        <div class="col-sm-9">
                          <p class="form-control-static"><?php echo date("d-m-Y"); ?></p>
                        </div>
                      </div>
                      <input name="date_issue" type="hidden" id="date_issue" value="<? echo date("d-m-Y"); ?>">
                      <div class="form-group">
                        <label class="col-sm-3 control-label">Due Date</label>
                        <div class="col-sm-9">
                          <?php

                    			  $libdb_date = new Modules_sql;
                    				$qry_due = "select issue_period from library_settings";
                    				$libdb_date->query($qry_due);
                    				$libdb_date->next_record();
                    				
                    				$d = $libdb_date->record[0];
                    				
                    				if(isset($_POST["date_due"])) $due_d = $_POST["date_due"];
                    				else $due_d = date('d-m-Y',mktime(0, 0, 0, date("m"), date("d")+$d,  date("Y")));
                    				
                    			?>
                          <div class="input-group date col-xs-3" id="date_due">
                              <input name="date_due" type="text" value="<?php echo $due_d; ?>" class="form-control" placeholder="Select due date">
                              <span class="input-group-addon">
                                  <span class="fa fa-calendar"></span>
                              </span>
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-9">
                          <input class="btn btn-primary" type="submit" name="submit" value="Issue"> 
                          <a class="btn btn-default" href="bookissue.php">Cancel</a>
                        </div>
                      </div>
                  </form>
                <?php
                  }
                }
              } /** --> END FUNCTION ISSUE BOOK <-- **/ ?>

              <?php function error_issue() {

              if(isset($_REQUEST["err"])) $err = $_REQUEST["err"];
              else $err = "";
               	echo "<div class=\"alert alert-danger\">";
              		if($err=="card") {
              			echo "The User doesn't have any card left to issue book";
              			echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
              		}
              		if($err=="book") {
              			echo "The Book Code is not valid";
              			echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
                  }
              		if($err=="user") {
              			echo "The User is not valid";
              			echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
                  }
              	echo "</div>";
              }
              ?>

              <?php

              function issue_done() {
                  global $app_absolute_path, $root_images_folder;
              	$id = $_REQUEST['id'];
              	$libdb_date = new Modules_sql;
              	$is_d = date('Y-m-d');
              	$due_d = date('Y-m-d',mktime(0, 0, 0, date("m")+1, date("d"),  date("Y")));
              	
              	$libdb = new Modules_sql;
              	$libdb_info = new Modules_sql;
              	$lib_name = new Modules_sql;
              	if(isset($_POST['submit'])) {
              		if(!empty($_POST['method'])) {
              			$method = TRUE;
              			$method = $_POST['method'];
              		}
              		else {
              		$method = FALSE;
              		echo"<div class=\"alert alert-danger\">Please Choose way to receive book(s)</div>.";
              		}
              		
              		if(!empty($_POST['check'])) {
              			$check = TRUE;
              			$check = $_POST['check'];
              		}
              		else {
              		$check = FALSE;
              		echo"<div class=\"alert alert-danger\">You have to check which book(s) to approve!.</div>";
              		}
              		$receipt = $_POST['receipt'];
              		
              		if($method && $check) {

              		$counter = 0;
              		foreach($_POST['check'] as $check)
                      {
              			$due_d = $_REQUEST['date_due'][$counter];
              			
              			if($method == 1) {
              				$qry = "update library_issue set get_action='".$method."', approve='Yes', date_issue='".$is_d."', date_due='".$due_d."',";
              				$qry .=" receipt_no='".$receipt."', issue_status=4 where issue_id='".$check."'";
              				$libdb->query($qry);
              			}
              			else {
              				$qry = "update library_issue set get_action='".$method."', approve='Yes', date_issue='".$is_d."', date_due='".$due_d."',";
              				$qry .=" receipt_no='".$receipt."', issue_status=3 where issue_id='".$check."'";
              				$libdb->query($qry);
              			}
              			
              			$counter++;
                      }

              		echo'<meta http-equiv="refresh" content="0;URL=receiptform.php?id='.$id.'&receipt='.$receipt.'">';
              		}
              	}
              	
              	$qry_info = "select contact_contact.*,library_issue.issue_id from contact_contact left join library_issue";
              	$qry_info .=" on contact_contact.id=library_issue.contact_id where contact_contact.id='".$id."'";
              	$qry_info .=" and library_issue.receipt_no is NULL order by issue_id DESC";
              	$libdb_info->query($qry_info);
              	$libdb_info->next_record();
              	
              	$qry_name .=" select contact_contact.id, contact_contact.icnum, contact_contact.fullname,";
              	$qry_name .=" contact_address.id, contact_address.line1, contact_address.phone1,";
              	$qry_name .=" contact_email.id, contact_email.email from";
              	$qry_name .=" (((library_member left join contact_contact on contact_contact.id=library_member.contact_id)";
              	$qry_name .=" left join contact_address ON contact_address.id=contact_contact.id)";
              	$qry_name .=" left join contact_email on contact_contact.id=contact_email.id)";
              	$qry_name .=" where library_member.contact_id =".$id."";
              	$lib_name->query($qry_name);
              	$lib_name->next_record();
              	
              	$qry_lib = "SELECT library_books.book_recordid, library_books_unit.accession_no, library_books.book_title, library_books.book_isbn,";
              	$qry_lib .=" library_books.book_author, library_issue.contact_id, library_issue.date_issue, library_issue.date_due, library_issue.issue_id";
              	$qry_lib .=" from ((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid)";
              	$qry_lib .=" left join library_issue on library_issue.accession_no=library_books_unit.accession_no )";
              	$qry_lib .=" where library_issue.contact_id = ".$id." and approve='No' ORDER BY library_issue.accession_no ASC";
              	$libdb->query($qry_lib);	
              	
              ?>
              <form action="<?php echo $PHP_SELF ?>" method="post" name="cart">
                <input type="hidden" value="<? echo $id; ?>" name="id">
                <input type="hidden" name="receipt" value="<? echo date("Ymd").$libdb_info->record[10]; ?>">
                List of Issue Book(s) for <?php echo $lib_name->record[2]; ?>
                Address : <?php echo $lib_name->record[4]; ?>
                I/C Number: <?php echo $lib_name->record[1]; ?>
                Contact Number : <?php echo $lib_name->record[5]; ?>
                E-mail : <?php echo $lib_name->record[7]; ?>

                Book(s) Information
                Receive Book(s) By :
                <input type="radio" name="method" value="1" <? if ($_POST['method']==1) echo "checked"; ?>> Deliver by ASM
                <input type="radio" name="method" value="2" <? if ($_POST['method']==2) echo "checked"; ?>> Pick up

                <table class="table table-bordered">
                  <tr> 
                    <td width="15%"><div align="center"><b>Book Code</b></div></td>
                    <td width="14%"> <div align="center"><b>ISBN Number</b></div></td>
                    <td width="30%"> <div align="left"><b>Title</b></div></td>
                    <td width="15%"> <div align="left"><b>Author</b></div></td>
                    <td width="9%"> <div align="center"><b>Date Issue</b></div></td>
                    <td width="8%"><div align="center"><b>Date Due</b></div></td>
                    <td width="9%" colspan="2"><div align="center"><b>Action</b></div></td>
                  </tr>
                  <?php
                		if($libdb->num_rows() == 0) {
                			echo "<tr><td colspan=\"7\">There is no record currently</td></tr>";
                		}
                		else {
                		while($libdb->next_record()) {
              		?>
                  <tr> 
                    <td><?php echo $libdb->record[1]; ?></td>
                    <td><?php echo $libdb->record[3]; ?></td>
                    <td><?php echo $libdb->record[2]; ?></td>
                    <td><?php echo $libdb->record[4]; ?></td>
                    <td align="center"><?php echo dateFormat($libdb->record[6], "d-m-Y"); ?></td>
                    <td align="center"> 
                    <?php
                			if($libdb->record[7]=='0000-00-00') {
                				echo dateFormat($due_d, "d-m-Y");
                				echo "<input type=\"hidden\" name=\"date_due[]\" value=\"".$due_d."\">";
                			}
                			else {
                			echo dateFormat($libdb->record[7], "d-m-Y");
                		?>
                    <input type="hidden" name="date_due[]" value="<?php echo $libdb->record[7]; ?>"> 
                    <?php } ?>
                    </td>
                    <td style="text-align:center">
                      <input type="checkbox" name="check[]" value="<? echo $libdb->record[8]; ?>" <? foreach($_POST['check'] as $check) { if ($libdb->record[8]==$check) echo "checked"; } ?> >
                    </td>
                    <td style="text-align:center"> 
                      <a href="functions.php?action=delissue&amp;book=<? echo $libdb->record[1]; ?>&id=<? echo $id; ?>&cart=<? echo $libdb->record[8]; ?>">Cancel</a> 
                    </td>
                  </tr>
                  <?php } 
                    } ?>
                </table>

                Check all the books in the list and click &quot;Confirm&quot; to confirm you reservation OR Click &quot;Add More Books&quot; to reserve more books.
                <input type="submit" name="submit" value="Confirm" class="btn btn-primary">
                <a class="btn btn-default" href="category.php">Add more books</a>
              </form>
              <?php } ?>
              <?php
              function issue_list() {
              	global $app_absolute_path,$root_images_folder;
              	$libdb = new Modules_sql;
              	$qry_all = "select count(*) from library_issue where approve='Yes' and date_return is null";
              	$result = $libdb->query($qry_all);
              	$row = $libdb->next_record();
              	$total_rows = $libdb->record[0];
              	
              	$st = requestNumber($_REQUEST['st'], 0);
              	$nh = requestNumber($_REQUEST['nh'], 20);
              	
              	$qry_list = "Select * from library_issue where approve='Yes' and date_return='0000-00-00' order by date_added desc LIMIT ".$st.", ".$nh."";
              	$libdb->query($qry_list);
              	
              ?>

              <h4>List of Issued Book(s)</h4>
              <table class="table table-striped">
                <tr> 
                  <td><div align="center"><b>No.</b></div></td>
                  <td><div align="left"><b>Book Code</b></div></td>
                  <td><div align="left"><b>Book Title</b></div></td>
                  <td><div align="left"><b>Member Name</b></div></td>
                  <td><div align="left"><b>Date Issue</b></div></td>
                  <td><div align="left"><b>Status</b></div></td>
                </tr>
                <?php
            		if($libdb->num_rows() == 0) {
            			echo "<tr><td colspan=\"6\">There is no record currently</td></tr>";
            		} else {
            		  $i = $st + 1;
        				  while($libdb->next_record()) {
        			 ?>
                <tr> 
                  <td><div align="center"><? echo $i; ?></div></td>
                  <td><?php echo $libdb->record[1]; ?></td>
                  <td><?php echo $libdb->record[6]; ?></td>
                  <td><?php echo "<a href=\"lib_members.php?action=detail&id=".$libdb->record[2]."\">".$libdb->record[5]."</a>"; ?></td>
                  <td><?php echo dateFormat($libdb->record[7], "j M Y"); ?></td>
                  <td><?php
              		  if(($libdb->record[9] == '0000-00-00') && ($libdb->record[8] < date("Y-m-d"))) {
              				echo "<span class=\"label label-danger\">Overdue</span>";
              		  } else {
              				echo "On Loan";
              		  }
              		?></td>
                </tr>
                <?php
              		$i++;
              		}
              			$this_page = $_SERVER['PHP_SELF']."?action=list";
              		
              			if ($st > 0){
              				$prev_st = $st - $nh;
              				if ($prev_st < 0) $prev_st = 0;
              				$first_link = $this_page.'&st=0&nh=' . $nh;
              				$prev_link = $this_page.'&st=' . $prev_st . '&nh=' .$nh;
              			}
              			else {
              				$first_link = '';
              				$prev_link = '';
              			}
              		
              			if (($st + $nh) < $total_rows){
              				$last_st = (ceil($total_rows / $nh) - 1) * $nh;
              				$next_link = $this_page.'&st=' . ($st + $nh)  . '&nh=' . $nh;
              				$last_link = $this_page.'&st=' . $last_st . '&nh=' .$nh;
              			}
              			else {
              				$next_link = '';
              				$last_link = '';
              			}
              		}
              		?>
                </table>
                <div class="alert alert-info">* Status in <span class="label label-danger">Overdue</span> is overdue record. Click on the member name in the list for member details.</div>

                <?php if($total_rows > $nh) { ?>
                <?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
                <?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
                <?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
                <?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>  
                <?php } ?>
              <?php } ?>

              <?php function list_pending() {

              $libdb = new Modules_sql;
              $lib_count = new Modules_sql;

              $qry_all = "select count(*) from library_issue where approve!='Yes' and receipt_no IS NOT NULL group by receipt_no";
              $result = $libdb->query($qry_all);
              $row = $libdb->next_record();
              $total_rows = $libdb->num_rows();
              	
              $st = requestNumber($_REQUEST['st'], 0);
              $nh = requestNumber($_REQUEST['nh'], 20);

              $qry_member = "select count(*),contact_contact.id, contact_contact.fullname, contact_contact.icnum, library_issue.* from ";
              $qry_member .=" library_issue left join contact_contact on contact_contact.id=library_issue.contact_id";
              $qry_member .=" where library_issue.approve='No' and library_issue.receipt_no is not null";
              $qry_member .=" group by library_issue.receipt_no order by contact_contact.fullname ASC LIMIT ".$st.", ".$nh."";
              $libdb->query($qry_member);
              ?>

              <h4>Reservation List</h4>
              <div class="alert alert-info">Below are books that need to be approved by Librarians. Click on the name in the list for details.</div>

              <table class="table table-striped">
                <tr> 
                  <td width="5%"><div align="center"><strong>No.</b></strong></div></td>
                  <td width="27%"><b>Member Name</b></td>
                  <td width="15%"><b>I/C Number</b></td>
        		      <td width="15%"><b>Receipt No</b></td>
        		      <td width="17%" align="center"><b>Receipt Issued Date</b></td>
                  <td width="10%" align="center"><b>Unit Books</b></td>
                </tr>
                <?php
              		if($libdb->num_rows() == 0) {
              		echo "<tr><td class=\"m2_td_content\" colspan=\"6\">There is no record currently</td></tr>";
              		}
              		else {
              		$i = $st+1;
              		while($libdb->next_record()) {
            		?>
                <tr> 
                  <td><div align="center"><? echo $i; ?></div></td>
                  <td><?php echo "<a href=\"bookpending.php?action=approve&receipt=".$libdb->record[15]."&id=".$libdb->record[1]."\">".$libdb->record[2]."</a>"; ?></td>
                  <td><?php echo $libdb->record[3]; ?></td>
                  <td><?php echo $libdb->record[15]; ?></td>
                  <td align="center"><?php echo date_format($libdb->record[14], "j M Y"); ?></td>		  		  
                  <td align="center"><?php echo $libdb->record[0]; ?></td>
                </tr>
                <?php $i++; } ?>
                <?php
            		 $this_page = $_SERVER['PHP_SELF']."?";
            	
            				if ($st > 0){
            					$prev_st = $st - $nh;
            					if ($prev_st < 0) $prev_st = 0;
            					$first_link = $this_page.'&st=0&nh=' . $nh;
            					$prev_link = $this_page.'&st=' . $prev_st . '&nh=' .$nh;
            				}
            				else {
            					$first_link = '';
            					$prev_link = '';
            				}
            			
            				if (($st + $nh) < $total_rows){
            					$last_st = (ceil($total_rows / $nh) - 1) * $nh;
            					$next_link = $this_page.'&st=' . ($st + $nh)  . '&nh=' . $nh;
            					$last_link = $this_page.'&st=' . $last_st . '&nh=' .$nh;
            				}
            				else {
            					$next_link = '';
            					$last_link = '';
            				}
            		}
            		?>
              </table>
              <?php if($total_rows > $nh) { ?>
              <?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
              <?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
              <?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
              <?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>  
              <?php } ?>
            <?php } ?>

          <?php
            function list_active() {

            $libdb = new Modules_sql;
            $dt = date('Y-m-d');

            $qry_member = "select contact_contact.id, contact_contact.fullname, contact_contact.icnum, library_issue.* from ";
            $qry_member .=" library_issue left join contact_contact on contact_contact.id=library_issue.contact_id";
            $qry_member .=" where library_issue.approve='No' and library_issue.receipt_no is NULL and library_issue.date_issue = '".$dt."'";
            $qry_member .=" group by library_issue.contact_id order by contact_contact.fullname ASC LIMIT 0,10";
            $libdb->query($qry_member);
            ?>
            <h4>Current Issuing Process</h4>
            <div class="alert alert-info">Below are current member in issuing procces. Click on the name in the list for details.</div>
            <table class="table table-striped">
              <tr> 
                <td width="5%"><div align="center"><strong>No.</b></strong></div></td>
                <td width="45%"><b>Member Name</b></td>
                <td width="25%"><b>I/C Number</b></td>
              </tr>
              <?php
            		if($libdb->num_rows() == 0) {
            		echo "<tr><td colspan=\"3\">There is no record currently</td></tr>";
            		}else {
            		$i = 1;
            		while($libdb->next_record()) {
            	?>
              <tr> 
                <td><div align="center"><? echo $i; ?></div></td>
                <td><?php echo "<a href=\"bookissue.php?action=success&&id=".$libdb->record[0]."\">".$libdb->record[1]."</a>"; ?></td>
                <td><?php echo $libdb->record[2]; ?></td>
              </tr>
              <?php $i++; }
            } ?>
            </table>
          <?php } ?>

          <?php function check_list() {
          	global $app_absolute_path,$root_images_folder;
          	$libdb = new Modules_sql;

          	if(isset($_POST['submit'])) {
          		if(!empty($_POST['check'])) {
          			$check = TRUE;
          			$check = $_POST['check'];
          		}
          		else {
          		$check = FALSE;
          		echo"<div class=\"alert alert-danger\">You have to check which book(s) to checkout!</div>";
          		}
          		if($check) {
          			foreach($_POST['check'] as $check)
          			{
          					$qry = "update library_issue set issue_status=5 where issue_id='".$check."'";
          					$libdb->query($qry);
          					
          					include_once("../classes/audit_trail.php");
          					$audit_trail = new audit_trail();
          					$audit_trail->writeLog($_SESSION['usr_username'], "library", "Approved Book Chek Out(Issue ID: ".$check."");			
          			}
          			
          			echo "<div class=\"alert alert-success\">Book(s) has been succesfully checked out.</div>";
          		}
          	}
	
          	$qry_lib = "SELECT library_books.book_recordid, library_books_unit.accession_no, library_books.book_title, library_books.book_isbn,";
          	$qry_lib .=" library_books.book_author, library_issue.contact_id, library_issue.date_issue, library_issue.date_due, library_issue.issue_id,";
          	$qry_lib .=" library_issue.receipt_no, contact_contact.id, contact_contact.fullname, contact_contact.icnum, library_issue.get_action,";
          	$qry_lib .=" library_issue.date_return from (((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid)";
          	$qry_lib .=" left join library_issue on library_issue.accession_no=library_books_unit.accession_no)";
          	$qry_lib .=" left join contact_contact on contact_contact.id=library_issue.contact_id)";
          	$qry_lib .=" where library_issue.approve='Yes' and library_issue.receipt_no is not null and library_issue.date_return='0000-00-00'";
          	$qry_lib .=" and library_issue.issue_status=3 or library_issue.issue_status=4 ORDER BY contact_contact.fullname ASC LIMIT 0,10";
          	$libdb->query($qry_lib);
          ?>

          <h4>Pending Book Issued</h4>
          <div class="alert alert-info">Please check any book in the list below, to make sure that the book(s) has already reached/sent to the borrower</div>
            <form action="<? echo $PHP_SELF ?>" method="post" name="cart">
              <input type="hidden" name="issue_id" value="<? echo $libdb->record[8]; ?>">
              <table class="table table-striped">
                <tr> 
                  <td width="4%"> <div align="center"><strong>No.</strong></div></td>
                  <td width="13%"> <div align="center"><strong>Member</strong></div></td>
                  <td width="13%"> <div align="left"><strong>Book Code</strong></div></td>
                  <td width="18%"> <div align="left"><strong>Book Title</strong></div></td>
                  <td width="13%"> <div align="center"><strong>Book ISBN</strong></div></td>
                  <td width="13%"><div align="center"><strong>Date Issued </strong></div></td>
                  <td width="16%"><div align="center"><strong>Method of Receive</strong></div></td>
                  <td width="10%"> <div align="center"><strong>Check</strong></div></td>
                </tr>
                <?php
            		if($libdb->num_rows() == 0) {
            			echo "<tr><td colspan=\"8\">There is no record currently</td></tr>";
            		}
            		else {
            		$i = 1;
            		while($libdb->next_record()) {
            		if($libdb->record[13]==1) {
            			$action = "Dispatch by ASM";
            		}
            		if($libdb->record[13]==2) {
            			$action = "Pick-up";
            		}
            		?>
                <tr> 
                  <td align="center" valign="top"><? echo $i; ?></td>
                  <td valign="top"><?php echo $libdb->record[11]; ?></td>
                  <td valign="top"><?php echo $libdb->record[1]; ?></td>
                  <td valign="top"><?php echo $libdb->record[2]; ?></td>
                  <td valign="top"><?php echo $libdb->record[3]; ?></td>
                  <td valign="top"><?php echo DateConvert($libdb->record[6], "d-m-Y"); ?></td>
                  <td valign="top"><?php echo $action; ?></td>
                  <td align="center" ><?php echo "<input type=\"checkbox\" name=\"check[]\" value=\"".$libdb->record[8]."\">";?></td>
                </tr>
                <?php $i++; } 
              } ?>
              </table>

              <a class="btn btn-default" href="bookchecklist.php">More Check List</a>
              <input type="submit" name="submit" value="Approve" class="btn btn-primary">
            </form>
            <?php } ?>
            <script language="javascript">
            	function validateField() {
            		
                	var form = document.entercode;
                
            		if (form.elements["member_ic"].value==0) {
            			alert( "You must enter Member IC" );
            			form.elements["member_ic"].focus();
            			return false;
            		}
            	
            		if (form.elements["book_code"].value == "") {
            			alert( "You must enter the Book Code" );
            			form.elements["book_code"].focus();
            			return false;
            		}
            	}
            </script>
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
