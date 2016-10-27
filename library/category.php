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
      <p></p>
		<?php
			switch ($_GET['action']) {
				case "catlist" :
					list_category();
				break;
				case "update" :
					update_category($kat);
				break;
				case "delete" :
					del_category($kat);
				break;
				case "catnew" :
					new_category();
				break;
				case "cfm_reassigned" :
					succes_reassign();
				break;
				case "cfm_delete" :
					confirm_delete();
				break;
				case "cfm_update" :
					confirm_update();
				break;
				case "cfm_add" :
					confirm_add();
				break;
				case "error" :
					error_cat();
				break;
				default :
					cat_list();
				break;
			}
			?>
<?php
function cat_list() {
	echo "<h4>Category</h4>";
	
global $app_absolute_path, $root_images_folder;

		$libdb = new Modules_sql;
		$qry_all = "select count(*) from library_category";
		$result = $libdb->query($qry_all);
		$row = $libdb->next_record();
		$total_rows = $libdb->record[0];
		
		$st = requestNumber($_REQUEST['st'], 0);
		// changed by azrad on Jun 05, 06
		// default value: 10
		$nh = requestNumber($_REQUEST['nh'], 20);
		$page = ceil($total_rows/$nh);
		$qry_str = "select * from library_category order by category_name asc LIMIT ".$st.", ".$nh."";
		$libdb->query($qry_str);
	?>
	<a class="btn btn-default" href="category.php?action=catnew">Add New Category</a>
	<div class="alert alert-info">You have to enter into the category before adding any new book(s).</div>
	<table class="table table-striped">
	  <tr> 
		<td width="10%"><b>Category ID</b></td>
		<td width="25%"><b>Category</b></td>
		<td width="35%"><b>Description</b></td>
		<td width="10%" align="center"><b>Action</b></td>
	  </tr>
  <?php
  if ($libdb->num_rows() == 0) {
  		echo "<tr><td colspan=\"4\">No Data Present</td></tr>";
  }
  else {
 	while($libdb->next_record()) {
		echo "<tr>";
		echo "<td>".$libdb->record[1]."</td>";
		echo "<td>";
		if($libdb->record[1] == 'UN') { 
			echo "<a href=\"category.php?action=cfm_delete\">".$libdb->record[2]."</a>";
		}
		else {
			echo"<a href=\"category_details.php?kat=".$libdb->record[0]."\">".$libdb->record[2]."</a>";
		}
		echo "</td>";
		echo "<td>"; if(empty($libdb->record[3])) { echo "-"; } else { echo $libdb->record[3]; } 
		echo "</td>";
		echo "<td align=\"center\">";
			if($libdb->record[1] == 'UN') {
				echo "";
			}
			else {
			
		       echo "<a href=\"category.php?action=update&kat=".$libdb->record[0]."\">Edit</a>";
			   echo "&nbsp;&nbsp;&nbsp;";			   
			   echo "<a href=\"category.php?action=delete&kat=".$libdb->record[0]."\"><Delete</a>";			   
			 }
		echo"</td>";
		echo "</tr>";
		}
		
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
	}
	?>
	<?php
	  if ($total_rows > $nh) {
	  	echo "Total Categories:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
	  </td>
	  </tr>
	  <?php } ?>
	  <div class="alert alert-warning">* Category "Unassigned Category" cannot be deleted.</div>
	<?
}
/* -- > START ADD NEW CATEGORY < -- */
function new_category() {
global $app_absolute_path,$root_images_folder;
$libdb = new Modules_sql;
?>
<h4>Add New Category</h4>
<div class="alert alert-info">Please fill in the fields below.</div>
      <?php
			if(isset($_POST['submit'])) {
				//check form
				if(!empty($_POST['cat_name'])) {
					 $ct_check = ereg_replace("'", "\'", $_POST['cat_name']);
					 $qry_str = "select count(*) category_name from library_category where category_name ='".$ct_check."'";
					 $libdb->query($qry_str);
					 $libdb->next_record();
			
					 if (!ereg("^[a-zA-Z0-9._-]", $_POST['cat_name'])) {
					 	$cat_name = FALSE;
						echo "<div class=\"alert alert-danger\">The category name you entered is not valid.</div>";	
					 }
					 elseif ($libdb->record[0]) {	
						$cat_name = FALSE;
						echo "<div class=\"alert alert-danger\">The category name you entered, has already exist. Please enter other category name </div>";
					 }
					 else{
						$cat_name = TRUE;
						$cat_name = ereg_replace("'", "\'", $_POST['cat_name']);
					 }
				}
				else {
					$cat_name = FALSE;
					echo"<div class=\"alert alert-danger\">Please enter category name</div>";
				}
				
				if(!empty($_POST['cat_id'])) {
					 $ctid_check = ereg_replace("'", "\'", $_POST['cat_id']);
					 $qry_str = "select count(*) category_id from library_category where category_id ='".$ctid_check."'";
					 $libdb->query($qry_str);
					 $libdb->next_record();
			
					 if (!ereg("^[a-zA-Z0-9._-]", $_POST['cat_id'])) {
					 	$cat_id = FALSE;
						echo "<div class=\"alert alert-danger\">The category ID you entered is not valid.</div>";	
					 }
					 elseif ($libdb->record[0]) {	
						$cat_id = FALSE;
						echo "<div class=\"alert alert-danger\">The category ID you entered, has already exist. Please enter other category ID </div>";
					 }
					 else{
						$cat_id = TRUE;
						$cat_id = ereg_replace("'", "\'", $_POST['cat_id']);
					 }
				}
				else {
					$cat_id = FALSE;
					echo"<div class=\"alert alert-danger\">Please enter category ID</div>";
				}
				
				$cat_description = ereg_replace("'", "\'", $_REQUEST["cat_description"]);
				$today = date("Y-m-d H:i:s");
				
  				if($cat_name && $cat_id) {
					$qry_str = "insert into library_category values ('NULL','".$cat_id."','".$cat_name."','".$cat_description."','".$today."');";
					if($libdb->query($qry_str)) {
						  include_once("../classes/audit_trail.php");
						  $audit_trail = new audit_trail();
						  $audit_trail->writeLog($_SESSION['usr_username'], "library", "Add New Category : ".$cat_name."");
						echo '<meta http-equiv="refresh" content="0;URL=category.php?action=cfm_add">';
					}
					else {
							echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
					}
				}
			}							
			?>
    <form name="category" method="post" action="<?php echo $PHP_SELF ?>">
    <dl class="dl-horizontal">
    	<dt>Category ID<font color="red">*</font></dt>
        <dd><input name="cat_id" type="text" class="form-control" id="cat_id" value="<?php if (isset($_POST['cat_id'])) echo $_POST['cat_id']; ?>"></dd>

        <dt>Category Name<font color="red">*</font></dt>
        <dd><input class="form-control" name="cat_name" type="text" id="cat_name" size="60" value="<?php if (isset($_POST['cat_name'])) echo $_POST['cat_name']; ?>"></dd>

        <dt>Category Description</dt>
        <dd>
        	<textarea class="form-control" name="cat_description" cols="50" rows="6" id="cat_description"><?php if (isset($_POST['cat_description'])) echo $_POST['cat_description']; ?></textarea>
        </dd>
       	</dl>
            <div class="alert alert-info">Field with (<font color="red">*</font>) is compulsory </div>
            <input type="submit" name="submit" value="Submit" class="btn btn-primary">
            <a href="category.php" class="btn btn-default">Cancel</a> 
        </form>
        <?
}
/* -- > END ADD NEW CATEGORY < -- */
/* -- > START UPDATE CATEGORY < -- */
function update_category($kat) {
	global $app_absolute_path,$root_images_folder;
    $libdb = new Modules_sql;
?>
<h4>Update Category</h4>
<div class="alert alert-info">Please fill in the fields below.</div>
	<?php
	$kat = $_REQUEST["kat"];
	if(isset($_POST['submit'])) {
	   
		//check form
		if(!empty($_POST['cat_name'])) {
			 $ct_check = ereg_replace("'", "\'", $_POST['cat_name']);
			 $qry_str = "select count(*) category_name from library_category where category_name ='".$ct_check."' and id!='".$kat."'";
			 $libdb->query($qry_str);
			 $libdb->next_record();
			 //if (!ereg("^[a-zA-Z0-9._-]", $_POST['cat_name'])) 
			 if (strspn($_POST['cat_name'],"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'-_. ") != strlen($_POST['cat_name']))
			 {
				$cat_name = FALSE;
				echo "<div class=\"alert alert-danger\">The category name you entered is not valid.</div>";	
			 }
			 elseif ($libdb->record[0]) 
			 {	
				$cat_name = FALSE;
				echo "<div class=\"alert alert-danger\">The category name you entered, has already exist. Please enter other category name </div>";
			 }
			 else
			 {
				$cat_name = TRUE;
				$cat_name = ereg_replace("'", "\'", $_POST['cat_name']);
			 }
		}
		else 
		{
			$cat_name = FALSE;
			echo"<div class=\"alert alert-danger\">Please enter category name</div>";
		}
		
		if(!empty($_POST['cat_id'])) {
			$cat_id = TRUE;
			$cat_id = $_POST['cat_id'];
		}
		else {
			$cat_id = FALSE;
			echo"<span class=\"alert alert-danger\">Please enter category ID</div>";
		}
		
		$cat_description = ereg_replace("'", "\'", $_REQUEST["cat_description"]);
		$today = date("Y-m-d H:i:s");
		
		if($cat_name && $cat_id) {
			  $qry_str = "UPDATE library_category SET category_id ='".$cat_id."', category_name='".$cat_name."', category_description='".$cat_description."', date_added = '".$today."' WHERE id ='".$kat."';";
			  if($libdb->query($qry_str)) {
				  include_once("../classes/audit_trail.php");
				  $audit_trail = new audit_trail();
				  $audit_trail->writeLog($_SESSION['usr_username'], "library", "Update Category Books : ".$cat_name."");
				  echo'<meta http-equiv="refresh" content="0;URL=category.php?action=cfm_update&kat='.$kat.'">';
			   }
			  else {
		       	  echo"<div class=\"alert alert-danger\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</div>";
			  }
		}
	}
	 $catid = $_REQUEST['kat'];
	 $qry_str = "select * from library_category where id='".$catid."'";
	 $libdb->query($qry_str);
	 $libdb->next_record();							
	?>
	<form name="category" method="post" action="category.php?action=update&amp;kat=<?php echo $kat; ?>">
	<dl class="dl-horizontal">
        <dt>Category ID<font color="red">*</font></dt>
        <dd><input name="cat_id" type="text" class="form-control" id="cat_id" value="<?php echo $libdb->record[1]; ?>"></dd>

        <dt>Category Name<font color="red">*</font></dt>
        <dd><input class="form-control" name="cat_name" type="text" id="cat_name" value="<?php echo $libdb->record[2]; ?>"></dd>

        <dt>Category Description</dt>
        <dd><textarea class="form-control" name="cat_description" cols="50" rows="6" id="cat_description"><?php echo $libdb->record[3]; ?></textarea></dd>
    </dl>
        <div class="alert alert-info">Field with (<font color="red">*</font>) is compulsory </div>
        	<input type="submit" name="submit" value="Submit" class="btn btn-primary">
            <a href="category.php" class="btn btn-default">Cancel</a> 
    </form>
<?php
}
/* --> END UPDATE CATEGORY < -- */

function del_category($kat) {
	global $app_absolute_path,$root_images_folder;
	$libdb = new Modules_sql;
	$cat_id = $_REQUEST['kat'];
	$qry = "select * from library_category where id = ".$cat_id."";
	$libdb->query($qry);
	$libdb->next_record();
?>
	<h4>Delete Category</h4>
    <div class="alert alert-warning">Are you sure you want to delete category as below ?</div>
		<table class="table">
        	<tr>
				<td>
				<?php
				echo "Category Name : ".$libdb->record[2]."<br>";
				echo "Category Description : ".$libdb->record[3]."<br>";
				?>
				</td>
			</tr>
		</table>
		<form name="form1" method="post" action="functions.php?cat=delete&catid=<?php echo $_REQUEST['kat']; ?>">
			<input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_delete.gif">
			<input type="submit" name="submit" value="Delete" class="btn btn-danger">
		</form>
<?php
}
function confirm_update() {
global $app_absolute_path,$root_images_folder;
$libdb = new Modules_sql;
$qry_cat = "select * from library_category where id = ".$_REQUEST['kat']."";
$libdb->query($qry_cat);

$libdb->next_record();
?>
		<h4>Update Category</h4>
		<div class="alert alert-success">Category <?php echo $libdb->record[2]; ?> has been successfully updated</div>
		<a class="btn btn-default" href="category.php">Back</a>
<?php
}

function confirm_delete() {
echo "<h4>Category</h4>";
$libdb = new Modules_sql;
global $app_absolute_path,$root_images_folder;
if (isset($_POST['submit']))
{
		if(!empty($_POST['category'])) {
			$newcat = TRUE;
			$newcat = $_POST['category'];
		}
		else {
		$newcat = FALSE;
		echo"<div class=\"alert alert-danger\">Please choose category</div>";
		}
		
		if(!empty($_POST['id'])) {
			$id = TRUE;
			$id = $_POST['id'];
		}
		else {
		$id = FALSE;
		echo"<div class=\"alert alert-danger\">Please check the checkbox of the book which need to re-assign to new category</div>";
		}
		
		$cat = $_REQUEST['category'];
		if($newcat && $id) {
        foreach($_POST['id'] as $id)
        {
            $qry = "Update library_books set category_id='".$newcat."' where book_recordid ='".$id."'";
			$libdb->query($qry);
				$qry = "select category_name from library_category where category_id='".$newcat."'";
				$libdb->query($qry);
				$libdb->next_record();
				$catname = $libdb->record[0];
			  include_once("../classes/audit_trail.php");
			  $audit_trail = new audit_trail();
			  $audit_trail->writeLog($_SESSION['usr_username'], "library", "Delete Category : ".$catname."");
        }
		}
    }


?>

	<?php
	$qry_all = "select count(*) from library_books where category_id = 'UN' or category_id=''";
	$libdb->query($qry_all);
	$libdb->next_record();
	if ($libdb->record[0]) {
	?>
	<form name="form1" method="post" action="<?php echo $PHP_SELF ?>?action=cfm_delete">
			<?php
			if(!empty($_SERVER['QUERY_STRING']) && eregi('del=0', $_SERVER['QUERY_STRING'])) {
				echo "<b>The category has been succesfully deleted</b><br><br>";
			}
			?>
			<div class="alert alert-info">Below are the list of book(s) under category which you have been deleted. You have to re-assign new category for the book(s):</div>
			
			<?php
			$qry_all = "select count(*) from library_books where category_id = 'UN' or category_id=''";
			$result = $libdb->query($qry_all);
			$row = $libdb->next_record();
			$total_rows = $libdb->record[0];
			
			$st = requestNumber($_REQUEST['st'], 0);
			$nh = requestNumber($_REQUEST['nh'], 10);
			$page = ceil($total_rows/$nh);
			$qry_book = "select book_recordid,book_title,book_isbn,book_summary,book_author from library_books where category_id = 'UN' or category_id='' LIMIT ".$st.", ".$nh."";
			$libdb->query($qry_book);
			
			?>
			  <table class="table table-striped">
                <tr>
			    <td>Book ISBN</td>
			    <td>Book Title</td>
			    <td>Book Summary</td>				
			    <td>Book Author</td>				
			    <td>Action</td>				
			  </tr>
			  <?
			  if ($libdb->num_rows() == 0) {
					echo "<tr><td class=\"m2_td_content\"colspan=\"5\">No records</td></tr>";
					}
				else {
					while($libdb->next_record()) {
					  echo"<tr>";
						echo"<td>".$libdb->record[1]."</td>";
						echo"<td>".$libdb->record[2]."</td>";
						echo"<td>".$libdb->record[3]."</td>";				
						echo"<td>".$libdb->record[4]."</td>";				
						echo"<td><input type=\"checkbox\" name=\"id[]\" value=\"".$libdb->record[0]."\"></td>";
					  echo"</tr>";
					  }
					  
			  	$this_page = $_SERVER['PHP_SELF']."?action=cfm_delete";
		
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
			  </table>>
			  <p>Select new category</p>
			   <?
				 $libdb_cat = new Modules_sql;
				 $qry_cat = "select category_id, category_name from library_category where category_id!='UN' order by category_name asc;";
				 $libdb_cat->query($qry_cat);
			
			   ?>
			   <select class="form-control" name="category" id="category">
                <option value="">- Choose Category -</option>
                <? 
					while($libdb_cat->next_record()) {          // same with while ($row = $rs->FetchRow())
						echo "<option value=\"".$libdb_cat->record[0]."\">".$libdb_cat->record[1]."</option>";
				}
				?>
              </select>
			   
			<?php
			  if ($total_rows > $nh) {
			  ?>
			  <table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
				<tr> 
				  <td width="200" class="m2_td_content">
				  <div align="right">
				  <?php
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
			</td>
		  </tr>
		  <tr>
			<td width="10%" colspan="2">
				<input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_submit.gif">
				<input type="hidden" name="submit" value="Submit">
				<a href="category.php"><img border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_cancel.gif"></a>
			</td>				
		  </tr>
		</table>
	  </form>
	  <?
	  }
	  else {
	  ?>
	  <table width="62%" border="0" cellspacing="0" cellpadding="5" class="m2_table_outline">
        <tr> 
			<td class="ar11_content">
			<? 
			if(!empty($_SERVER['QUERY_STRING']) && eregi('del=0', $_SERVER['QUERY_STRING'])) {
				echo "The category has been succesfully deleted<br>";
			}
			?>
            <strong>There is no book(s) to be re-assigned to new category. </strong><br>
            <br>
			<div align="right"> 
				<form name="form1" method="post" action="category.php">
                <div align="left">
					<input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_ok.gif">
					<input type="hidden" name="submit" value="Submit">
                </div>
              </form>
			  </div></td>
		  </tr>
		</table>
	<?
	}
	?>
	  </td>
  </tr>
</table>
<?
}

function confirm_add() {
global $app_absolute_path,$root_images_folder;
?>
<h4>Add New Category</h4>
<div class="alert alert-success">Your new category has successfully added</div>
<a class="btn btn-default" href="category.php">Back</a>
<?php
}

function error_cat() {

if(isset($_REQUEST["err"])) $err = $_REQUEST["err"];
else $err = "";
   	echo "<h4>Update Category</h4>";
	if($err=="catname") {
		echo "<div class=\"alert alert-danger\">The Category name has already exist</div>";
		echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Back</a>";				
	}

}
?>
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