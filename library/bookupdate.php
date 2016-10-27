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

$isbn = $_REQUEST['isbn'];

include("class.php");
global $app_absolute_path,$root_images_folder;

$libdb = new Modules_sql;
$libdb2 = new Modules_sql;
$qry_record = "select * from library_books where book_isbn ='".$isbn."'";
$libdb->query($qry_record);
$libdb->next_record();
$id = $libdb->record[0];
$split = explode("|", $libdb->record[11]);
$sub1 = $split[0];
$sub2 = $split[1];				
$sub3 = $split[2];

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
                <?php include("breadcrumb.php"); ?>
                <p></p>
                <h4>Update Book Details</h4>
                <div class="alert alert-info">You can edit the book details by filling up the form below with a new data.</div>
                <?php if(isset($_POST['submit'])) {

					//Book Image
					if(empty($_FILES['bookimg']['tmp_name'])) {
						$_FILES['bookimg']['name'] = $libdb->record[20];
					}
					else {
						// This is the temporary file created by PHP 
						$uploadedfile = $_FILES['bookimg']['tmp_name'];
						
						// Create an Image from it so we can do the resize
						$src = imagecreatefromjpeg($uploadedfile);
						
						// Capture the original size of the uploaded image
						list($width,$height)=getimagesize($uploadedfile);
						
						// For our purposes, I have resized the image to be
						// 600 pixels wide, and maintain the original aspect 
						// ratio. This prevents the image from being "stretched"
						// or "squashed". If you prefer some max width other than
						// 600, simply change the $newwidth variable
						$newwidth=200;
						$newheight=($height/$width)*200;
						$tmp=imagecreatetruecolor($newwidth,$newheight);
						
						// this line actually does the image resizing, copying from the original
						// image into the $tmp image
						imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height); 
						
						// now write the resized image to disk. I have assumed that you want the
						// resized, uploaded image file to reside in the ./images subdirectory.
						$filename = "uploads/". $_FILES['bookimg']['name'];
						imagejpeg($tmp,$filename,100);
						
						imagedestroy($src);
						imagedestroy($tmp);
					}
					
					//Table of content
					require_once('fileupload-class.php');
					
					if(empty($_FILES['booktoc']['tmp_name'])) {
						$my_toc->file['name'] = $libdb->record[23];
					}
					else {
						$my_toc = new uploader('en'); 
						$my_toc->max_filesize(1048576);
						$my_toc->upload("booktoc", "", ".pdf");
						$mode = 1;
						$my_toc->save_file("toc/", $mode);
						// Check if everything worked
						if ($my_toc->error) {
							echo "<div class=\"alert alert-danger\">".$my_toc->error."</div>";
						} else {
							// Successful upload!
							if(stristr($my_toc->file['type'], "pdf")) {
							}
						}
					}
					
			  $cat = $_REQUEST['cat'];
			  $book_name = ereg_replace("'", "\'", $_REQUEST["book_name"]); 
			  $author = ereg_replace("'", "\'", $_REQUEST["author"]);
			  $edition = $_REQUEST["edition"];
			  $publisher = ereg_replace("'", "\'", $_REQUEST["publisher"]);
			  $year = $_REQUEST["year"];
			  $ill = $_REQUEST["ill"];
			  $height = $_REQUEST["height"];
			  $page = $_REQUEST["page"];
			  $index = $_REQUEST["indexes"];
			  $subject = ereg_replace("'", "\'", $_REQUEST['sub1'])."|".ereg_replace("'", "\'", $_REQUEST['sub2'])."|".ereg_replace("'", "\'", $_REQUEST['sub3']);
			  $editor = ereg_replace("'", "\'", $_REQUEST["editor"]);
			  $elt = ereg_replace("'", "\'", $_REQUEST["elt"]);
			  $summary =  ereg_replace("'", "\'", $_REQUEST["summary"]);
			  $isbn = $_REQUEST["isbn"];
			  $copy = $_REQUEST["copies"];
			  $issn = $_REQUEST["issn"];
			  $vol = $_REQUEST["volume"];
			  $no = $_REQUEST["no"];
			  $bookrack = $_REQUEST["bookrack"];
			  $dt_added = date("Y-m-d H:i:s");
				
			
				if($isbn && empty($my_uploader->error) && empty($my_toc->error)) {
					$qry_str ="UPDATE library_books SET category_id='$cat', book_title='$book_name.', book_author='$author',";
					$qry_str .="book_edition='$edition',book_publisher='$publisher',book_year='$year',book_illustration='$ill',";
					$qry_str .="book_height='$height',book_page='$page',book_indexes='$index',";
					$qry_str .="book_subject='$subject',book_editor='$editor',book_elt='$elt',book_summary='$summary',book_isbn='$isbn',";
					$qry_str .="book_copies='$copy',book_issn='$issn',book_volume='$vol',book_number='$no',";
					$qry_str .="book_image='".$_FILES['bookimg']['name']."', book_rack='$bookrack',book_toc='".$my_toc->file['name']."' WHERE book_isbn ='$isbn';";
					
					if($libdb2->query($qry_str)) {
						include_once("../classes/audit_trail.php");
						$audit_trail = new audit_trail();
						$audit_trail->writeLog($_SESSION['usr_username'], "library", "Update Books Details: ".$isbn."");
						echo'<meta http-equiv="refresh" content="0;URL=bookdetails.php?id='.$id.'">';
					}
					else {
						echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
					}
				}
										
			}
			?>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="books" class="form-horizontal">
            <div class="form-group">
			<label class="col-sm-3 control-label">Call No</label>
				<div class="col-xs-3">
        			<input class="form-control" name="bookrack" type="text" id="bookrack" value="<?php echo $libdb->record[22]; ?>">
        		</div>
            </div>
            
            <div class="form-group">
			<label class="col-sm-3 control-label">Category</label>
				<div class="col-xs-6">
            		<select class="form-control" id="cat" name="cat">
                        <option value="0">-- Select a category --
                        <?php
							$libdb_cat = new Modules_sql;
							$qry_str = "select category_id, category_name from library_category order by category_name";
							$libdb_cat->query($qry_str);
							$kat = $libdb->record[1];
							while($libdb_cat->next_record()) {
								if($kat == $libdb_cat->record[0]){
									print( "<option value=\"".$libdb_cat->record[0]."\" selected>".$libdb_cat->record[1]."</option>\n" );
								}else{	
									echo "<option value=\"".$libdb_cat->record[0]."\">".$libdb_cat->record[1]."</option>";
								}
							}
						?>
                    </select>
                </div>
            </div>

            <div class="form-group">
			<label class="col-sm-3 control-label">100 10</label>
				<div class="col-xs-6">
            		<input class="form-control" name="author" type="text" id="author" value="<?php echo $libdb->record[3]; ?>">
            	</div>
            </div>

            <div class="form-group">
			<label class="col-sm-3 control-label">245 10</label>
				<div class="col-xs-6">
            		<input class="form-control" type="text" id="book_name" name="book_name" value="<?php echo $libdb->record[2]; ?>">
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">250 00</label>
				<div class="col-xs-3">
            		<input class="form-control" name="edition" type="text" id="edition" value="<?php echo $libdb->record[4]; ?>">
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">260 00</label>
				<div class="col-xs-3">
            		<input class="form-control" name="publisher" type="text" id="publisher" value="<?php echo $libdb->record[5]; ?>">
            	</div>
            	<div class="col-xs-3">
            		<input class="form-control" name="year" type="text" id="year" value="<?php echo $libdb->record[6]; ?>">
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">300 00</label>
				<div class="col-xs-3">
            		<input class="form-control" name="ill" type="text" id="ill" value="<?php echo $libdb->record[7]; ?>">
            	</div>
            	<div class="col-xs-3">
            		<input class="form-control" name="height" type="text" id="height" value="<?php echo $libdb->record[8]; ?>">
            	</div>
            	<div class="col-xs-3">
            		<input class="form-control" name="page" type="text" id="page" value="<?php echo $libdb->record[9]; ?>">
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">500 00</label>
				<div class="col-xs-3">
		            <select class="form-control" name="indexes" id="indexes">
		            <?php
						if(!empty($libdb->record[10])) {
							if($libdb->record[10] == 'Yes') {
								echo "<option value=\"1\" selected>Yes</option>";
								echo "<option value=\"2\">No</option>";
							}else{
								echo "<option value=\"1\">Yes</option>";
								echo "<option value=\"2\" selected>No</option>";
							}
						}else{
					?>
		                <option value="">- Book Index -</option>
		                <option value="1">Yes</option>
		                <option value="2">No</option>
		            <?php } ?>
		            </select>
		        </div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">600 00</label>
				<div class="col-xs-3">
      				<div class="input-group">
        				<span class="input-group-addon">1</span>
            			<input class="form-control" name="sub1" type="text" id="sub1" value="<?php echo $sub1; ?>">
            		</div>
            	</div>
            	<div class="col-xs-3">
      				<div class="input-group">
        				<span class="input-group-addon">2</span>
        				<input class="form-control" name="sub2" type="text" id="sub2" value="<?php echo $sub2; ?>">
        			</div>
        		</div>
        		<div class="col-xs-3">
      				<div class="input-group">
        				<span class="input-group-addon">3</span>
            			<input class="form-control" name="sub3" type="text" id="sub3" value="<?php echo $sub3; ?>">
            		</div>
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">700 00</label>
				<div class="col-xs-3">
            		<input class="form-control" name="editor" type="text" id="editor" value="<?php echo $libdb->record[12]; ?>">
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">700 10</label>
				<div class="col-xs-3">
            		<input class="form-control" name="elt" type="text" id="elt" size="40" value="<?php echo $libdb->record[13]; ?>">
            	</div>
            </div>

            <div class="alert alert-info">* You cannot modify ISBN No and copies of books</div>
            <div class="form-group">
			    <label class="col-sm-3 control-label">800 00</label>
				<div class="col-xs-3">
            		<input readonly class="form-control" type="text" id="isbn" name="isbn" value="<? echo $libdb->record[15]; ?>">
            	</div>
            	<div class="col-xs-3">
            		<input name="copies" type="text" class="form-control" id="copies" value="<?php echo $libdb->record[16]; ?>" readonly> 
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">800 10</label>
				<div class="col-xs-3">
		            <input class="form-control" name="issn" type="text" id="issn" value="<? echo $libdb->record[17]; ?>">
		        </div>
		        <div class="col-xs-3">
		            <input name="volume" type="text" class="form-control" id="volume" value="<? echo $libdb->record[18]; ?>">
		        </div>
		        <div class="col-xs-3">
		            <input name="no" type="text" class="form-control" id="no" value="<? echo $libdb->record[19]; ?>">
		        </div>
            </div>
            
            <div class="form-group">
			    <label class="col-sm-3 control-label">Abstract</label>
				<div class="col-xs-6">
            		<textarea class="form-control" name="summary" cols="60" rows="5" id="summary"><?php echo $libdb->record[14]; ?></textarea>
            	</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">Images</label>
				<div class="col-xs-3">
		            <input name="bookimg" type="file" id="bookimg">
		            <?php if(!empty($libdb->record[20])) { echo "<img class=\"img-thumbnail\" src=\"uploads/".$libdb->record[20]."\"> <span class=\"label label-info\">Current Image</span>"; } ?>
		        </div>
            </div>
            

            <div class="form-group">
			    <label class="col-sm-3 control-label">TOC</label>
				<div class="col-xs-3">
            		<input name="booktoc" type="file" id="booktoc">
            		<?php if(!empty($libdb->record[23])) { echo "<p class=\"form-control-static\"><span class=\"label label-info\">Current TOC</span> <a target=\"_blank\" href=\"toc/".$libdb->record[23]."\">".$libdb->record[23]."</a></p>"; } ?>
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