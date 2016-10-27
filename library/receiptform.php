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
<script language="javascript">

function Clickheretoprint()
{ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25"; 
  var content_vlue = document.getElementById("print_content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('<html><head><title>ASMIC : Issue Books</title>');

   docprint.document.write('<link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">');
   docprint.document.write('<link href="../css/style.css" rel="stylesheet" type="text/css">'); 

   docprint.document.write('</head><body onLoad="window.print()"><center>');
   docprint.document.write('<?=addslashes(file_get_contents("../includes/print_header_space.php"))?>');          
   docprint.document.write(content_vlue);          
   docprint.document.write('</center></body></html>'); 
   docprint.document.close(); 
   docprint.focus(); 
}
</script>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
      <?
		include("class.php");
		include_once($app_absolute_path."includes/functions.php");
	  ?>
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td class="module_title"><? include("breadcrumb.php"); ?></td>
        </tr>
        <tr> 
          <td class="ar11_content"><div class="module_sub_title"><br><h4>Issue Books</h4></div><br>
		  <?
		  global $app_absolute_path,$root_images_folder;
		  $libdb = new Modules_sql;
		  $id = $_REQUEST['id'];
		  $receipt = $_REQUEST['receipt'];
			$qry_record .=" select library_books_unit.accession_no, contact_contact.id, contact_contact.icnum, contact_contact.fullname,";
			$qry_record .=" contact_address.id, contact_address.line1, contact_address.phone1, library_issue.*, ";
			$qry_record .=" contact_email.id, contact_email.email from";
			$qry_record .=" ((library_issue left join library_books_unit on library_issue.accession_no=library_books_unit.accession_no),";
			$qry_record .=" (contact_contact left JOIN contact_address ON contact_address.id=contact_contact.id)";
			$qry_record .=" left join contact_email on contact_contact.id=contact_email.id)";
			$qry_record .=" where library_issue.contact_id =".$id." and library_issue.contact_id=contact_contact.id and approve='Yes'";
			$qry_record .=" and receipt_no=".$receipt." ORDER BY library_issue.accession_no ASC";
			$libdb->query($qry_record);
			$libdb->next_record();
		  ?>
		  </td>
        </tr>
        <tr> 
          <td>
		  <div class="style3" id="print_content">
		  <table width="100%" border="0" cellspacing="3" cellpadding="2">
			  <tr> 
				  <td class="module_sub_title">Details Of Issued Book(s)</td>
			  </tr>
			  <tr> 
				<td class="ar11_content">Reference Number : <? echo $receipt; ?><br>
                    Date : <? echo date("d M Y"); ?> <br>
                    Receive Book(s) : 
					<? 
					if($libdb->record[10] == 1) {
							echo "Dispatch by ASMIC"; 
					}
					else { echo "Pick Up"; }
					?>
				</td>
			  </tr>
			  <tr> 
				<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
					<tr> 
					  <td colspan="2" class="module_sub_title">Contact Information</td>
					</tr>
					<tr> 
					  <td class="ar11_content" width="16%">NRIC :</td>
					  <td class="ar11_content" width="84%"><? echo $libdb->record[2]; ?></td>
					</tr>
					<tr> 
					  <td class="ar11_content" valign="top">Name :</td>
					  <td class="ar11_content"><? echo $libdb->record[3]; ?></td>
					</tr>
					<tr> 
					  <td class="ar11_content" valign="top">Address :</td>
					  <td class="ar11_content"><? echo nl2br($libdb->record[5]); ?>
                        <br>
                        <br>
                      </td>
					</tr>
					<tr> 
					  <td class="ar11_content">Contact Number :</td>
					  <td class="ar11_content"><? echo $libdb->record[6]; ?></td>
					</tr>
					<tr> 
					  <td class="ar11_content">E-mail :</td>
					  <td class="ar11_content"><? echo $libdb->record[20]; ?></td>
					</tr>
					<tr> 
					  <td colspan="2"><hr></td>
					</tr>
				  </table></td>
			  </tr>
			  <tr> 
				  <td>List of issued book(s) :</td>
			  </tr>
			  <tr>
			  	<td>
			  <?
 
			  $qry = "select library_books.book_recordid, library_books.book_isbn,library_books.book_title, library_books.book_author,";
			  $qry .= " library_books_unit.book_recordid, library_books_unit.accession_no, contact_contact.id, library_issue.*";
			  $qry .= " from ((library_books inner join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid),";
			  $qry .= " (library_issue inner join contact_contact on library_issue.contact_id=contact_contact.id)) where";
			  $qry .= " library_issue.contact_id = ".$id." and library_issue.accession_no=library_books_unit.accession_no and approve='Yes'";
			  $qry .= " and receipt_no=".$receipt." and library_issue.date_return is null";
			  $libdb->query($qry);
			  ?>
                <table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
                    <tr> 
                      <td width="14%" class="m2_td_fieldname"><div align="center">Book Code</div></td>
                      <td width="14%" class="m2_td_fieldname"><div align="center">ISBN Number</div></td>
                      <td width="27%" class="m2_td_fieldname"><div align="left">Title</div></td>
                      <td width="18%" class="m2_td_fieldname"><div align="left">Author</div></td>
                      <td width="13%" class="m2_td_fieldname"><div align="center">Date Issue</div></td>
                      <td width="13%" class="m2_td_fieldname"><div align="center">Date Due</div></td>
                    </tr>
					<?
					if($libdb->num_rows() == 0) {
						echo "<tr><td colspan=\"6\">No records</td></tr>";
					}
					else {
					while($libdb->next_record()) {
					?>
                    <tr> 
                      <td class="m2_td_content">&nbsp;<? echo $libdb->record[5]; ?></td>
                      <td class="m2_td_content">&nbsp;<? echo $libdb->record[1]; ?></td>
                      <td class="m2_td_content">&nbsp;<? echo $libdb->record[2]; ?></td>
                      <td class="m2_td_content">&nbsp;<? echo $libdb->record[3]; ?></td>
                      <td class="m2_td_content">&nbsp;<? echo DateConvert($libdb->record[14], "d M Y"); ?></td>
                      <td class="m2_td_content">&nbsp;<? echo DateConvert($libdb->record[15], "d M Y"); ?></td>
                    </tr>
					<?
					}
					}
					?>
                  </table> 
				  </td>
			  </tr>
			  <tr> 
				<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="1">
					<tr> 
					  <td width="52%">&nbsp;</td>
					  <td width="48%">&nbsp;</td>
					</tr>
					<tr> 
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					</tr>
					<tr> 
					  <td>Receive By, </td>
					  <td>Delivered By, </td>
					</tr>
					<tr> 
					  <td><br>
						....................................</td>
					  <td><br>
						..............................</td>
					</tr>
					<tr> 
					  <td>&nbsp;</td>
					  <td>&nbsp;</td>
					</tr>
				  </table>
				 </td>
			  </tr>
			</table>
			</div>
		  </td>
        </tr>
		<tr>
		 <td>
		 	<input type="button" onClick="javascript:Clickheretoprint()" class="btn btn-primary" value="Print">
			<a href="bookissue.php" class="btn btn-default">Cancel</a>
		 </td>
		</tr>
      </table>
	  </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
</div>
    <!-- End Body Content -->

  <?php include '../inc/footer.php'; ?>
  
    <!-- End site footer -->
    <a id="back-to-top"><i class="fa fa-angle-double-up"></i></a>  
</div>
<?php include '../inc/js.php'; ?>
</body>
</html>