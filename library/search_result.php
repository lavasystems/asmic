<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0); 
session_start();
include_once("local_config.php");

if(isset($_REQUEST["mode"])) $mode = $_REQUEST["mode"];
else $mode = "search";
include '../inc/pagehead.php';
include("class.php");
include_once($app_absolute_path."includes/functions.php");
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
                <h4>Search Book</h4>
                
                <?php if ($mode == "advance") {
					$libdb = new Modules_sql;
					$qry_str = "select category_id, category_name from library_category order by category_name asc;";
					$libdb->query($qry_str);
					$kat = $_POST['category'];
				?>

                <form name="form1" method="post" action="search_result.php?mode=advance">
                <dl class="dl-horizontal">
	            	<dt>Subject</dt>
	            	<dd><input name="subject" type="text" class="form-control" id="subject" value="<?php if (isset($_POST['subject'])) echo $_POST['subject']; ?>"></dd>
	            	<dt>Categories</dt>
	            	<dd><select class="form-control" name="category" id="select">
						<option value="">- Choose Category -</option>
						<?php
							while($libdb->next_record()) {
							if($kat == $libdb->record[0]){
								print( "<option value=\"".$libdb->record[0]."\" selected>".$libdb->record[1]."</option>\n" );
							}else{	
								echo "<option value=\"".$libdb->record[0]."\">".$libdb->record[1]."</option>";
							}
						}
						?>
					</select></dd>
					<dt>Book Code</dt>
					<dd><input name="book_code" type="text" class="form-control" id="book_code" value="<?php if (isset($_POST['book_code'])) echo $_POST['book_code']; ?>"></dd>
					<dt>ISBN No</dt>
					<dd><input name="isbn" type="text" class="form-control" id="isbn" value="<?php if (isset($_POST['isbn'])) echo $_POST['isbn']; ?>"></dd>
					<dt>Title</dt>
					<dd><input name="book_title" type="text" class="form-control" id="book_title" value="<?php if (isset($_POST['book_title'])) echo $_POST['book_title']; ?>"></dd>
					<dt>Author</dt>
					<dd><input name="author" type="text" class="form-control" id="author" value="<?php if (isset($_POST['author'])) echo $_POST['author']; ?>"></dd>
					<dt>Status</dt>
					<dd><select class="form-control" name="status" id="status">
						<option value="1" <? if($_POST['status']==1) echo "selected"; ?>>ALL</option>
						<option value="2" <? if($_POST['status']==2) echo "selected"; ?>>Available</option>
						<option value="3" <? if($_POST['status']==3) echo "selected"; ?>>Issued</option>
					</select></dd>
					<dt></dt>
					<dd>
						<input type="submit" name="submit" class="btn btn-primary">
						<a class="btn btn-default" href="search_result.php?mode=search">Simple Search</a> 
					</dd>
					</dl>
				</form>
            <?php
				$libdb_book = new Modules_sql;
				$libdb_adv = new Modules_sql;
				$book_code = ereg_replace("'", "\'", $_REQUEST['book_code']);
				$subject = ereg_replace("'", "\'", $_REQUEST['subject']);
				$category = ereg_replace("'", "\'", $_REQUEST['category']);
				$isbn = ereg_replace("'", "\'", $_REQUEST['isbn']);
				$book_title = ereg_replace("'", "\'", $_REQUEST['book_title']);
				$author = ereg_replace("'", "\'", $_REQUEST['author']);
				$status = ereg_replace("'", "\'", $_REQUEST['status']);		
				
				$qry_all = "select count(*) from library_books left join library_books_unit ";
				$qry_all .=" on library_books.book_recordid=library_books_unit.book_recordid where 1=1 ";
				$qry_where = "";
				if (!empty($subject)) {
					$qry_where .= "and library_books.book_subject='".$subject."'";
				}
				if (!empty($book_code)) {
					$qry_where .= "and library_books_unit.accession_no='".$book_code."'";
				}
				if (!empty($category)) {
					$qry_where .= " and library_books.category_id = '".$category."'";
				}
				if (!empty($isbn)) {
					$qry_where .= " and library_books.book_isbn = '".$isbn."'";
				}
				if (!empty($book_title)) {
					$qry_where .= " and library_books.book_title like '%".$book_title."%'";
				}
				if (!empty($author)) {
					$qry_where .= " and library_books.book_author like '%".$author."%'";
				}
				if ($status > 0) {
					if ($status == 2) {
					    $qry_where .=" and library_books_unit.book_status = 'y'";
					}
					else if ($status == 3) {
					    $qry_where .=" and library_books_unit.book_status = 'n'";
					}
				}
				/* check to see if any options were selected for searching */
				if (strlen($qry_where) != 0) {
				    $qry_all .= $qry_where." group by library_books.book_isbn";
				    }
				else { /* append a fake but true clause since we opened up the where condition initially */
				    $qry_all .= " and 2 = 2 group by library_books.book_isbn";
				    }
				$result = $libdb_adv->query($qry_all);
				$row = $libdb_adv->next_record();
				$total_rows = $libdb_adv->num_rows();
				//echo $qry_all;
				$st = requestNumber($_REQUEST['st'], 0);
				$nh = requestNumber($_REQUEST['nh'], 20);
				$page = ceil($total_rows/$nh);
				
				$qry_str = "select * from library_books left join library_books_unit ";
				$qry_str .=" on library_books.book_recordid=library_books_unit.book_recordid where 1=1 ";
				$qry_where = "";
				if (!empty($subject)) {
					$qry_where .= "and library_books.book_subject = '".$subject."'";
				}
				if (!empty($book_code)) {
					$qry_where .= "and library_books_unit.accession_no = '".$book_code."'";
				}
				if (!empty($category)) {
					$qry_where .= " and library_books.category_id = '".$category."'";
				}
				if (!empty($isbn)) {
					$qry_where .= " and library_books.book_isbn = '".$isbn."'";
				}
				if (!empty($book_title)) {
					$qry_where .= " and library_books.book_title like '%".$book_title."%'";
				}
				if (!empty($author)) {
					$qry_where .= " and library_books.book_author like '%".$author."%'";
				}
				if ($status > 0) {
					if ($status == 2){
					    $qry_where .=" and library_books_unit.book_status = 'y'";
					}
					else if ($status == 3) {
					    $qry_where .=" and library_books_unit.book_status = 'n'";
					}
				}
				/* check to see if any options were selected for searching */
				if (strlen($qry_where) != 0) {
				    $qry_str .= $qry_where. " group by library_books.book_isbn order by library_books.book_recordid asc LIMIT ".$st.", ".$nh.";";
				    }
				else { /* append a fake but true clause since we opened up the where condition initially */
				    $qry_str .= " group by library_books.book_isbn order by library_books.book_recordid asc LIMIT ".$st.", ".$nh."";
				    }

				$libdb_book->query($qry_str);
				echo "<h4>SEARCH RESULT</h4>";
				echo "<table class=\"table table-striped\">";
				echo "<td><b>No.</b></td>";
				echo "<td><b>Category</b></td>";		
				echo "<td><b>Book Title</b></td>";
				echo "<td><b>Book ISBN</b></td>";
				echo "<td><b>Book Author</b></td>";
				if ($libdb_book->num_rows() == 0) {
				    echo "<tr><td colspan=\"6\">Empty search result</td></tr>";
				    }
				else {
				$i = $st + 1;
				    while($libdb_book->next_record()) {
					 
					 $libdb_category = new Modules_sql;
					 $qry_str = "select category_id, category_name from library_category";
					 $libdb_category->query($qry_str);
					 while($libdb_category->next_record()) {
					 	if($libdb_book->record[1] == $libdb_category->record[0]) {
							$kat = $libdb_category->record[1];
						}
					 }
						echo "<tr>";			
						echo "<td align=\"center\">".$i."</td>";
						echo "<td>".$kat."</td>";				
						echo "<td>";
						if (isAllowed(array(501), $_SESSION['permissions'])){
							echo "<a href=\"bookdetails.php?id=".$libdb_book->record[0]."\">".$libdb_book->record[2]."</a></td>";
						}
						if (isAllowed(array(502, 503), $_SESSION['permissions'])){
							echo "<a href=\"pubbookdetails.php?id=".$libdb_book->record[0]."\">".$libdb_book->record[2]."</a></td>";
						}						
						echo "<td>".$libdb_book->record[15]."</td>";
						echo "<td>".$libdb_book->record[3]."</td>";
						echo "</tr>";
						$i++;
						}
						
						$this_page = $_SERVER['PHP_SELF']."?mode=advance";
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
				echo "</table>";
			?>
            <?php if ($total_rows > $nh) {
            	echo "<p>Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page."</p>";
			?>
				<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>
            <?php }
			}
		
			if ($mode == "search") { ?>
            <form name="form1" method="post" action="search_result.php?mode=search">
            <dl class="dl-horizontal">
            	<dt>Search</dt>
            	<dd><input class="form-control" name="keyword" type="text" id="keyword" value="<?php if (isset($_POST['keyword'])) echo $_POST['keyword']; ?>"></dd>
            	<dt></dt>
            	<dd>
            		<input type="submit" name="submit" class="btn btn-primary">
            		<a class="btn btn-default" href="search_result.php?mode=advance">Advanced Search</a>
            	</dd>
            </form>
            <!-- simple search result -->
            <?php
				$libdb_qry = new Modules_sql;
				$libdb = new Modules_sql;
				$libdb_cat = new Modules_sql;
				$keyword = preg_replace("/'/", "'", $_REQUEST['keyword']);
				
				$qry_all = "select count(*) from library_books inner join library_books_unit on";
				$qry_all .=" library_books.book_recordid=library_books_unit.book_recordid where 1=1";
				$qry_where = "";
					if (!empty($keyword)) {
						$qry_where =" and library_books.book_title like '%".$keyword."%'";
						$qry_where .=" or library_books.book_subject = '".$keyword."'";
						$qry_where .=" or library_books.book_isbn = '".$keyword."'";
						$qry_where .=" or library_books_unit.accession_no like '%".$keyword."%'";
						$qry_where .=" or library_books.book_summary like '%".$keyword."%'";
						$qry_where .=" or library_books.book_elt like '%".$keyword."%'";	
						$qry_where .=" or library_books.book_author like '%".$keyword."%'";
					}
					if (strlen($qry_where) != 0) {
					    $qry_all .= $qry_where." group by library_books_unit.book_recordid";
					}
					else { 
					/* append a fake but true clause since we opened up the where condition initially */
					    $qry_all .= " group by library_books_unit.book_recordid";
					}		
				$result = $libdb->query($qry_all);
				$row = $libdb->next_record();
				$total_rows = $libdb->num_rows();
				if(isset($_REQUEST['st'])){}
				$st = requestNumber($_REQUEST['st'], 0);
				$nh = requestNumber($_REQUEST['nh'], 20);
				$page = ceil($total_rows/$nh);
				
					$qry_str = "select * from library_books inner join library_books_unit on";
					$qry_str .=" library_books.book_recordid=library_books_unit.book_recordid where 1=1";
					$qry_where ="";
					if (!empty($keyword)) {
						$qry_cat = "select category_id from library_category where category_name like '%".$keyword."%'";
						$libdb_cat->query($qry_cat);
						$libdb_cat->next_record();
						$cat = $libdb_cat->record[0];
						
						$qry_where =" and library_books.book_title like '%".$keyword."%'";
						$qry_where .=" or library_books.book_subject = '".$keyword."'";
						$qry_where .=" or library_books.book_isbn = '".$keyword."'";
						$qry_where .=" or library_books_unit.accession_no like '%".$keyword."%'";
						$qry_where .=" or library_books.book_summary like '%".$keyword."%'";
						$qry_where .=" or library_books.category_id ='".$cat."'";			
						$qry_where .=" or library_books.book_author like '%".$keyword."%'";
					}
					if (strlen($qry_where) != 0) {
					    $qry_str .= $qry_where." group by library_books_unit.book_recordid order by library_books.book_title asc LIMIT ".$st.", ".$nh."";
					    }
					else { 
					/* append a fake but true clause since we opened up the where condition initially */
					    $qry_str .= " group by library_books_unit.book_recordid order by library_books.book_title asc LIMIT ".$st.", ".$nh."";
					    }
					$libdb_qry->query($qry_str);

					echo "<h4>SEARCH RESULT</h4>";
					echo "<table class=\"table table-striped\">";
					echo "<tr>";
					echo "<td><b>No.</b></td>";
					echo "<td><b>Category</b></td>";		
					echo "<td><b>Book Title</b></td>";
					echo "<td><b>Book Summary</b></td>";
					echo "<td><b>Book ISBN</b></td>";
					echo "<td><b>Book Author</b></td>";
					echo "</tr>";
					if ($libdb_qry->num_rows() == 0) {
					    echo "<tr><td colspan=\"6\">Empty search result</td></tr>";
					    }
					else {
					$i = $st + 1;
					    while($libdb_qry->next_record()) {
						 $libdb_category = new Modules_sql;
						 $qry_str = "select category_id, category_name from library_category";
						 $libdb_category->query($qry_str);
						 while($libdb_category->next_record()) {
						 	if($libdb_qry->record[1] == $libdb_category->record[0]) {
								$kat = $libdb_category->record[1];
							}
						 }
						
							echo "<tr>";
							echo "<td align=\"center\">".$i."</td>";
							echo "<td>".$kat."</td>";				
							echo "<td>";
							if (isAllowed(array(501), $_SESSION['permissions'])){
								echo "<a href=\"bookdetails.php?id=".$libdb_qry->record[0]."\">".$libdb_qry->record[2]."</a></td>";
							}
							if (isAllowed(array(502,503), $_SESSION['permissions'])){
								echo "<a href=\"pubbookdetails.php?id=".$libdb_qry->record[0]."\">".$libdb_qry->record[2]."</a></td>";
							}
							$summary = $libdb_qry->record[14];
							$max=150; 
							$modstr=((strlen($summary)>$max)?substr($summary,0,$max) . "..." : $summary); 
							echo "<td width=\"30%\">".$modstr."</td>";			
							echo "<td>".$libdb_qry->record[15]."</td>";
							echo "<td>".$libdb_qry->record[3]."</td>";
							echo "</tr>";
							$i++;
						}
							$this_page = $_SERVER['PHP_SELF']."?mode=search";
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
					echo "</table>";
				?>
				<?php if ($total_rows > $nh) { 
			  		echo "<p>Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page."</p>";
			  	?>
			  	<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
				<?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>
            	<? }
			} ?>
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
