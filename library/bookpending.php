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
                <h4>List Of Books Reservation</h4>
				<?php
				switch ($_GET['action']){
				case "approve" :
					approve_book();
				break;
				case "cancel" :
					cancel_book();
				break;
				case "done" :
					approve_done();
				break;
				default :
					list_pending();
				break;
				}
				?>
				<?php
				function approve_book() {
					global $app_absolute_path,$root_images_folder;
					$id = $_REQUEST['id'];
					$receipt = $_REQUEST['receipt'];
					$libdb_date = new Modules_sql;
					$qry_due = "select issue_period from library_settings";
					$libdb_date->query($qry_due);
					$libdb_date->next_record();
					$d = $libdb_date->record[0];
					$due = date('d-m-Y',mktime(0, 0, 0, date("m"), date("d")+$d,  date("Y")));
					$due_d = changeDate($due);
					$is_d = date("Y-m-d");

					
					$libdb = new Modules_sql;
					$lib_name = new Modules_sql;
					if(isset($_POST['submit'])) {
						if(!empty($_POST['check'])) {
							$check = TRUE;
							$check = $_POST['check'];
						}
						else {
						$check = FALSE;
						echo"<div class=\"alert alert-danger\">You have to check which books to approve!</div>";
						}

						$method = $_POST['method'];
						$receipt = $_POST['receipt'];
						if($check) {
							foreach($_POST['check'] as $check)
							{
								$qry = "update library_issue set get_action='".$method."', approve='Yes', date_issue='".$is_d."', date_due='".$due_d."',";
								$qry .=" receipt_no='".$receipt."', issue_status=3 where issue_id='".$check."'";
								$libdb->query($qry);			
							}
							include_once("../classes/audit_trail.php");
						    $audit_trail = new audit_trail();
						    $audit_trail->writeLog($_SESSION['usr_username'], "library", "Approve Books Receipt No : ".$receipt."");
						 	echo'<meta http-equiv="refresh" content="0;URL=receiptform.php?id='.$id.'&receipt='.$receipt.'">';
						}
					}
					
					
					$qry_lib = "SELECT library_books.book_recordid, library_books_unit.accession_no, library_books.book_title, library_books.book_isbn,";
					$qry_lib .=" library_books.book_author, library_issue.contact_id, library_issue.date_issue, library_issue.date_due, library_issue.issue_id,";
					$qry_lib .=" contact_contact.id, contact_contact.fullname, contact_contact.icnum from";
					$qry_lib .=" (((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid)";
					$qry_lib .=" left join library_issue on library_issue.accession_no=library_books_unit.accession_no)";
					$qry_lib .=" left join contact_contact on contact_contact.id=library_issue.contact_id)";
					$qry_lib .=" where library_issue.contact_id='".$id."' and library_issue.receipt_no='".$receipt."' and library_issue.approve!='Yes'";
					$qry_lib .=" ORDER BY library_issue.accession_no ASC";
					$libdb->query($qry_lib);
					
					$qry_name .=" select library_books_unit.accession_no, contact_contact.id, contact_contact.icnum, contact_contact.fullname,";
					$qry_name .=" contact_address.line1, contact_address.phone1, library_issue.date_issue, library_issue.issue_id,";
					$qry_name .=" library_issue.receipt_no, library_issue.get_action, contact_email.email from";
					$qry_name .=" ((library_issue left join library_books_unit on library_issue.accession_no=library_books_unit.accession_no),";
					$qry_name .=" (contact_contact left JOIN contact_address ON contact_address.id=contact_contact.id)";
					$qry_name .=" left join contact_email on contact_contact.id=contact_email.id)";
					$qry_name .=" where library_issue.contact_id ='".$id."' and library_issue.receipt_no='".$receipt."' and library_issue.contact_id=contact_contact.id and library_issue.approve!='Yes'";
					$lib_name->query($qry_name);
					$lib_name->next_record();
					
				?>
				<form action="<? echo $PHP_SELF ?>" method="post" name="cart">
					<input type="hidden" name="method" value="<? echo $lib_name->record[9]; ?>">
					<input type="hidden" name="receipt" value="<? echo date("Ymd").$lib_name->record[7]; ?>">
					<h5>List of Pending Books to be approved for:</h5>
					<dl class="dl-horizontal">
						<dt>Name</dt><dd><?php echo $lib_name->record[3]; ?></dd>
						<dt>Address</dt><dd><?php echo $lib_name->record[4]; ?></dd>
						<dt>Contact Number</dt><dd><?php echo $lib_name->record[5]; ?></dd>
						<dt>E-mail</dt><dd><?php echo $lib_name->record[10]; ?></dd>
						<dt>Method of receiving</dt><dd><?php 
															  if($lib_name->record[9]==1){
															  	echo "Dispatch By ASM";
															  }
															  elseif($lib_name->record[9]==2) {
															  	echo "Pick Up";
															  }
															  else {
															  	echo "No Method";
															  }
															  ?></dd>
					</dl>

					<h5>Books Information</h5>
					<div class="alert alert-info">Notes: Please tick on the check box for each records to approve the books</div>
					<table class="table table-striped">
					<tr> 
					  <td width="5%"><div align="center">No.</div></td>
					  <td width="15%">Member</td>
					  <td width="15%">Book Code</td>
					  <td width="20%">Book Title</td>
					  <td width="15%">Book ISBN</td>
					  <td width="15%">Date Issue Request</td>
					  <td width="8%">Action</td>
					</tr>
					<?php
					if($libdb->num_rows() == 0) {
						echo "<tr><td colspan=\"7\">No record</td></tr>";
					}
					else {
					$i = 1;
					while($libdb->next_record()) {
					?>
					<tr> 
					  <td><?php echo $i; ?></td>
					  <td><?php echo $libdb->record[10]; ?></td>
					  <td><?php echo $libdb->record[1]; ?></td>
					  <td><?php echo $libdb->record[2]; ?></td>
					  <td><?php echo $libdb->record[3]; ?></td>
					  <td><?php echo $libdb->record[6]; ?></td>
					  <td align="center"><?php
						echo "<input type=\"checkbox\" name=\"check[]\" value=\"".$libdb->record[8]."\">";
					  ?></td>
					</tr>
					<?php $i++;
						}
					}
					?>
					</table>
					<input type="submit" name="submit" value="Submit" class="btn btn-primary">
    			</form>
				<?php } ?>
				<?php
				function list_pending() {

				$libdb = new Modules_sql;

				$qry_all = "select count(*) from library_issue where approve!='Yes' and library_issue.receipt_no IS NOT NULL group by contact_id";
				$result = $libdb->query($qry_all);
				$row = $libdb->next_record();
				$total_rows = $libdb->record[0];
					
				$st = requestNumber($_REQUEST['st'], 0);
				$nh = requestNumber($_REQUEST['nh'], 20);

				$qry_member = "select contact_contact.id, contact_contact.fullname, contact_contact.icnum, library_issue.* from ";
				$qry_member .=" library_issue left join contact_contact on contact_contact.id=library_issue.contact_id";
				$qry_member .=" where library_issue.approve='No' and library_issue.receipt_no is not null";
				$qry_member .=" group by library_issue.receipt_no order by contact_contact.fullname ASC LIMIT ".$st.", ".$nh."";
				$libdb->query($qry_member);

				?>

				<div class="alert alert-info">Below are books that need to be approved by Librarians. Click on the name in the list for details.</div>
				<table class="table table-striped">
				<tr> 
					<td width="5%"><div align="center">No.</div></td>
					<td width="45%">Member Name</td>
					<td width="25%">Member IC</td>
					<td width="25%">Unit Books</td>
				</tr>
		        <?php
					if($libdb->num_rows() == 0) {
						echo "<tr><td colspan=\"4\">No record</td></tr>";
					}else{
						$i = $st+1;
						while($libdb->next_record()) {
				?>
				<tr> 
					<td><div align="center"><?php echo $i; ?></div></td>
					<td><?php echo "<a href=\"bookpending.php?action=approve&receipt=".$libdb->record[14]."&id=".$libdb->record[0]."\">".$libdb->record[1]."</a>"; ?></td>
					<td><?php echo $libdb->record[2]; ?></td>
					<td><?php echo $libdb->record[1]; ?></td>
				</tr>
		        <?php
					$i++;
					}
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
	  		<?php if ($total_rows > $nh) { ?>
	  			<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>
			<?php } ?>
	<?php } ?>
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