<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
session_start();
$id = $_SESSION['usr_id'];
$permission = $_SESSION['permissions'];
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501, 502,503), $_SESSION['permissions'])){
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
                  <?php
                  if (isAllowed(array(501), $permission)){
                    echo admin_index();
                  }
                  if (isAllowed(array(502,503), $permission)){
                    echo user_index();
                  }
                  if (isAllowed(array(502,503), $permission)){
                    include("pubrightbar.php");
                  }
                  ?>
                  <?php
                  function admin_index() {
                  		global $app_absolute_path, $root_images_folder;
                  		$libdb = new Modules_sql;
                  		$qry_books = "Select date_added, book_isbn, book_title, book_publisher, book_author, book_recordid";
                  		$qry_books .=" from library_books where (DAYOFYEAR(NOW()) - DAYOFYEAR(date_added)) <7 group by book_isbn order by date_added desc";
                  		$qry_books .=" LIMIT 0, 10";
                  		$libdb->query($qry_books);
                  ?>
<div class="row">
  <div class="col-lg-4 col-lg-offset-4">
  	<form name="form1" method="post" action="search_result.php">
      <div class="form-group">
        <input name="keyword" type="text" id="keyword" class="form-control">
        <input type="submit" class="btn btn-primary" value="Search"> 
        <a class="btn btn-default" href="search_books.php?page=advance">Advanced Search</a>
      </div>
    </form>
  </div>
</div>
  
  <a class="btn btn-default pull-right" href="newbooklist.php">More new book arrival</a>
  <h4>New Book(s) Arrival</h4>
	<table class="table table-striped">
    <tr>
      <td width="33%"><strong>Book Title</strong></td>
      <td width="21%"><strong>Publisher</strong></td>
      <td width="18%"><strong>Author</strong></td>
      <td width="12%"><strong>Date Added</strong></td>
      <td width="16%"><strong>Book ISBN</strong></td>
    </tr>
    <?php
		if($libdb->num_rows() == 0) {
			echo "<tr><td colspan=\"5\">No Latest Book(s)</td></tr>";
		}
		else{
			while($libdb->next_record()) {
			?>
        <tr>
          <td><a href="bookdetails.php?id=<? echo $libdb->record[5]; ?>"><? echo $libdb->record[2]; ?></a></td>
          <td><?php echo $libdb->record[3]; ?></td>
          <td><?php echo $libdb->record[4]; ?></td>
          <td><?php echo DateConvert($libdb->record[0], "j M Y"); ?></td>
          <td><?php echo $libdb->record[1]; ?></td>
        </tr>
        <?
		  }
		}
		?>
  </table>
	<div class="alert alert-info">Below are list of reserved book(s) which <b>haven't be confirmed</b> by the user. Click on the <i class="fa fa-times"></i> on each record to cancel the reservation.</div>

	<?php
		global $app_absolute_path, $root_images_folder;
		$lib_reserve = new Modules_sql;
		
		$qry_all = "select count(*) from library_issue where receipt_no is NULL";
		$result = $libdb->query($qry_all);
		$row = $libdb->next_record();
		$total_rows = $libdb->record[0];
		
		$st = requestNumber($_REQUEST['st'], 0);
		$nh = requestNumber($_REQUEST['nh'], 7);
		$page = ceil($total_rows/$nh);
		$dt = date('Y-m-d');
		
		//LIST WILL BE DISPLAYED BOOKS WHICH HAVEN'T BEEN CONFIRMED MORE THAN ONE DAY
		$qry = "select library_issue.issue_id,library_issue.contact_id,library_issue.date_issue,library_issue.receipt_no,";
		$qry .=" library_books.book_recordid,library_books.book_title,library_books.book_isbn,library_books_unit.accession_no,";
		$qry .=" contact_contact.fullname, contact_contact.id from (library_issue inner join contact_contact on";
		$qry .=" library_issue.contact_id=contact_contact.id) inner join library_books inner join library_books_unit on";
		$qry .=" library_books.book_recordid=library_books_unit.book_recordid where";
		$qry .=" library_issue.accession_no=library_books_unit.accession_no and library_issue.receipt_no is NULL";
		$qry .=" and library_issue.date_issue < '".$dt."' order by library_issue.date_issue desc LIMIT ".$st.", ".$nh."";
		$lib_reserve->query($qry);
		?>
    <h4>Reserved Book(s)</h4>
		<table class="table table-striped">
      <tr>
        <td width="15%">Book Code</td>
        <td width="30%">Book Title</td>
        <td width="20%">Reserved By</td>
        <td width="12%">Date reserved</td>
        <td width="8%">Action</td>
        </tr>
        <?php
    		if($lib_reserve->num_rows()==0) {
    			echo"<tr><td colspan=\"5\">No records</tr></td>";
    		}
    		else {
    		  while($lib_reserve->next_record()){
		    ?>
      <tr>
        <td><?php echo $lib_reserve->record[7]; ?></td>
        <td><?php echo $lib_reserve->record[5]; ?></td>
        <td><a href="bookissue.php?action=success&amp;id=<?php echo $lib_reserve->record[9]; ?>"><?php echo $lib_reserve->record[8]; ?></a></td>
        <td><?php echo date_format($lib_reserve->record[2], "j M Y"); ?></td>
        <td align="center"><a href="functions.php?action=cancelreserve&amp;book=<?php echo $lib_reserve->record[7]; ?>&amp;id=<?php echo $lib_reserve->record[1]; ?>&amp;cart=<? echo $lib_reserve->record[0]; ?>" onClick="return confirm('Are you sure to cancel the reservation?')"><i class="fa fa-times"></i></a></td>
      </tr>
      <?php } ?>
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
		} ?>
	  <?php if($total_rows > $nh) { ?>
	  <?php echo "Total Books:" .$total_rows." Page ".(ceil($st/$nh)+1)." of ".$page; ?>
		<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
    <?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
    <?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
    <?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>
    <?php } ?>
    <?php }
	?>
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