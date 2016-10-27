<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0); 
session_start();
include("class.php");
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
$id = $_SESSION['usr_id'];
$_SESSION['usr_username'];

if (!isAllowed(array(501, 502, 503), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
include '../inc/pagehead.php';
?>
<script language="javascript">
function Clickheretoprint()
{ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25"; 
  var content_vlue = document.getElementById("print_content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('<html><head><title>ASMIC : Issue Books</title>');
   docprint.document.write('<link href="../asmic.css" rel="stylesheet" type="text/css">'); 
   docprint.document.write('</head><body onLoad="window.print()"><center>');
   docprint.document.write('<?=addslashes(file_get_contents("../includes/print_header_space.php"))?>');             
   docprint.document.write(content_vlue);          
   docprint.document.write('</center></body></html>'); 
   docprint.document.close(); 
   docprint.focus(); 
}
</script>
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
                <? include("breadcrumb.php"); ?>
                <p></p>
				<h2>Reserve Book(s)</h2>
				<?php
				switch ($_GET['action']){
					case "reserve" :
					issuebook();
					break;
					case "error" :
					error_issue();
					break;
					case "reserve_confirm" :
					reserve_book();
					break;
					case "res_success" :
					reserve_success();
					break;
					default :
					to_confirm();
					break;
				}
				?>
				<?php
					function to_confirm() {
					global $id,$app_absolute_path,$root_images_folder;
					$libdb = new Modules_sql;
						//To get user IC from contact table
						$qry_ic = "select contact_contact.icnum from contact_contact left join user_users on contact_contact.id=user_users.usr_contactid";
						$qry_ic .=" where user_users.usr_id=".$id."";
						$libdb->query($qry_ic);
						$libdb->next_record();
						$mbr_ic = $libdb->record[0];
				?>

				Please Enter book code and member IC to reserve new book
	<form name="entercode" method="post" action="bookreserve.php?action=issue&amp;res=1" onSubmit="validateField();">
        I/C Number
        <input class="form-control" name="member_ic" type="text" id="member_ic" value="<?php echo $mbr_ic; ?>">
        Book Code
        <input class="form-control" name="book_code" type="text" id="book_code" value="<?php echo $_GET['book_code']; ?>">
        <input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_submit.gif">
        <input type="hidden" name="Submit" value="Submit">
    </form>
<?php
}
/** --> END FUNCTION ENTER BOOK TO ISSUE <-- **/
/** --> START FUNCTION ISSUE BOOK <-- **/
function issuebook() {
	global $id,$app_absolute_path,$root_images_folder;
	$libdb = new Modules_sql;
	
	//To get user IC from contact table
	$qry_ic = "select contact_contact.icnum from contact_contact left join user_users on contact_contact.id=user_users.usr_contactid";
	$qry_ic .=" where user_users.usr_id=".$id."";
	$libdb->query($qry_ic);
	$libdb->next_record();
	$mbr_ic = $libdb->record[0];
	
	$book_code = $_REQUEST["book_code"];	
	//To get user ID from contact table
	$qry_id = "select id from contact_contact where icnum = '".$mbr_ic."' and delflag=0";
	$libdb->query($qry_id);
	$libdb->next_record();
	$mbr_id = $libdb->record[0];
	
	$qry = "select library_books.book_recordid, library_books.book_title, library_books_unit.book_status, library_books_unit.accession_no,";
	$qry .=" contact_contact.id, contact_contact.fullname, library_member.* from";
	$qry .=" ((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid),";
	$qry .=" (contact_contact left join library_member on contact_contact.id=library_member.contact_id))";
	$qry .=" where contact_contact.id = '".$mbr_id."' and library_books_unit.accession_no='".$book_code."'";
	$libdb->query($qry);
	
	if (($libdb->num_rows() == 0) || empty($mbr_ic)) {
	$libdb->next_record();
		echo "<div class=\"alert alert-danger\">Sorry we don't have your record in our system</div>";
		echo "<div class=\"alert alert-danger\">Please contact our administrator for further action.</div>";
		echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
	}
	else {
	$libdb->next_record();
		if(($libdb->record[8] < 1) && ($libdb->num_rows() != 0)) {
  			echo "<div class=\"alert alert-danger\">Sorry, you don't have any card left to issue book.</div>";
			echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
		}
		else if($libdb->record[2] == 'n') {
			echo "<div class=\"alert alert-danger\">Sorry, The book Has been Issued</div>";
			echo "<a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
		}
		else {
	
?>
Please ensure the information below is accurate and select the reservation date. To proceed with the reservation, please click 'Reserve' button.
	<?php
	if(isset($_POST['submit'])) {
	
	  //POSTED VALUE FROM FORM
	  $id = $_REQUEST['id'];
	  $date_issue = $_REQUEST["date_issue"];
	  $book_code = $_REQUEST["book_code"];
	  $member_name = $_REQUEST["member_name"];
	  $book_title = $_REQUEST["book_title"];
	  $date1 = changeDate ($date_issue);
	  $dt_added = date("Y-m-d H:i:s");
		  
		if(!empty($_POST['date_issue'])) {
			$dr = changeDate($_POST['date_issue']);
			if($dr < date('Y-m-d')) {
				$date_issue = FALSE;
				echo"<div class=\"alert alert-danger\">Plese make sure that you have entered the correct reservation date</div>";
			}
			else {
				$date_issue = TRUE;
			}
		}
		else {
			$date_issue = FALSE;
			echo"<div class=\"alert alert-danger\">Plese make sure that you have entered the correct reservation date</div>";
		}
		if($date_issue) {
		  	
		  //INSERT TO DB
		  $qry_str = "insert into library_issue (issue_id, accession_no, contact_id, get_action, approve, member_name, book_title, date_issue, date_added) values";
		  $qry_str .=" ('NULL', '".$book_code."','".$id."', '3', 'No', '".$member_name."', '".$book_title."','".$date1."','".$dt_added."');";
		  $libdb->query($qry_str);
		  
		  //REDUCE CARDS OF MEMBER
		  $qry_str = "update library_member set member_cards = member_cards - 1 where contact_id = '".$id."';";
		  $libdb->query($qry_str);
		  
		  //CHANGE STATUS OF BOOKS
		  $qry_str = "update library_books_unit set book_status = 'n' where accession_no = '".$book_code."';";
		  $libdb->query($qry_str);
	
		  include_once("../classes/audit_trail.php");
		  $audit_trail = new audit_trail();
		  $audit_trail->writeLog($_SESSION['usr_username'], "library", "Reserve Books : ".$book_code."");
		  echo'<meta http-equiv="refresh" content="0;URL=bookreserve.php?action=reserve_confirm&id='.$id.'">';
		 }
		
	}
	?>
	<form name="entercode" method="post" action="<?php echo $PHP_SELF ?>">
		<input type="hidden" value="<? echo $mbr_id; ?>" name="id">
        
        I/C Number
        <?php echo $mbr_ic; ?>
        <input name="member_ic" type="hidden" id="member_ic" value="<?php echo $mbr_ic; ?>"> 

        Name
        <?php echo $libdb->record[5]; ?>
        <input name="member_name" type="hidden" id="member_name" value="<?php echo $libdb->record[5]; ?>">

        Book Code
        <?php echo $book_code; ?>
        <input name="book_code" type="hidden" id="book_code" value="<?php echo $book_code; ?>">

        Title
        <?php echo $libdb->record[1]; ?>
        <input name="book_title" type="hidden" id="book_title" value="<?php echo $libdb->record[1]; ?>">

        Date of reserve
        <input class="form-control" name="date_issue" type="text" id="date_issue" value="<?php echo date("d-m-Y"); ?>">
        <input type="hidden" name="submit" value="Submit">
        <a href="pubcategory.php">Cancel</a>
    </form>
<?php
}
}
}
/** --> END FUNCTION ISSUE BOOK <-- **/

function error_issue() {

if(isset($_REQUEST["err"])) $err = $_REQUEST["err"];
else $err = "";
	 if($err=="card") {
		echo "<div class=\"alert alert-danger\">The User doesn't have any card left to issue book</div>";
		echo "<br><a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";
	 }
	 if($err=="book") {
		echo "<div class=\"alert alert-danger\">The Book Code is not valid</div>";
		echo "<br><a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";				
	 }
	 if($err=="user") {
		echo "<div class=\"alert alert-danger\">The User is not valid</div>";
		echo "<br><a class=\"btn btn-default\" href=\"#\" onclick=\"history.back();return false\">Go Back To The Previous Page</a>";				
	 }
}

function reserve_book() {
global $app_absolute_path, $root_images_folder;
	$id = $_REQUEST['id'];
	$libdb_date = new Modules_sql;
	$qry_due = "select issue_period from library_settings";
	$libdb_date->query($qry_due);
	$libdb_date->next_record();
	$d = $libdb_date->record[0];
	
	
	$libdb = new Modules_sql;
	$libdb_info = new Modules_sql;
	$libdb_r = new Modules_sql;
	if(isset($_POST['submit'])) {
		if(!empty($_POST['method'])) {
			$method = TRUE;
			$method = $_POST['method'];
		}
		else {
		$method = FALSE;
		echo"<p><span class=\"errormsg\">Please choose way to receive your book</span></p>";
		}
		if(!empty($_POST['check'])) {
			$check = TRUE;
			$check = $_POST['check'];
		}
		else {
		$check = FALSE;
		echo"<p><span class=\"errormsg\">You have to check which book(s) to approve!</span></p>";
		}
		
		$receipt = $_POST['receipt'];
		if($method && $check) {
		$counter = 0;
		foreach($_POST['check'] as $check)
        {
			$is_d = $_REQUEST['date_issue'][$counter];
			$qry = "update library_issue set get_action='".$method."', approve='No', date_issue='".$is_d."',";
			$qry .="receipt_no='".$receipt."', issue_status=2 where issue_id='".$check."'";
			$libdb->query($qry);
			$counter++;
        }
			
		 echo'<meta http-equiv="refresh" content="0;URL=bookreserve.php?action=res_success&id='.$id.'&receipt='.$receipt.'">';
		}
	}
	
	$qry_info .=" select contact_contact.id, contact_contact.icnum, contact_contact.fullname,";
	$qry_info .=" contact_address.id, contact_address.line1, contact_address.phone1,";
	$qry_info .=" contact_email.id, contact_email.email from";
	$qry_info .=" (((library_member left join contact_contact on contact_contact.id=library_member.contact_id)";
	$qry_info .=" left join contact_address ON contact_address.id=contact_contact.id)";
	$qry_info .=" left join contact_email on contact_contact.id=contact_email.id)";
	$qry_info .=" where library_member.contact_id =".$id."";
	$libdb_info->query($qry_info);
	$libdb_info->next_record();
	
	$qry_chk_id = "select count(*) from library_issue where contact_id=".$id."";
	$libdb_r->query($qry_chk_id);
	$libdb_r->next_record();
	  if ($libdb_r->record[0]) {
			$qry_receipt = "select library_issue.get_action, library_issue.date_issue, library_issue.issue_id";
			$qry_receipt .=" from library_issue where contact_id=".$id." and receipt_no is NULL order by issue_id DESC";
			$libdb_r->query($qry_receipt);
			$libdb_r->next_record();
	  }
	
	$qry_lib = "SELECT library_books.book_recordid, library_books_unit.accession_no, library_books.book_title, library_books.book_isbn,";
	$qry_lib .=" library_books.book_author, library_issue.contact_id, library_issue.date_issue, library_issue.date_due, library_issue.issue_id,";
	$qry_lib .=" library_issue.issue_status from ((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid)";
	$qry_lib .=" left join library_issue on library_issue.accession_no=library_books_unit.accession_no )";
	$qry_lib .=" where library_issue.contact_id = ".$id." and library_issue.issue_status!=5";
	$qry_lib .=" ORDER BY library_issue.issue_status ASC";
	$libdb->query($qry_lib);
?>
<div align="center"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_reserve_step3.gif"></div><br>
<form action="<? echo $PHP_SELF ?>" method="post" name="cart">
<input type="hidden" value="<? echo $id; ?>" name="id">
<input type="hidden" name="receipt" value="<? echo date("Ymd").$libdb_r->record[2]; ?>">
  <table width="100%" border="0" cellpadding="1">
  <tr> 
      <td width="100%" class="ar11_content"><strong>Reserve Book(s) for : <? echo $libdb_info->record[2]; ?><br>
        </strong> I/C Number : <? echo $libdb_info->record[1]; ?><br>
		Address : <? echo $libdb_info->record[4]; ?><br>
        Contact Number : <? echo $libdb_info->record[5]; ?><br>
        E-Mail : <? echo $libdb_info->record[7]; ?><br>
      </td>
  </tr>
  <tr> 
	  <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator2.gif" class="ar11_content">&nbsp;</td>
  </tr>
 <tr><td class="ar11_content"><strong>1. Choose delivery method</strong></td></tr>
 <tr><td class="ar11_content">Receive Book(s) By :
      <input type="radio" name="method" value="1" <? if ($_POST['method']==1) echo "checked"; ?>>
      Deliver by ASM &nbsp;&nbsp; <input type="radio" name="method" value="2" <? if ($_POST['method']==2) echo "checked"; ?>>
      Pick up</td>
</tr>
 <tr> 
	  <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator2.gif" class="ar11_content">&nbsp;</td>
  </tr>
 <tr>
    <td class="ar11_content"><strong>2. Select book(s)</strong></td>
  </tr>
	<tr>
		<td class="ar11_content">
		<ul>
			<li>To confirm your delivery method, please select a delivery method and check on any of the below book(s) to match the delivery method. Once done, click on the "Confirm" button to proceed.</li>
			<li>Click &quot;Add More Books&quot; to reserve more books.</li>
	        <li><b>NOTE:</b> All reserveration will be cancelled the following day if no confirmation has been made.</li>
		</ul>
		</td>
	</tr>
         <tr> 
            <td class="ar11_content"><strong>Books Information</strong></td>
          </tr>
	
  <tr> 
    <table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
        <tr> 
          <td width="15%" class="m2_td_fieldname"> <div align="center">Book Code</div></td>
          <td width="30%" class="m2_td_fieldname"> <div align="left">Title</div></td>
          <td width="20%" class="m2_td_fieldname"> <div align="left">Author</div></td>
          <td width="15%" class="m2_td_fieldname"> <div align="center">Date Reserve</div></td>
		  <td width="12%" class="m2_td_fieldname"> <div align="center">Status</div></td>
          <td width="8%" class="m2_td_fieldname"> <div align="center">&nbsp;</div></td>
        </tr>
		<?
		if($libdb->num_rows() == 0) {
			echo "<tr><td colspan=\"6\" class=\"ar11_content\">You have no reservations record. Click button \"Add More Books\" to reserve new books.</td></tr>";
		}
		else {
		$set=0;
		while($libdb->next_record()) {
		
		?>	
        <tr> 
          <td class="m2_td_content" valign="top"><? echo $libdb->record[1]; ?></td>
          <td class="m2_td_content" valign="top"><? echo $libdb->record[2]; ?></td>
          <td class="m2_td_content" valign="top"><? echo $libdb->record[4]; ?></td>
          <td class="m2_td_content" align="center" valign="top"><? echo ChangeDate($libdb->record[6], "d-m-Y"); ?><input type="hidden" name="date_issue[]" value="<? echo $libdb->record[6]; ?>"></td>
          <td class="m2_td_content" valign="top">
		  	<? 
				if($libdb->record[9]==1) {
					echo "<b>Not Confirm</b>";
				}
				elseif($libdb->record[9]==2) {
					echo "<b>Pending Approval</b>";
				}
				elseif($libdb->record[9]==3) {
					echo "<b>Approved - Pending Pick up</b>";
				}
				elseif($libdb->record[9]==4) {
					echo "<b>Approved - Pending Delivered</b>";
				}
				else {
					echo "";
				}
			?>
		  </td>
		  <td class="m2_td_content" valign="top">
		  <?
		  if($libdb->record[9]==1) {
		  ?>
		  	<input type="checkbox" name="check[]" value="<? echo $libdb->record[8]; ?>" <? foreach($_POST['check'] as $check) { if ($libdb->record[8]==$check) echo "checked"; } ?>>
			<a href="functions.php?action=delreserve&book=<? echo $libdb->record[1]; ?>&id=<? echo $id; ?>&cart=<? echo $libdb->record[8]; ?>"><img title="Cancel Reservation" border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/icon_cancelreserve.gif"></a>
		  <?
		  }
		  else {
		  	echo "";
		  }
		  ?>
		  </td>
        </tr>
		<?
		}
		$set++;
		}
		?>
      </table></td>
  </tr>
  <tr> 
      <td class="ar11_content"><br>
        <br>
		<input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_confirm.gif">
		<input type="hidden" name="submit" value="Submit">
		<a href="pubcategory.php"><img border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_addmorebooks.gif"></a>
		&nbsp;<a href="#" onclick="history.back();return false"><img border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_back.gif"></a>
	  </td>
  </tr>
  <tr><td>&nbsp;</td></tr>
<tr><td class="ar11_content">You may cancel your reservations by clicking icon <img title="Delete" border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/icon_cancelreserve.gif">on each record.</td></tr>
</table>
</form>
<?
}

function reserve_success() {
	global $app_absolute_path,$root_images_folder;
	$libdb = new Modules_sql;
	$libdb_info = new Modules_sql;
	$id = $_REQUEST['id'];
	$receipt = $_REQUEST['receipt'];
	
	$qry_info .=" select library_books_unit.accession_no, contact_contact.id, contact_contact.icnum, contact_contact.fullname,";
	$qry_info .=" contact_address.line1, contact_address.phone1, library_issue.date_issue, library_issue.issue_id,";
	$qry_info .=" library_issue.receipt_no, library_issue.get_action, contact_email.email from";
	$qry_info .=" ((library_issue left join library_books_unit on library_issue.accession_no=library_books_unit.accession_no),";
	$qry_info .=" (contact_contact left JOIN contact_address ON contact_address.id=contact_contact.id)";
	$qry_info .=" left join contact_email on contact_contact.id=contact_email.id)";
	$qry_info .=" where library_issue.contact_id = ".$id." and library_issue.contact_id=contact_contact.id and library_issue.approve='No'";
	$qry_info .=" and library_issue.receipt_no = ".$receipt." ORDER BY library_issue.accession_no ASC";
	$libdb_info->query($qry_info);
	$libdb_info->next_record();
	
	$qry = "select library_books.book_recordid, library_books.book_isbn,library_books.book_title, library_books.book_author,";
	$qry .= " library_books_unit.book_recordid, library_books_unit.accession_no, contact_contact.id, library_issue.*";
	$qry .= " from ((library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid),";
	$qry .= " (library_issue left join contact_contact on library_issue.contact_id=contact_contact.id)) where";
	$qry .= " library_issue.contact_id = ".$id." and library_issue.receipt_no=".$receipt."";
	$qry .=" and library_issue.accession_no=library_books_unit.accession_no and library_issue.approve='No'";
	$libdb->query($qry);
?>
<div align="center"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_reserve_step4.gif"></div><br>
<table width="100%" border="0" cellspacing="0" cellpadding="1">
  <tr> 
    <td>
	<div class="style3" id="print_content">
	    <table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
          <tr> 
            <td width="100%" class="ar11_content"> <strong>Reserved Book(s) for 
              : <? echo $libdb_info->record[3]; ?></strong><br>
              I/C Number : <? echo $libdb_info->record[2]; ?><br>
              Address : <? echo $libdb_info->record[4]; ?><br>
              Contact Number : <? echo $libdb_info->record[5]; ?><br>
              E-Mail : <? echo $libdb_info->record[10]; ?><br>
              <br> </td>
          </tr>
          <tr>
            <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator2.gif" class="ar11_content">&nbsp;</td>
          </tr>
           <tr> 
            <td class="ar11_content">The reservation of the following book(s) have been sent for approval. You will be informed by ASM Librarian once the approval process has been completed. Thank you.</td>
          </tr>
		  <tr><td class="ar11_content">To view your reservation list and the book status, please click <a href="bookreserve.php?action=reserve_confirm&id=<?=$id?>">Reservation List</a> on the main menu.
		  <tr><td>&nbsp;</td></tr>
         <tr> 
            <td class="ar11_content"><strong>Books Information</strong></td>
          </tr>
 
 <tr> 
            <td><table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
                <tr> 
                  <td align="center" class="m2_td_fieldname">No.</td>
                  <td class="m2_td_fieldname">Book Code</td>
                  <td class="m2_td_fieldname">Book Title</td>
                  <td class="m2_td_fieldname">Book ISBN</td>
                  <td class="m2_td_fieldname">Date Reserve</td>
                </tr>
                <?
				if($libdb->num_rows() == 0) {
					echo "<tr><td colspan=\"5\">No records</td></tr>";
				}
				else {
				$i = 1;
				while($libdb->next_record()) {
			  ?>
                <tr> 
                  <td align="center" class="m2_td_content"><? echo $i; ?></td>
                  <td class="m2_td_content"><? echo $libdb->record[5]; ?></td>
                  <td class="m2_td_content"><? echo $libdb->record[2]; ?></td>
                  <td class="m2_td_content"><? echo $libdb->record[1]; ?></td>
                  <td class="m2_td_content"><? echo DateConvert($libdb->record[14], "j M Y"); ?></td>
                </tr>
                <?
			  $i++;
			  }
			  }
			  ?>
              </table></td>
          </tr>
          <tr> 
            <td>&nbsp;</td>
          </tr>
        </table> 
	  </div>
	  </td>
  </tr>
  <tr><td class="ar11_content">Please print out this page for your reference.</td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td>
		<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
		<a href="pubcategory.php"><img border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_back.gif"></a>
    </td>
  </tr>
</table>
<br>
<?
}
?>
<script language="javascript">
	function validateField() {
		
    	var form = document.entercode;
    
		if (form.elements["member_ic"].value==0) {
			alert( "You must enter Member Code" );
			form.elements["member_ic"].focus();
			event.returnValue=false;
		}
	
		if (form.elements["book_code"].value == "") {
			alert( "You must enter the Book Code" );
			form.elements["book_code"].focus();
			event.returnValue=false;
		}
	}

</script>
