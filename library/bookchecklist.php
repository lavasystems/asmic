<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
session_start();
$id = $_SESSION['usr_id'];
$permission = $_SESSION['permissions'];
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include '../inc/pagehead.php';
include("class.php");
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
                <div class="row">
                <h3>Issued Books Check List</h3>
                <div class="alert alert-info">Please check any books in the list below, to make sure that the books has already reached/sent to the borrower</div>
        <?php
		  		global $app_absolute_path,$root_images_folder;
				  $libdb = new Modules_sql;

				if(isset($_POST['submit'])) {
					if(!empty($_POST['check'])) {
						$check = TRUE;
						$check = $_POST['check'];
					}
					else {
					$check = FALSE;
					echo"<div class=\"alert alert-danger\">You have to check which books to checkout!</div>";
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
						
						echo "<div class=\"alert alert-success\">Books has been succesfully checked out.</div>";
					}
				}
				
				
				$qry_lib = "SELECT library_books.book_recordid, library_books_unit.accession_no, library_books.book_title, library_books.book_isbn,";
				$qry_lib .=" library_books.book_author, library_issue.contact_id, library_issue.date_issue, library_issue.date_due, library_issue.issue_id,";
				$qry_lib .=" library_issue.receipt_no, contact_contact.id, contact_contact.fullname, contact_contact.icnum, library_issue.get_action from";
				$qry_lib .=" (((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid)";
				$qry_lib .=" left join library_issue on library_issue.accession_no=library_books_unit.accession_no)";
				$qry_lib .=" left join contact_contact on contact_contact.id=library_issue.contact_id)";
				$qry_lib .=" where library_issue.issue_status=3 or library_issue.issue_status=4 and library_issue.approve='Yes'";
				$qry_lib .=" and library_issue.receipt_no is not null ORDER BY contact_contact.fullname ASC";
				$libdb->query($qry_lib);
				
			?>
            <form action="<?php echo $PHP_SELF ?>" method="post" name="cart">
              <input type="hidden" name="issue_id" value="<? echo $libdb->record[8]; ?>">
              <input type="hidden" name="receipt" value="">
				        <table class="table table-striped">
                      <tr> 
                        <td width="5%"><div align="center">No.</div></td>
                        <td width="15%"><div align="center">Member</div></td>
                        <td width="15%"><div align="left">Book Code</div></td>
                        <td width="20%"><div align="left">Book Title</div></td>
                        <td width="15%"><div align="center">Book ISBN</div></td>
                        <td width="15%"><div align="center">Date Issued </div></td>
						            <td width="15%"><div align="center">Method Of Receive</div></td>	
                        <td width="8%"> <div align="center">Check</div></td>
                      </tr>
        <?php
					if($libdb->num_rows() == 0) {
						echo "<tr><td colspan=\"8\">No record</td></tr>";
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
                        <td align="center"><?php echo $i; ?></td>
                        <td><?php echo $libdb->record[11]; ?></td>
                        <td><?php echo $libdb->record[1]; ?></td>
                        <td><?php echo $libdb->record[2]; ?></td>
                        <td><?php echo $libdb->record[3]; ?></td>
                        <td><?php echo DateConvert($libdb->record[6], "d-m-Y"); ?></td>
                        <td><?php echo $action; ?></td>						
                        <td> 
                          <?
							echo "<input type=\"checkbox\" name=\"check[]\" value=\"".$libdb->record[8]."\">";
					  ?>
                        </td>
                      </tr>
                      <?
					$i++;
					}
					}
					?>
                    </table>
                    <input type="submit" name="submit" class="btn btn-primary" value="Submit">
            </form>
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