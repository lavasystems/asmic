<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
session_start();
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501,201), $_SESSION['permissions'])){
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
                <h2>Search Members</h2>
            <?php
			switch ($_GET['action']){
			case "detail" :
				member_details($id);
			break;
			case "list" :
				member_list();
			break;
			case "search" :
				result_search();
			break;
			default :
				member_search();
			break;
			}
			?>
          
<?php
function member_details () {
	global $app_absolute_path,$root_images_folder;
	$id = $_REQUEST['id'];
	$libdb = new Modules_sql;
	
	$qry_member = "SELECT contact_contact.id, contact_contact.icnum, contact_contact.fullname, contact_address.id, contact_address.line1,";
	$qry_member .=" contact_address.phone1, library_member.*, contact_contact.delflag";
	$qry_member .=" FROM ((contact_contact left JOIN contact_address ON contact_address.id=contact_contact.id)";
	$qry_member .=" left JOIN library_member ON library_member.contact_id=contact_contact.id)";
	$qry_member .=" WHERE library_member.contact_id =".$id." and contact_contact.delflag=0";
	$libdb->query($qry_member);
	$libdb->next_record();

?>
<h3>Member Details</h3>
<dl class="dl-horizontal">
	<dt>Member IC</dt>
	<dd><? echo $libdb->record[1]; ?></dd>
	<dt>Member Name</dt>
	<dd><? echo $libdb->record[2]; ?></dd>
	<dt>Member address</dt>
	<dd><? echo $libdb->record[4]; ?></dd>
	<dt>Contact Number</dt>
	<dd><? echo $libdb->record[5]; ?></dd>
	<dt>Join Date</dt>
	<dd><? echo DateConvert($libdb->record[9], "j M Y"); ?></dd>
	<dt>Available Cards</dt>
	<dd><? echo $libdb->record[8]; ?> Card(s) (<i>* Cards left to issue books</i>)</dd>
</dl>
<h3>List of books issued for this person</h3>
		  <?php
		    $lib_book = new Modules_sql;
		  	$qry_all = "select count(*) from library_issue where contact_id ='".$id."' and approve='yes';";
			$result = $lib_book->query($qry_all);
			//$row = $libdb->fetch_row($result);
			$row = $lib_book->next_record();
			$total_rows = $lib_book->record[0];
			
			$st = requestNumber($_REQUEST['st'], 0);
			$nh = requestNumber($_REQUEST['nh'], 10);
			$page = ceil($total_rows/$nh);
			
			$qry_book = "SELECT library_books.book_recordid, library_books.book_title, library_books.book_isbn,";
			$qry_book .=" library_books_unit.book_recordid, library_books_unit.accession_no, library_books_unit.book_status, library_issue . * from";
			$qry_book .=" ((library_issue left join library_books_unit on";
			$qry_book .=" library_books_unit.accession_no=library_issue.accession_no) left join";
			$qry_book .=" library_books on library_books.book_recordid=library_books_unit.book_recordid)";
			$qry_book .=" where library_issue.contact_id = '".$id."' and approve='yes'";
			$qry_book .=" order by library_issue.date_added desc LIMIT ".$st.", ".$nh.";";
			$lib_book->query($qry_book);
			
		  ?>
		  <table class="table table-striped">
              <tr> 
                <td width="5%">No.</td>
                <td width="13%">Book Code</td>
                <td width="40%">Book Title</td>
                <td width="23%">Date Issue</td>
                <td width="19%">Status</td>
              </tr>
			  <?php
			  if ($lib_book->num_rows() == 0) {
					echo "<tr><td colspan=\"5\">Empty Record</td></tr>";
					}
			  else {
			  $i = $st + 1;
					while($lib_book->next_record()) {
						if(($lib_book->record[15] != '0000-00-00') && ($lib_book->record[14] < date("Y-m-d"))) {
							$status = "<span class=\"label label-danger\">Overdue</span>";
						}
						else if(($lib_book->record[15] == '0000-00-00') && ($lib_book->record[14] < date("Y-m-d"))) {
							$status = "<span color=\"label label-warning\">Overdue and Haven't Returned</span>";
						}
						else if(($lib_book->record[15] == '0000-00-00') && ($lib_book->record[14] > date("Y-m-d"))) {
							$status = "<span color=\"label label-info\">Still Issued</span>";
						}
						else {
							$status = "<span color=\"label label-success\">Returned</span>";
						}
			  ?>
              <tr> 
                <td><?php echo $i; ?></td>
                <td><?php echo $lib_book->record[7]; ?></td>
                <td><?php echo $lib_book->record[12]; ?></td>
                <td><?php echo DateConvert($lib_book->record[13], "j M Y"); ?></td>
                <td><?php echo $status ?></td>
              </tr>
			  <?php
			  $i++;
			  }
				
						$this_page = $_SERVER['PHP_SELF']."?action=detail&id=".$id."";
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
			
		  <?php
		  if ($total_rows > $nh) {
		  ?>

				<table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
				<tr> 
				  <td width="200" class="m2_td_content">
				  <div align="right">
				  <?
				  echo "Total Books:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
				  ?>
				  </div></td>
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
		  
		  <?php
			}
		  ?>

		  <a class="btn btn-default" href="lib_members.php">Back</a>
		  
<?php
}

function member_search() {
global $app_absolute_path, $root_images_folder;
?>
	<form name="search_member" method="post" action="lib_members.php?action=search">
        Member IC
        <input name="icno" type="text" id="icno" class="form-control">
        Member Name
        <input name="membername" type="text" id="membername" class="form-control">
        <input type="submit" name="submit" value="Search" class="btn btn-primary">
        <a class="btn btn-default" href="search_books.php?page=search">Search Books</a>
    </form>
<?
}
function result_search() {
	global $app_absolute_path, $root_images_folder;
	
?>
	
	<form name="search_member" method="post" action="lib_members.php?action=search">
        Member IC
        <input name="icno" type="text" id="icno" class="form-control">
        Member Name
        <input name="membername" type="text" id="membername" class="form-control">
        <input type="submit" name="submit" value="Search" class="btn btn-primary">
        <a class="btn btn-default" href="search_books.php?page=search">Search Books</a>
    </form>
<?

		$membername =  ereg_replace("'", "\'", $_REQUEST['membername']);
		$icno =  ereg_replace("'", "\'",$_REQUEST['icno']);
		
		$libdb = new Modules_sql;
		$libdb_mem = new Modules_sql;
		$qry_all = "select count(*) from library_member left join contact_contact";
		$qry_all .=" on contact_contact.id=library_member.contact_id where 1=1 and contact_contact.delflag=0";
		$qry_where = "";
		if (!empty($membername)) { $qry_where = " and contact_contact.fullname like '%".$membername."%'"; }
		if (!empty($icno)) { $qry_where = " and contact_contact.icnum = '".$icno."'"; }
		/* check to see if any options were selected for searching */
		if (strlen($qry_where) != 0) { $qry_all .= $qry_where.""; }
		/* append a fake but true clause since we opened up the where condition initially */
		else { $qry_all .= " and 2 = 2"; }
			
		$result = $libdb->query($qry_all);
		$row = $libdb->next_record();
		$total_rows = $libdb->record[0];
		
		$st = requestNumber($_REQUEST['st'], 0);
		$nh = requestNumber($_REQUEST['nh'], 10);
		$page = ceil($total_rows/$nh);
			
			$qry_str = "select library_member.*, contact_contact.* from library_member left join contact_contact";
			$qry_str .=" on contact_contact.id=library_member.contact_id where 1=1 and contact_contact.delflag=0";
			$qry_where = "";
			if (!empty($membername)) { $qry_where = " and contact_contact.fullname like '%".$membername."%'"; }
			if (!empty($icno)) { $qry_where = " and contact_contact.icnum = '".$icno."'"; }
			/* check to see if any options were selected for searching */
			if (strlen($qry_where) != 0) { $qry_str .= $qry_where." LIMIT ".$st.", ".$nh.""; }
			/* append a fake but true clause since we opened up the where condition initially */
			else { $qry_str .= " and 2 = 2 LIMIT ".$st.", ".$nh.""; }
			$libdb_mem->query($qry_str);	
			
			echo "<h2>Search Results</h2>";
			echo "<table class=\"table table-striped\">";
			echo "<tr>";
			echo "<td width=\"5%\"><b>No.</b></td>";
			echo "<td width=\"12%\"><b>Title</b></td>";
			echo "<td width=\"20%\"><b>IC Number</b></td>";
			echo "<td width=\"30%\"><b>Member Name</b></td>";
			echo "<td width=\"10%\"><b>Join Date</b></td>";						
			echo "<td width=\"10%\"><b>Available Cards</b></td>";
			echo "</tr>";
			if ($libdb_mem->num_rows() == 0) {
		    	echo "<tr><td colspan=\"7\">Empty search result</td></tr>";
			}
			else {
			$i = $st + 1;
				while($libdb_mem->next_record()) {
				
					echo "<tr>";
					echo "<td>".$i."</td>";
					echo "<td>".$libdb_mem->record[8]."</td>";
					echo "<td>".$libdb_mem->record[7]."</td>";
					echo "<td align=\"left\"><a href=\"lib_members.php?action=detail&id=".$libdb_mem->record[1]."\">".$libdb_mem->record[6]."</a></td>";
					echo "<td align=\"left\">".DateConvert($libdb_mem->record[3], "d M Y")."</td>";							
					echo "<td>".$libdb_mem->record[2]."</td>";
					echo "</tr>";
					$i++;
				}
				$this_page = $_SERVER['PHP_SELF']."?action=search";
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

	  <?php
	  if ($total_rows > $nh) {
	  ?>
			<table>
				<tr> 
				  <td width="200" class="m2_td_content">
				  <div align="right">
				  <?php
				  echo "Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
				  ?>
				  </div></td>
				  <td><div align="right"><span class="fontcolorblue">&laquo;</span> 
					  <?=generateLink('First', $first_link)?>
					</div></td>
				  <td><div align="right"><span class="fontcolorblue">&lsaquo;</span> 
					  <?=generateLink('Previous', $prev_link)?>
					</div></td>
				  <td><div align="right"> 
					  <?=generateLink('Next', $next_link)?>
					  <span class="fontcolorblue">&rsaquo;</span></div></td>
				  <td><div align="right"> 
					  <?=generateLink('End', $last_link)?>
					  <span class="fontcolorblue">&raquo;</span></div></td>
				</tr>
			  </table>
	  <?
		}
	  ?>
<?		
}
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