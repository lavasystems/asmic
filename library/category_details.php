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
<h4>Category Details</h4>
		  <?php
		$libdb2 = new Modules_sql;
	    $qry_str2 = "select id,category_id,category_name,category_description from library_category where id=".$kat."";
	    $libdb2->query($qry_str2);
	    $libdb2->next_record();
	
			echo "<p>Category : ".$libdb2->record[2];
			echo "</p><p>Description : "; if(empty($libdb2->record[3])) { echo "-";} else { $libdb2->record[3];
				echo "</p>";
			}
	
	    $libdb = new Modules_sql;
	    $catid = $libdb2->record[1];
		
		
	    $qry_all = "select count(*) from library_books where category_id ='".$catid."'"; 
		$libdb->query($qry_all);
		$libdb->next_record();
		$total_rows = $libdb->record[0];
		
		$st = requestNumber($_REQUEST['st'], 0);
		$nh = requestNumber($_REQUEST['nh'], 10);
		$qry_str = "select library_books.book_recordid,library_books.category_id, library_books.book_title,library_books.book_isbn,";
		$qry_str .=" library_books.book_publisher,library_books.book_author,library_books.date_added,";
		$qry_str .=" library_books_unit.book_recordid, library_books_unit.book_status from library_books inner join library_books_unit";
		$qry_str .=" on library_books.book_recordid=library_books_unit.book_recordid";
		$qry_str .=" where library_books.category_id='".$catid."'";
		$qry_str .=" group by library_books.book_isbn ORDER BY library_books.book_title asc";
	    $qry_str .=" LIMIT ".$st.", ".$nh."";
		$libdb->query($qry_str);
		echo "<p>Unit of book records : ".$total_rows."</p>";
		
		?>
		<p><a class="btn btn-primary" href="booknew.php?kat=<? echo $libdb2->record[0]; ?>">Add New Book</a></p>
		<table class="table table-striped">
		  <tr> 
			  <td width="4%"><b>No</b></td>
			  <td width="30%"><b>BookTitle</b></td>
			  <td width="15%"><b>ISBN No.</b></td>
			  <td width="15%"><b>Publisher</b></td>
			  <td width="14%"><b>Author</b></td>
			  <td width="12%"><b>Date Added</b></td>
			  <td width="14%"><b>Action</b></td>
		  </tr>
		<?php
	  if ($libdb->num_rows() == 0) {
			echo "<tr><td class=\"m2_td_content\" colspan=\"7\">No Data Present</td></tr>";
	  }
	  else {
	  $i= $st + 1;	
		while($libdb->next_record()) {
		
		
					echo "<td>".$i."</td>";
					echo "<td><a href=\"bookdetails.php?id=".$libdb->record[0]."\">".$libdb->record[2]."</a></td>";
					echo "<td>".$libdb->record[3]."</td>";
					echo "<td>".$libdb->record[4]."</td>";
					echo "<td>".$libdb->record[5]."</td>";								
					echo "<td align=\"center\">".DateConvert($libdb->record[6], "j M Y")."</td>";
					echo "<td>";
						echo "<a href=\"bookupdate.php?isbn=".$libdb->record[3]."\" class=\"btn btn-default btn-xs\">Edit</a>";
						if($libdb->record[8]=='y'){
							echo "<a href=\"bookmodify.php?action=del_all&id=".$libdb->record[0]."\" onClick=\"return confirm('Are you sure want to delete the whole book?')\" class=\"btn btn-default btn-xs\">Delete</a>";
						}
					echo "</td>";
					echo "</tr>";
				$i++;	
			}
			
			$this_page = $_SERVER['PHP_SELF']."?kat=".$kat."";
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
		  <?
		  if ($total_rows > $nh) {
		  ?>
		  <table width="179"  border="0" align="left" cellpadding="0" cellspacing="1" class="m2_table_outline">
			<tr>
			  <td width="38" class="m2_td_content"><div align="right"><span class="fontcolorblue">&laquo;</span> <?=generateLink('First', $first_link)?></div></td>
			  <td width="60" class="m2_td_content"><div align="right"><span class="fontcolorblue">&lsaquo;</span> <?=generateLink('Previous', $prev_link)?></div></td>
			  <td width="40" class="m2_td_content"><div align="right"><?=generateLink('Next', $next_link)?> <span class="fontcolorblue">&rsaquo;</span></div></td>
			  <td width="42" class="m2_td_content"><div align="right"><?=generateLink('End', $last_link)?> <span class="fontcolorblue">&raquo;</span></div></td>
			</tr>
		</table>
		<?
		}
		?>
		  
		  <div class="alert alert-warning">* You cannot delete any book record if any unit of the books is still be issued</div></td>
        
</div>
    <!-- End Body Content -->

  <?php include '../inc/footer.php'; ?>
  
    <!-- End site footer -->
    <a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include '../inc/js.php'; ?>
</body>
</html>