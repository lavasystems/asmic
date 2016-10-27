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
$libdb = new Modules_sql;
$kat = $_REQUEST["kat"];
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
                	<h2>Add New Book</h2>
					<div class="alert alert-info">Please fill up the form below to add new book</div>
		<?php
			if(isset($_POST['submit'])) {
						
				//check form
				if(!empty($_POST['isbn'])) {
					 $qry_str = "select count(*) book_isbn from library_books where book_isbn ='".$_POST['isbn']."'";
					 $libdb->query($qry_str);
					 $libdb->next_record();
					 if ($libdb->record[0]) {
					 $isbn = FALSE;
						echo "<div class=\"alert alert-danger\">ISBN No. is already exist</div>";
						echo "<a class=\"btn btn-default\" href=\"bookdetails.php?isbn=".$_POST['isbn']."\">Click Here</a> <span class=\"ar11_content\">to add more book unit under the same ISBN NO.</span>";
					 }
					 else{
						$isbn = TRUE;
						$isbn = $_POST['isbn'];
					 }
				}
				else {
				$isbn = FALSE;
				echo"<div class=\"alert alert-danger\">Please enter the ISBN No.</div>";
				}
				
				if(!empty($_POST['cat'])) {
					$cat = TRUE;
					$cat = $_POST['cat'];
				}
				else {
				$cat = FALSE;
				echo"<div class=\"alert alert-danger\">Please select the category</div>";
				}
				
				if(!empty($_POST['book_name'])) {
					$book_name = TRUE;
					$book_name = ereg_replace("'", "\'", $_POST["book_name"]);
				}
				else {
				$book_name = FALSE;
				echo"<div class=\"alert alert-danger\">Please enter the book title</div>";
				}

					//Book Image
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

				  	//Table of content
					require_once('fileupload-class.php');
					$my_toc = new uploader('en'); 
					// Set the max filesize of uploadable files in bytes
					$my_toc->max_filesize(2097152);
					// For ..//images, you can set the max pixel dimensions 
					//$my_toc->max_image_size(1024, 1024); // ($width, $height)
					// UPLOAD the file
					$my_toc->upload("booktoc", "", ".pdf");
					// MOVE THE FILE to its final destination
					//	$mode = 1 ::	overwrite existing file
					//	$mode = 2 ::	rename new file if a file
					//	           		with the same name already 
					//             		exists: file.txt becomes file_copy0.txt
					//	$mode = 3 ::	do nothing if a file with the
					//	           		same name already exists
					$mode = 1;
					$my_toc->save_file("toc/", $mode);
					// Check if everything worked
					if ($my_toc->error) {
						//echo $my_uploader->error . "<br>";
						echo "<div class=\"alert alert-danger\">".$my_toc->error."</div>";
						$my_toc->file['name'] = FALSE;
					} else {
						// Successful upload!
						if(stristr($my_toc->file['type'], "pdf")) {
						}
					}

				   
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
			  	  $summary = ereg_replace("'", "\'", $_REQUEST["summary"]);
				  $issn = $_REQUEST["issn"];
				  $vol = $_REQUEST["volume"];
				  $no = $_REQUEST["no"];
				  $copy = $_REQUEST["copies"];
				  $bookrack = $_REQUEST["bookrack"];
				  $dt_added = date("Y-m-d H:i:s");
				
			
				if($cat && $book_name && $isbn && empty($my_uploader->error) && empty($my_toc->error)) {
					  $qry_str = "insert into library_books (category_id, book_title, book_author, book_edition, book_publisher, book_year,";
					  $qry_str .="book_illustration, book_height, book_page, book_indexes, book_subject, book_editor, book_elt, book_summary,";
					  $qry_str .="book_isbn, book_copies, book_issn, book_volume, book_number, book_image,book_rack,book_toc,date_added) values";
					  $qry_str .=" ('".$cat."','".$book_name."','".$author."','".$edition."','".$publisher."','".$year."',";
					  $qry_str .="'".$ill."','".$height."','".$page."','".$index."','".$subject."','".$editor."','".$elt."','".$summary."',";
					  $qry_str .="'".$isbn."','".$copy."','".$issn."','".$vol."','".$no."','".$_FILES['bookimg']['name']."','".$bookrack."','".$my_toc->file['name']."','".$dt_added."')";
					 include_once("../classes/audit_trail.php");
					 $audit_trail = new audit_trail();
					 $audit_trail->writeLog($_SESSION['usr_username'], "library", "Add New Books ISBN : ".$isbn."");
					if($libdb->query($qry_str)) {
					$id_record = mysql_insert_id();
						if($copy > 0) {
							$c = 0;
							while($c<$copy) {
							$aplh = $c+1;
							
								$qry_unit = "insert into library_books_unit(book_recordid, accession_no, book_status, date_added) values";
								$qry_unit .="('".$id_record."', '".$isbn."-".$aplh."', 'y', '".$dt_added."')";
								$libdb->query($qry_unit);
								
							$c++;
							}
							echo'<meta http-equiv="refresh" content="0;URL=bookdetails.php?id='.$id_record.'">';
						}
						
						echo'<meta http-equiv="refresh" content="0;URL=bookdetails.php?id='.$id_record.'">';
					}
					else {
						echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
					}
				}
			//mysql_close();
										
			}
			?>
		<form action="<?php echo $PHP_SELF ?>" method="post" enctype="multipart/form-data" name="books" class="form-horizontal">
        	<div class="form-group">
				<label class="col-sm-3 control-label">Call No</label>
				<div class="col-xs-3">
        			<input class="form-control" name="bookrack" type="text" id="bookrack" value="<?php if (isset($_POST['bookrack'])) echo $_POST['bookrack']; ?>">
        		</div>
            </div>
            
            <div class="form-group">
				<label class="col-sm-3 control-label">Category</label>
				<div class="col-xs-6">
					<select class="form-control" id="cat" name="cat">
                        <option value="0">- Select a category-</option>
                        <?php
							$qry_str = "select id, category_id, category_name from library_category order by category_name ASC";
							$libdb->query($qry_str);
							while($libdb->next_record()) {
								if( $kat == "".$libdb->record[0]."" ){
									print( "<option value=\"".$libdb->record[1]."\" selected>".$libdb->record[2]."</option>" );
								}else{	
									echo "<option value=\"".$libdb->record[1]."\">".$libdb->record[2]."</option>";
								}
							}
						?>
					</select>
				</div>
            </div>

            <div class="form-group">
				<label class="col-sm-3 control-label">100 10</label>
				<div class="col-xs-6">
					<input class="form-control" type="text" id="author" name="author" maxlength="100" value="<?php if (isset($_POST['author'])) echo $_POST['author']; ?>">
				</div>
            </div>

            <div class="form-group">
				<label class="col-sm-3 control-label">245 10</label>
				<div class="col-xs-6">
					<input class="form-control" type="text" id="book_name" name="book_name" maxlength="255" value="<?php if (isset($_POST['book_name'])) echo $_POST['book_name']; ?>">
				</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">250 00</label>
				<div class="col-xs-3">
					<input class="form-control" name="edition" type="text" id="edition" maxlength="50" value="<?php if (isset($_POST['edition'])) echo $_POST['edition']; ?>">
				</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">260 00</label>
				<div class="col-xs-3">
					<input class="form-control" name="publisher" type="text" id="publisher" maxlength="100" value="<?php if (isset($_POST['publisher'])) echo $_POST['publisher']; ?>">
				</div>
            	<div class="col-xs-3">
					<input class="form-control" name="year" type="text" id="year" value="<?php if (isset($_POST['year'])) echo $_POST['year']; ?>">
				</div>
            </div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">300 00</label>
				<div class="col-xs-3">
					<input class="form-control" name="ill" type="text" id="ill" value="<?php if (isset($_POST['ill'])) echo $_POST['ill']; ?>">
				</div>
            	<div class="col-xs-3">
					<input class="form-control" name="height" type="text" id="height" value="<?php if (isset($_POST['height'])) echo $_POST['height']; ?>">
				</div>
            	<div class="col-xs-3">
					<input class="form-control" name="page" type="text" id="page" size="15" value="<?php if (isset($_POST['page'])) echo $_POST['page']; ?>">
				</div>
            </div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">500 00</label>
				<div class="col-xs-3">
					<select class="form-control" name="indexes" id="indexes" value="<?php if (isset($_POST['indexes'])) echo $_POST['indexes']; ?>">
						<option value="">-Book Index-</option>
						<option value="1">Yes</option>
						<option value="2">No</option>
					</select>
				</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">600 00</label>
				<div class="col-xs-3">
					<div class="input-group">
        				<span class="input-group-addon">1</span>
						<input class="form-control" name="sub1" type="text" id="sub1" value="<?php if (isset($_POST['sub1'])) echo $_POST['sub1']; ?>">
					</div>
				</div>
				<div class="col-xs-3">
					<div class="input-group">
        				<span class="input-group-addon">2</span>
						<input class="form-control" name="sub2" type="text" id="sub2" value="<?php if (isset($_POST['sub2'])) echo $_POST['sub2']; ?>">
					</div>
				</div>
				<div class="col-xs-3">
					<div class="input-group">
        				<span class="input-group-addon">3</span>
						<input class="form-control" name="sub3" type="text" id="sub3" value="<?php if (isset($_POST['sub3'])) echo $_POST['sub3']; ?>">
					</div>
				</div>
			</div>
			<div class="form-group">
			    <label class="col-sm-3 control-label">700 00</label>
				<div class="col-xs-3">
					<input class="form-control" name="editor" type="text" id="editor" value="<?php if (isset($_POST['editor'])) echo $_POST['editor']; ?>">
				</div>
			</div>


			<div class="form-group">
			    <label class="col-sm-3 control-label">700 10</label>
				<div class="col-xs-3">
					<input class="form-control" name="elt" type="text" id="elt" value="<?php if (isset($_POST['elt'])) echo $_POST['elt']; ?>">
				</div>
			</div>

			<div class="form-group">
			    <label class="col-sm-3 control-label">800 00</label>
				<div class="col-xs-3">
					<input class="form-control" type="text" id="isbn" name="isbn" maxlength="20" value="<?php if (isset($_POST['isbn'])) echo $_POST['isbn']; ?>">
				</div>
            	<div class="col-xs-3">
					<input name="copies" type="text" class="form-control" id="copies" value="1">
				</div>
			</div>

			<div class="form-group">
			    <label class="col-sm-3 control-label">800 10</label>
				<div class="col-xs-3">
					<input class="form-control" name="issn" type="text" id="issn" value="<?php if (isset($_POST['issn'])) echo $_POST['issn']; ?>">
				</div>
		        <div class="col-xs-3">
					<input name="volume" type="text" class="form-control" id="volume" value="<?php if (isset($_POST['volume'])) echo $_POST['volume']; ?>">
				</div>
		        <div class="col-xs-3">
					<input name="no" type="text" class="form-control" id="no" value="<?php if (isset($_POST['no'])) echo $_POST['no']; ?>">
				</div>
			</div>

			<div class="form-group">
			    <label class="col-sm-3 control-label">Abstract</label>
				<div class="col-xs-6">
					<textarea class="form-control" name="summary" cols="60" rows="5" id="summary"><?php if (isset($_POST['summary'])) echo $_POST['summary']; ?></textarea>
				</div>
            </div>

            <div class="form-group">
			    <label class="col-sm-3 control-label">Images</label>
				<div class="col-xs-3">
					<input name="bookimg" type="file" id="bookimg">
				</div>
			</div>

			<div class="form-group">
			    <label class="col-sm-3 control-label">TOC</label>
				<div class="col-xs-3">
					<input type="file" name="booktoc">
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
