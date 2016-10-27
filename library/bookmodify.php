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
      <?php include("breadcrumb.php"); ?>
      <p></p>
      <h4>BOOK DETAILS</h4>
		  <?php
        switch ($_GET['action']){
          case "delete" :
            delete_code();
          break;
          case "del_all" :
            delete_all();
          break;
          default :
            edit_code();
          break;
        }
		  ?>
      <?php
      function edit_code(){
      		global $id, $isbn, $app_absolute_path,$root_images_folder;
      		
      		$isbn = $_REQUEST["accno"];
      		
      		$libdb2 = new Modules_sql;
      		$libdb = new Modules_sql;
      		$qry_record = "select * from library_books inner join library_books_unit ON library_books_unit.book_recordid=library_books.book_recordid";
      		$qry_record .=" where library_books_unit.accession_no = '".$isbn."'"; 
      		$libdb2->query($qry_record);
      		$libdb2->next_record();
      		$unitid = $libdb2->record[24];
      		$id = $libdb2->record[0];
      		
      		$split = explode("|", $libdb2->record[11]);
      	    $sub1 = $split[0];
      	    $sub2 = $split[1];				
      	    $sub3 = $split[2];

      if(isset($_POST['submit'])) {	
      	if(!empty($_POST['book_code'])) {
      		 $qry_str = "select count(*) accession_no from library_books_unit where accession_no ='".$_POST['book_code']."'";
      		 $libdb->query($qry_str);
      		 $libdb->next_record();
      		 if ($libdb->record[0]) {
      		 $book_code = FALSE;
      			echo "<div class=\"alert alert-danger\">Book Code has already exist.</div>";
      		 }
      		 else{
      			$book_code = TRUE;
      			$book_code = $_POST['accession_no'];
      		 }
      	}
      	else {
      	$book_code = FALSE;
      	echo"<div class=\"alert alert-danger\">Please enter the book code.</div>";
      	}
      	$book_code = $_REQUEST['book_code'];
      	if($book_code) {
      		$qry_str ="UPDATE library_books_unit SET accession_no='".$book_code."' WHERE unit_id ='".$unitid."';";
      		if($libdb2->query($qry_str)) {
      			include_once("../classes/audit_trail.php");
      		    $audit_trail = new audit_trail();
      		    $audit_trail->writeLog($_SESSION['usr_username'], "library", "Modify Books : ".$book_code."");
              //var_dump($_POST);die;
      			echo'<meta http-equiv="refresh" content="0;URL=bookdetails.php?id='.$book_code.'">';
      		}
      		else {
      			echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
      		}
      	}
      							
      }
      ?>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="books" class="form-horizontal">
<div class="alert alert-info">You are only allowed to modify the book code for any book unit.
<a class="btn btn-default" href="bookupdate.php?isbn=<?php echo $libdb2->record[15]; ?>">Click here</a> if you want to update the details of the book.</div>
<form class="form-horizontal">
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">Book Rack</label>
    <div class="col-sm-9">
    <input class="form-control" readonly type="text" name="bookrack" value="<?php echo $libdb2->record[22]; ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">Book Code</label>
    <div class="col-sm-9">
    <input class="form-control" name="book_code" type="text" id="book_code" value="<?php echo $libdb2->record[26]; ?>">
    </div>
  </div>
  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">Category</label>
    <div class="col-sm-9">
      <select disabled="true" class="form-control" id="select" name="cat">
        <?php 
           $libdb3 = new Modules_sql;
           $qry_str = "select category_id, category_name from library_category order by category_name";
           $libdb3->query($qry_str);
           $kat = $libdb2->record[1];
           while($libdb3->next_record()) {
           if ( $kat == $libdb3->record[0] )
              {
                print( "<option disabled value=\"".$libdb3->record[0]."\" selected>".$libdb3->record[1]."</option>\n" );
              }
            else {  
            
              echo "<option disabled value=\"".$libdb3->record[0]."\">".$libdb3->record[1]."</option>";
            }
          }
        ?>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">100 10</label>
    <div class="col-sm-9">
      <input class="form-control" readonly name="author" type="text" id="author" value="<?php echo $libdb2->record[3]; ?>"> 
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">245 10</label>
    <div class="col-sm-9">
      <input class="form-control" readonly type="text" id="book_name" name="book_name" value="<?php echo $libdb2->record[2]; ?>"> 
      </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">250 00</label>
    <div class="col-sm-9">
      <input class="form-control" readonly name="edition" type="text" id="edition" value="<?php echo $libdb2->record[4]; ?>"> 
      </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">260 00</label>
    <div class="col-xs-4">
      <input class="form-control" readonly name="publisher" type="text" id="publisher" value="<?php echo $libdb2->record[5]; ?>">
    </div>
    <div class="col-xs-2">
        <input class="form-control" readonly name="year" type="text" id="year" value="<?php echo $libdb2->record[6]; ?>"> 
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">300 00</label>
    <div class="col-xs-3">
      <input class="form-control" readonly name="ill" type="text" id="ill" value="<?php echo $libdb2->record[7]; ?>">
    </div>
    <div class="col-xs-3">
      <input class="form-control" readonly name="height" type="text" id="height" value="<?php echo $libdb2->record[8]; ?>">
    </div>
    <div class="col-xs-3">
        <input class="form-control" readonly name="page" type="text" id="page" value="<?php echo $libdb2->record[9]; ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">500 00</label>
    <div class="col-xs-9">
      <select disabled="true" class="form-control" name="indexes" id="indexes">
        <option disabled="true" value="" <?php if(empty($libdb2->record[10])) { echo "selected"; } ?>>- Book Index -</option>
        <option disabled="true" value="1" <?php if($libdb2->record[10] == 'Yes') { echo "selected"; } ?>>Yes</option>
        <option disabled="true" value="2" <?php if($libdb2->record[10] != 'Yes') { echo "selected"; } ?>>No</option>
      </select>
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">600 00</label>
    <div class="col-xs-3">
      <div class="input-group">
        <span class="input-group-addon">1</span>
        <input class="form-control" readonly name="sub1" type="text" id="sub1" value="<?php echo $sub1; ?>">
      </div>
    </div>
    <div class="col-xs-3">
      <div class="input-group">
        <span class="input-group-addon">2</span>
        <input class="form-control" readonly name="sub2" type="text" id="sub2" value="<?php echo $sub2; ?>">
      </div>
    </div>
    <div class="col-xs-3">
      <div class="input-group">
        <span class="input-group-addon">3</span>
        <input class="form-control" readonly name="sub3" type="text" id="sub3" value="<?php echo $sub3; ?>">
      </div>
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">700 00</label>
    <div class="col-xs-9">
      <input class="form-control" readonly name="editor" type="text" id="editor" value="<?php echo $libdb2->record[12]; ?>">
    </div>
  </div>

  <div class="form-group">
    <label for="inputEmail3" class="col-sm-3 control-label">700 10</label>
    <div class="col-xs-9">
      <input class="form-control" readonly name="elt" type="text" id="elt" value="<?php echo $libdb2->record[13]; ?>">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">800 00</label>
    <div class="col-xs-9">
      <input class="form-control" readonly type="text" id="isbn" name="isbn" value="<?php echo $libdb2->record[15]; ?>">
      <input class="form-control" readonly name="copies" type="text" id="copies" value="<?php echo $libdb2->record[16]; ?>">
    </div>
  </div>

  <div class="form-group">
    <label class="col-xs-3 control-label">800 10</label>
    <div class="col-xs-3">
      <input class="form-control" readonly name="issn" type="text" id="issn" value="<?php echo $libdb2->record[17]; ?>">
    </div>
    <div class="col-xs-3">
      <input class="form-control" readonly name="volume" type="text" id="volume" value="<?php echo $libdb2->record[18]; ?>">
    </div>
    <div class="col-xs-3">
      <input class="form-control" readonly name="no" type="text" id="no" value="<?php echo $libdb2->record[19]; ?>">
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Abstract</label>
    <div class="col-xs-9">
      <textarea class="form-control" readonly name="summary" cols="60" rows="5" id="textarea"><? echo $libdb2->record[14]; ?></textarea>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">Images</label>
    <div class="col-xs-9">
      <?php
    	  if(!empty($libdb2->record[20])) {	
    		  echo "<img class=\"img-thumbnail\" src=\"uploads/".$libdb2->record[20]."\" title=\"".$libdb2->record[20]."\"> <span class=\"label label-info\">Current Image</span>";
    	  }else{
    	  	echo "<p class=\"form-control-static\">No picture Provided</p>";
    	  }
  	  ?>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label">TOC</label>
    <div class="col-xs-9">
      <?php
        if(!empty($libdb2->record[23])){
          echo "<p class=\"form-control-static\"><span class=\"label label-info\">Current TOC</span> <a target=\"_blank\" href=\"toc/".$libdb2->record[23]."\">".$libdb2->record[23]."</a></p>";
	       }else{
	  	    echo "<p class=\"form-control-static\">No Table of Content Provided</p>";
	       }
      ?>
    </div>
  </div>

  <div class="form-group">
    <label class="col-sm-3 control-label"></label>
    <div class="col-xs-9">
      <input type="submit" name="submit" value="Submit" class="btn btn-primary">
      <a class="btn btn-default" href="#" onClick="history.go(-1);return true;"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
  </div>
</form>
<?php } ?>

<?php
  function delete_code() {
  global $app_absolute_path,$root_images_folder;
  $accno = $_REQUEST['accno'];
	$libdb = new Modules_sql;
	$libdb2 = new Modules_sql;
	$libdb_all = new Modules_sql;
	$qry_record = "select * from library_books inner join library_books_unit on library_books_unit.book_recordid=library_books.book_recordid";
	$qry_record .=" where library_books_unit.accession_no = '".$accno."'"; //and accession_no ='".$id."'";
	$libdb_all->query($qry_record);
	$libdb_all->next_record();
	$libdb_all->record[16];
	$isbn = $libdb_all->record[15];
	$id = $libdb_all->record[0];
	
	if(isset($_POST['submit'])) {	
			if(!empty($_POST['book_code'])) {
				 $qry_str = "select count(*) accession_no from library_books_unit where accession_no ='".$_POST['book_code']."' and book_status = 'n'";
				 $libdb2->query($qry_str);
				 $libdb2->next_record();
				 if ($libdb2->record[0]) {
				 $book_code = FALSE;
					echo "<div class=\"alert alert-danger\">Book cannot be delete due to the status which still being issued.</div>";
				 }
				 else{
					$book_code = TRUE;
					$book_code = $_POST['book_code'];
				 }
			}
			
			if($book_code) {
				$qry_str = "delete from library_books_unit where accession_no='".$book_code."';";;
				if($libdb->query($qry_str)) {
					$copy = $libdb_all->record[16] - 1;
					
					$qry = "update library_books set book_copies='".$copy."' where book_recordid='".$id."'";
					$libdb->query($qry);
					
					include_once("../classes/audit_trail.php");
					$audit_trail = new audit_trail();
					$audit_trail->writeLog($_SESSION['usr_username'], "library", "Delete Books Unit: ".$book_code."");
					
					$qry ="select count(*) from library_books where book_recordid='".$id."'";
					$libdb->query($qry);
					$libdb->next_record();
					if($libdb->record[0]) {
						echo'<meta http-equiv="refresh" content="0;URL=bookdetails.php?id='.$id.'">';
					}
					else {
						echo'<meta http-equiv="refresh" content="0;URL=category.php">';
					}
				}
				else {
					echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
				}
			}
									
		} 
	?>
	<form name="form1" method="post" action="<?php echo $PHP_SELF; ?>">
    You are about to delete book unit :
    <dl class="dl-horizontal">
      <dt>Book Code</dt>
      <dd><input class="form-control" readonly name="book_code" type="text" id="book_code" value="<?php echo $accno; ?>"></dd>
      <dt>Book Title</dt>
      <dd><?php echo $libdb_all->record[2]; ?></dd>
      <dt>Book Author</dt>
      <dd><?php echo $libdb_all->record[3]; ?></dd>
      <dt>Book Summary</dt>
      <dd><?php echo $libdb_all->record[14]; ?></dd>
      <dt></dt>
      <dd>
        <input type="submit" name="submit" value="Submit" class="btn btn-primary">
        <a class="btn btn-default" href="#" onClick="history.go(-1);return true;"><i class="fa fa-arrow-left"></i> Back</a>
      </dd>
    </dl>
    <input type="hidden" name="isbn" value="<?php echo $libdb_all->record['15']; ?>">
  </form>
<?php } ?>

<?php
  function delete_all() {
  global $app_absolute_path,$root_images_folder;
  $id = $_REQUEST['id'];

	$libdb = new Modules_sql;
	$libdb2 = new Modules_sql;
	$libdb_all = new Modules_sql;
	$qry_record = "select library_books.*, library_books_unit.* from library_books inner join library_books_unit on library_books_unit.book_recordid=library_books.book_recordid";
	$qry_record .=" where library_books_unit.book_recordid = '".$id."'"; //and accession_no ='".$id."'";
	$libdb_all->query($qry_record);
	$libdb_all->next_record();
	$libdb_all->record[16];
	$isbn = $libdb_all->record[15];
	$code= $libdb_all->record[24];
	
	if(isset($_POST['submit'])) {	
			if(!empty($_POST['book_code'])) {
				 $qry_str = "select count(*) accession_no from library_books_unit where accession_no ='".$_POST['book_code']."' and book_status = 'n'";
				 $libdb2->query($qry_str);
				 $libdb2->next_record();
				 if ($libdb2->record[0]) {
				 $book_code = FALSE;
					echo "<div class=\"alert alert-danger\">Book cannot be delete due to the status which still being issued.</div>";
				 }
				 else{
					$book_code = TRUE;
					$book_code = $_POST['book_code'];
				 }
			}
			
			if($book_code) {
				$qry_str = "delete from library_books_unit where book_recordid='".$book_code."';";;
				if($libdb->query($qry_str)) {
					
					$qry = "delete from library_books where book_recordid='".$book_code."'";
					$libdb->query($qry);
					
					include_once("../classes/audit_trail.php");
					$audit_trail = new audit_trail();
					$audit_trail->writeLog($_SESSION['usr_username'], "library", "Delete Books : ".$book_code."");
					
					echo'<meta http-equiv="refresh" content="0;URL=category.php">';
				}
				else {
					echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
				}
			}
									
		} 
	?>
	<form name="form1" method="post" action="<? echo $PHP_SELF; ?>">
  You are about to delete THE BOOK RECORD :-
  Book ID : <input class="inputbox" readonly name="book_code" type="text" id="book_code" value="<? echo $id; ?>">
	Book ISBN : <? echo $libdb_all->record[15]; ?>
  Book Title : <? echo $libdb_all->record[2]; ?>
  Book Author : <? echo $libdb_all->record[3]; ?>
  Book Summary : <? echo $libdb_all->record[14]; ?>
  <input type="submit" name="submit" value="Submit" class="btn btn-primary">
  <a class="btn btn-default" href="#" onClick="history.go(-1);return true;"><i class="fa fa-arrow-left"></i> Back</a>
  </form>
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