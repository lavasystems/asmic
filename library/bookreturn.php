<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0); 
session_start();
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include("class.php");
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
                <div class="row">
                <!-- Content Start -->
                <h2>Return Book</h2>
    <?php
			switch ($_GET['action']) {
				case "confirm" :
					return_book($book_code);
				break;
				case "return" :
					return_success();
				break;
				default :
					return_main();
				break;
			}
		?>
<?php function return_main() { ?>
  <div class="alert alert-info">Please enter Book Code to verify the Book information</div>
  <form name="enter_book" method="post" action="bookreturn.php?action=confirm" onSubmit="return confirmBook();">
    <dl class="dl-horizontal">
      <dt>Book Code</dt>
      <dd><input class="form-control" name="book_code" type="text" id="book_code" value="<?php echo $_GET['book_code']; ?>"></dd>
      <dt></dt>
      <dd><input type="submit" name="submit" value="Submit" class="btn btn-primary">
      <a class="btn btn-default" href="#" onClick="history.go(-1);return true;"><i class="fa fa-arrow-left"></i> Back</a></dd>
    </dl>
  </form>
<?php } ?>
<?php
function return_book() {
	$libdb = new Modules_sql;
	$book_code = $_REQUEST['book_code'];
	$qry = "select library_issue.accession_no, library_issue.contact_id, library_issue.book_title, library_issue.date_issue,";
	$qry .=" library_issue.date_due, library_issue.date_return,";
	$qry .=" contact_contact.icnum, contact_contact.id, contact_contact.fullname from library_issue";
	$qry .=" left join contact_contact on library_issue.contact_id=contact_contact.id where";
	$qry .=" library_issue.accession_no='".$book_code."' and library_issue.approve='Yes' and library_issue.date_return='0000-00-00'";
	$libdb->query($qry);  
?>
<div class="alert alert-info">Below are details of book to be returned</div>
		<?php
		  if ($libdb->num_rows() == 0) {
			 echo "<div class=\"alert alert-danger\">Sorry, No Data Present</div>";
			 echo "<a class=\"btn btn-default\" href=\"bookreturn.php?book_code=".$book_code."\">Go Back</a>";
		  }
		  else {
			 $libdb->next_record();
		  ?>
		  
		  <form name="returnbook" method="post" action="functions.php?action=return" onSubmit="return validateField();">
        <input type="hidden" name="book_code" value="<?php echo $libdb->record[0]; ?>">
		    <input type="hidden" name="id" value="<?php echo $libdb->record[1]; ?>">
			  <dl class="dl-horizontal">
          <dt>I/C Number</dt>
          <dd><?php echo $libdb->record[6]; ?></dd>
          <dt>Member Name</dt>
          <dd><?php echo $libdb->record[8]; ?></dd>
          <dt>Book Code</dt>
          <dd><?php echo $libdb->record[0]; ?></dd>
          <dt>Title</dt>
          <dd><?php echo $libdb->record[2]; ?></dd>
          <dt>Date Issue</dt>
          <dd><?php echo DateConvert($libdb->record[3], "j M Y"); ?></dd>
          <dt>Due Date</dt>
          <dd><?php
					echo DateConvert($libdb->record[4], "j M Y"); 
					if(date("Y-m-d") > $libdb->record[4]) {
						echo "<span class=\"label label-danger\">Overdue</span>"; 
					}
				  ?></dd>
          <dt>Return Date</dt>
          <dd><?php echo date("d-m-Y"); ?></dd>
          <dt></dt>
          <dd><input type="submit" name="submit" value="Submit" class="btn btn-primary">
          <a class="btn btn-default" href="bookreturn.php">Cancel</a></dd>
        </form>
			<?php } ?>
<?php } ?>
<?php
  function return_success() {			
  	echo "<div class=\"alert alert-success\">The book has succesfully returned</div>";
  	echo "<a class=\"btn btn-default\" href=\"bookreturn.php\">Go Back</a>";
  }
?>
<script language="javascript">
	function confirmBook() {
    var form = document.enter_book;
    
		if (form.elements["book_code"].value==0) {
			alert( "You must enter the Book Code to validate the Book" );
			form.elements["book_code"].focus();
			return false;
		}
	}
	
	function validateField() {
    var form = document.returnbook;
    
		if (form.elements["date_return"].value==0) {
			alert( "You must enter the Return Date" );
			form.elements["date_return"].focus();
			return false;
		}		
	}
</script>
</div>
    <!-- End Body Content -->

  <?php include '../inc/footer.php'; ?>
  
    <!-- End site footer -->
    <a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include '../inc/js.php'; ?>
</body>
</html>