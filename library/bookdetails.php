<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0); 
session_start();
$id = $_SESSION['usr_id'];
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");

if (!isAllowed(array(501), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include '../inc/pagehead.php';
?>
<script type="text/javascript" src="expand.js"></script>
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
  <?php
		include("class.php");
		global $app_absolute_path,$root_images_folder;
    if(isset($_REQUEST['isbn'])){
      $isbn = $_REQUEST['isbn'];
    }
		if(isset($_REQUEST['id'])){
      $id = $_REQUEST['id'];
    }
		  $libdb = new Modules_sql;
		  $libdb_detail = new Modules_sql;
		  $qry_str = "select * from library_books where book_recordid ='".$id."'";
		  $libdb_detail->query($qry_str);
		  $libdb_detail->next_record();
		  $split = explode("|", $libdb_detail->record[11]);
      $sub1 = $split[0];
      $sub2 = $split[1];				
      $sub3 = $split[2];
	  ?>
    <?php include("breadcrumb.php"); ?>
    <p></p>
    <div class="row">
      <div class="col-lg-3">
      <h4>Book Details</h4>
      <?php
  		  $libdb2 = new Modules_sql;
  		  $qry_cat = "select * from library_category where category_id ='".$libdb_detail->record[1]."'";
  		  $libdb2->query($qry_cat);
  		  $libdb2->next_record();
  		  
  		  if(empty($libdb_detail->record[20])){
  		  	echo "<img src=\"http://placehold.it/150x180?text=No+Image\" class=\"img-thumbnail\">";
  		  }
  		  else{
          echo "<img src=\"uploads/".$libdb_detail->record[20]."\" class=\"img-thumbnail\">";
  		  }
  		?>
      <?php if(!empty($libdb_detail->record[23])) {
        echo "<p><a target=\"_blank\" href=\"toc/".$libdb_detail->record[23]."\">Read Table Of Content</a></p>"; 
      } ?>
      <p>
      <?php echo stripslashes($libdb_detail->record[14]); ?>
      </p>
  		
      <?php if(!empty($libdb_detail->record[14])) { ?>
  			<a class="btn btn-default" href="bookabstract.php?id=<?php echo $_REQUEST['id']; ?>" target="name" onclick="window.open('bookabstract.php?id=<?php echo $_REQUEST['id']; ?>','name','height=520,width=450,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no');"><i class="fa fa-print"></i> Print</a>
  		<?php } ?>
      <a class="btn btn-primary" href="bookupdate.php?isbn=<? echo $libdb_detail->record[15]; ?>"><i class="fa fa-book"></i> Update</a>
    </div>

    <div class="col-lg-6">

      <dl class="dl-horizontal">
        <dt>CATEGORY</dt>
        <dd><?php echo $libdb2->record[2]; ?></dd>
        <dt>CALL NO</dt>
        <dd><?php echo $libdb_detail->record[22]; ?></dd>
        <dt>AUTHOR</dt>
        <dd><?php echo $libdb_detail->record[3]; ?></dd>
        <dt>TITLE</dt>
        <dd><?php echo $libdb_detail->record[2]; ?></dd>
        <dt>EDITION</dt>
        <dd><?php echo $libdb_detail->record[4]; ?> Ed</dd>
        <dt>PUBLICATION</dt>
        <dd><?php echo $libdb_detail->record[5]; ?> / <? echo $libdb_detail->record[6]; ?></dd>
        <?php if($libdb_detail->record[7]){ ?>
        <dt>DESCRIPTION</dt>
        <dd><?php echo $libdb_detail->record[7]; ?> / <? echo $libdb_detail->record[8]; ?>&nbsp;cm / <? echo $libdb_detail->record[9]; ?>&nbsp;pg</dd>
        <?php } ?>
        <?php if($libdb_detail->record[10]){ ?>
        <dt>INDEXES</dt>
        <dd><?php echo $libdb_detail->record[10]; ?></dd>
        <?php } ?>
        <?php if($sub1){ ?>
        <dt>SUBJECT</dt>
        <dd>1) <?php echo $sub1; ?><br>
            2) <?php echo $sub2; ?><br>
            3) <?php echo $sub3; ?></dd>
        <?php } ?>
        <?php if($libdb_detail->record[12]){ ?>
        <dt>EDITOR</dt>
        <dd><?php echo $libdb_detail->record[12]; ?></dd>
        <?php } ?>
        <?php if($libdb_detail->record[13]){ ?>
        <dt>SECOND AUTHOR</dt>
        <dd><? echo $libdb_detail->record[13]; ?></dd>
        <?php } ?>
        <dt>ISBN NO</dt>
        <dd><?php echo $libdb_detail->record[15]; ?> / <? echo $libdb_detail->record[16]; ?> copy(s)</dd>
        <dt>ISSN NO</dt>
        <dd><?php echo $libdb_detail->record[17]; ?> / <? echo $libdb_detail->record[18]; ?> / <? echo $libdb_detail->record[19]; ?></dd>
      </dl>
    </div>

    <div class="col-lg-3">
      <h4>Add more book unit</h4>
      <div class="alert alert-info">Do you want to add more Book Unit under this ISBN number? If yes, Please enter another Book Code in the form below</div>
      <?php
  	  if(isset($_POST['submit'])) {

    		//check form
    		if(!empty($_POST['bookcode'])) {
    			 $qry_str = "select count(*) accession_no from library_books_unit where accession_no ='".$_POST['bookcode']."'";
    			 $libdb->query($qry_str);
    			 $libdb->next_record();
    			 if ($libdb->record[0]) {
    			 $bookcode = FALSE;
    			 echo "<div class=\"alert alert-danger\">Book Code already Exist. Please enter other code.</div>";
    			 }
    			 else{
    				$bookcode = TRUE;
    				$bookcode = $_POST['bookcode'];
    			 }
    		}
    		else {
    		  $bookcode = FALSE;
    		  echo"<div class=\"alert alert-warning\">Please enter the book code.</div>";
    		}
    	  $copy = $libdb_detail->record[16] + 1;
    	  $dt_added = date("Y-m-d H:i:s");
    	  if($bookcode) {
    		  $qry = "update library_books set book_copies='".$copy."' where book_recordid='".$id."'";
    		  $libdb->query($qry);
    		  
    		  $qry_str = "insert into library_books_unit (book_recordid, accession_no, book_status, date_added) values";
    		  $qry_str .=" ('".$libdb_detail->record[0]."','".$bookcode."','y','".$dt_added."')";
    		  if($libdb->query($qry_str)) {
    		  		include_once("../classes/audit_trail.php");
    				$audit_trail = new audit_trail();
    				$audit_trail->writeLog($_SESSION['usr_username'], "library", "Insert Books Unit: ".$bookcode."");
    				echo'<meta http-equiv="refresh" content="0;URL=bookdetails.php?id='.$id.'">';
    		  }
    		  else {
    				echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
    		  }
    	  }
  	  }
  	  ?>
      <form action="<?php echo $PHP_SELF ?>" method="post" name="newcode" id="newcode">
        <label>Book Code</label>
        <input name="bookcode" type="text" class="form-control" id="bookcode">
  			<input type="submit" name="submit" value="Search" class="btn btn-default">
      </form>
    </div>
    </div>
    <div class="clearfix"></div>
    <div class="alert alert-info">Below are the list of books under the same ISBN Number.</div>
    <?php
				  
			$qry_all = "select count(*) from library_books_unit where book_recordid ='".$libdb_detail->record[0]."';";
			$result = $libdb->query($qry_all);
			$row = $libdb->next_record();
			$total_rows = $libdb->record[0];
			
			$st = requestNumber($_REQUEST['st'], 0);
			$nh = requestNumber($_REQUEST['nh'], 10);

			$qry_str = "select a.accession_no, a.book_status, b.book_title, b.book_author, b.book_copies, b.book_recordid";
			$qry_str .=" from library_books_unit a, library_books b where a.book_recordid ='".$libdb_detail->record[0]."'";
			$qry_str .=" and a.book_recordid=b.book_recordid order by a.accession_no LIMIT ".$st.", ".$nh.";";
			$libdb->query($qry_str);
		  
		?>
    <table class="table table-striped">
      <tr> 
        <td width="4%">No.</td>
        <td width="16%">BOOK CODE</td>
        <td width="20%">BOOK TITLE</td>
        <td width="16%">AUTHOR</td>
        <td width="13%">STATUS</td>
        <td width="20%">ACTION</td>
      </tr>
      <?php
  		  if($libdb->num_rows() == 0) {
  			 echo "<tr><td colspan=\"6\">No record</td></tr>";
  		  }
  		  else {
  		    $i= $st + 1;
  		    while($libdb->next_record()){
  		  
  			   if($libdb->record[1] == 'y'){
  				  $status = "Available";
  			   }else{
  				  $status = "Issued";
  			   }
		  ?>
      <tr> 
        <td><?php echo $i; ?></td>
        <td><?php echo $libdb->record[0]; ?></td>
        <td><?php echo $libdb->record[2]; ?></td>
        <td><?php echo $libdb->record[3]; ?></td>
        <td><?php echo $status; ?></td>
        <td><?php if($libdb->record[1] == 'y') { ?>
          <a class="btn btn-default btn-xs" href="bookmodify.php?accno=<? echo $libdb->record[0]; ?>"><i class="fa fa-pencil" title="Modify Book Code"></i></a> 
          <?php }
					   if(($libdb->record[1] == 'y') && ($libdb->record[4] >1)) { ?>
          <a class="btn btn-default btn-xs" href="bookmodify.php?action=delete&amp;accno=<?php echo $libdb->record[0]; ?>"><i class="fa fa-times" title="Delete"></i></a> 
          <?php }
						if($libdb->record[4]<=1) {
							echo "<a class=\"btn btn-default btn-xs\" href=\"bookmodify.php?action=del_all&id=".$libdb->record[5]."\" onClick=\"return confirm('Are you sure want to delete the whole book?')\"><i class=\"fa fa-times\" title=\"Delete\"></i></a>";
						}
          ?>
          <a class="btn btn-default btn-xs" href="bookprint.php?id=<?php echo $libdb->record[0]; ?>" target="_blank" onclick="window.open(this.href, this.target, 'height=800,width=800,status'); return false;"><i class="fa fa-print" title="Print"></i></a>
          <?php
						if($libdb->record[1] == 'y') {
							echo "<a class=\"btn btn-default btn-xs\" href=\"bookissue.php?code=".$libdb->record[0]."\"><i class=\"fa fa-book\" title=\"Issue Book\"></i></a>";
						}
					?>
          <?php $i++;
					  }
					  $this_page = $_SERVER['PHP_SELF']."?id=".$id."";
		
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
          </td>
        </tr>
      </table>

            <?php if($total_rows > $nh) { ?>
            <table width="179"  border="0" align="right" cellpadding="0" cellspacing="1"  class="m2_table_outline">
              <tr> 
                <td width="38" class="m2_td_content"><div align="right"><span class="fontcolorblue">&laquo;</span> 
                    <?=generateLink('First', $first_link)?>
                  </div></td>
                <td width="60" class="m2_td_content"><div align="right"><span class="fontcolorblue">&lsaquo;</span> 
                    <?=generateLink('Previous', $prev_link)?>
                  </div></td>
                <td width="40" class="m2_td_content"><div align="right"> 
                    <?=generateLink('Next', $next_link)?>
                    <span class="fontcolorblue">&rsaquo;</span></div></td>
                <td width="42" class="m2_td_content"><div align="right"> 
                    <?=generateLink('End', $last_link)?>
                    <span class="fontcolorblue">&raquo;</span></div></td>
              </tr>
            </table>
            <?php } ?>
          
          <div class="alert alert-info">* You cannot do any other action except print out the book details if the book is still issued.</div>
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