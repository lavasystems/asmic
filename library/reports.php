<?
session_start();
include_once("local_config.php");
require_once($app_absolute_path . "includes/functions.php");
if (!isAllowed(array(501), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
?>
<script language="javascript" src="../includes/CalendarPopup.js"></script>
<SCRIPT language="javascript" type="text/javascript">
	function clearUserSearch (form){
		form.usr_grpid.options[0].selected = true;
		form.usr_username.value = '';
		form.fullname.value = '';
		form.icnum.value = '';
		form.dateFrom.value = '';
		form.dateTo.value = '';
	}

	var calFrom = new CalendarPopup();
	calFrom.showYearNavigation();
	var calTo = new CalendarPopup();
	calTo.showYearNavigation();
</SCRIPT>
<script language="javascript">
function Clickheretoprint()
{ 
  var disp_setting="toolbar=yes,location=no,directories=yes,menubar=yes,"; 
      disp_setting+="scrollbars=yes,width=650, height=600, left=100, top=25"; 
  var content_vlue = document.getElementById("print_content").innerHTML; 
  
  var docprint=window.open("","",disp_setting); 
   docprint.document.open(); 
   docprint.document.write('<html><head><title>ASMIC</title>');
   docprint.document.write('<link href="../asmic.css" rel="stylesheet" type="text/css">'); 
   docprint.document.write('</head><body onLoad="window.print()"><center>');
   docprint.document.write('<?=addslashes(file_get_contents("../includes/print_header_space.php"))?>');                
   docprint.document.write(content_vlue);          
   docprint.document.write('</center></body></html>'); 
   docprint.document.close(); 
   docprint.focus(); 
}
</script>
<? 
include_once($app_absolute_path."includes/template_header.php");
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="4%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="24" height="8"></td>
          <td>&nbsp;</td>
          <td width="1%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="12" height="8"></td>
        </tr>
        <tr> 
          <td class="module_title"><? include("breadcrumb.php"); ?></td>
        </tr>
        <tr> 
          <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif" width="2" height="13"></td>
        </tr>
        <tr> 
          <td> <? include("body_nav.php"); ?> </td>
        </tr>
		<tr> 
			<td><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" height="3" width="1"></td>
		</tr>        <tr> 
          <td><div class="module_sub_title"><br>Reports</div></br>
		  <?	
			switch ($_GET['action']){
				case "issue" :				
					book_issue();
				break;
				case "due" :
					book_due();
				break;
				case "book" :
					book_list();
				break;
				case "av_book" :
					book_available();
				break;
				case "return" :
					book_returned();
				break;
				case "chk" :			
					check();
				break;
				default :
					main_report();
				break;
			}			
		  ?>
		  </td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
include_once($app_absolute_path."includes/template_footer.php");

function main_report() {
global $app_absolute_path, $root_images_folder;
?>
<div class="ar11_content">Select options below to view the reports :</div>
<br>
<table width="60%" border="0" cellspacing="0" cellpadding="10" class="m2_table_outline">
  <tr> 
    <td> 
      <form name="report" method="post" action="reports.php?action=chk">
        <table width="100%" border="0" cellspacing="0" cellpadding="2" >
          <tr> 
            <td colspan="4"></td>
          </tr>
          <tr> 
            <td colspan="4" class="ar11_content"></td>
          </tr>
          <tr> 
            <td width="25%" class="ar11_content"><div align="right">Type of Report 
                :</div></td>
            <td width="33%"> <select class="inputbox" name="report" id="report">
                <option value="">-- Select Reports --</option>
                <option value="1" <? if($_REQUEST['action'] == 'issue') { echo "selected"; } ?>>Issued Books</option>"; ?>
                <option value="2" <? if($_REQUEST['action'] == 'due') { echo "selected"; } ?>>Overdue Books</option>
                <option value="3" <? if($_REQUEST['action'] == 'book') { echo "selected"; } ?>>List of Books</option>
                <option value="4" <? if($_REQUEST['action'] == 'av_book') { echo "selected"; } ?>>Available Books</option>
                <option value="5" <? if($_REQUEST['action']=='return') { echo "selected"; } ?>>Returned Books</option>
              </select> </td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr> 
            <td class="ar11_content"><div align="right">From: </div></td>
            <td> 
              <?
			//Added by CB
			//=======================================================================================================
			if(isset($_REQUEST["dateFrom"])) $dateFrom = $_REQUEST["dateFrom"];
			elseif(isset($_REQUEST["from"])) $dateFrom = $_REQUEST["from"];
			else $dateFrom = date("m/d/Y");
			//=======================================================================================================
			?>
              <input class="inputbox" name="dateFrom" type="text" id="dateFrom" size="10" value="<?=htmlspecialchars($dateFrom) ?>" readonly> 
              <A id="anchorFrom" onclick="calFrom.select(document.report.dateFrom,'anchorFrom','MM/dd/yyyy'); &#10;return false;" href="javascript:void(0)" name="anchorFrom"><img align="absmiddle" src="../images/icon_calendar.gif" width="22" height="20" border="0"></A> 
            </td>
            <td class="ar11_content" width="7%">To:</td>
            <td width="35%"> 
              <?
			//Added by CB
			//=======================================================================================================
			if(isset($_REQUEST["dateTo"])) $dateTo = $_REQUEST["dateTo"];
			elseif(isset($_REQUEST["to"])) $dateTo = $_REQUEST["to"];
			else $dateTo = date('m/d/Y',mktime(0, 0, 0, date("m")+1, date("d"),  date("Y")));
			//=======================================================================================================
			?>
              <input class="inputbox" name="dateTo" type="text" id="dateTo" size="10" value="<?=htmlspecialchars($dateTo) ?>" readonly> 
              <A id="anchorTo" onclick="calTo.select(document.report.dateTo,'anchorTo','MM/dd/yyyy'); &#10;return false;" href="javascript:void(0)" name="anchorTo"><img align="absmiddle" src="../images/icon_calendar.gif" width="22" height="20" border="0"></A> 
            </td>
          </tr>
          <tr> 
            <td colspan="4"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" height="4" width="1"></td>
          </tr>
          <tr> 
            <td></td>
            <td colspan="3"> <input type="image" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_submit.gif"> 
              <input type="hidden" name="Submit" value="Submit"> </td>
          </tr>
        </table>
      </form>
	  </td>
  </tr>
</table>
<br>
<?
}

function check() {
	echo main_report();
	if(empty($_REQUEST['report'])) {
		echo "<font class=\"errormsg\">* Please Select type of reports</font>";
	}
	
	$report = $_REQUEST['report'];
	$dt_from = $_REQUEST['dateFrom']; //Added by CB
	$dt_to = $_REQUEST['dateTo']; //Added by CB
	
	if($report == 1) {
	echo'<meta http-equiv="refresh" content="0;URL=reports.php?action=issue&from='.$dt_from.'&to='.$dt_to.'">';	
	}
	if($report == 2) {
	echo'<meta http-equiv="refresh" content="0;URL=reports.php?action=due&from='.$dt_from.'&to='.$dt_to.'">';
	}
	if($report == 3) {
	echo'<meta http-equiv="refresh" content="0;URL=reports.php?action=book&from='.$dt_from.'&to='.$dt_to.'">';
	}
	if($report == 4) {
	echo'<meta http-equiv="refresh" content="0;URL=reports.php?action=av_book&from='.$dt_from.'&to='.$dt_to.'">';
	}
	if($report == 5) {
	echo'<meta http-equiv="refresh" content="0;URL=reports.php?action=return&from='.$dt_from.'&to='.$dt_to.'">';
	}
	
}

function book_issue () {
	global $app_absolute_path,$root_images_folder;
	echo main_report();
	
	include("class.php");
	include_once($app_absolute_path."includes/functions.php");
	$libdb = new Modules_sql;
	
	$report = $_REQUEST['report'];
	$dt_from = date("Y-m-d", strtotime($_REQUEST["from"])); //Added by CB
	$dt_to = date("Y-m-d", strtotime($_REQUEST["to"])); //Added by CB

	$qry_all = "select count(*) from library_issue where approve='Yes' and receipt_no is not null and date_issue >= '".$dt_from."' and date_issue <= '".$dt_to."'";
	$result = $libdb->query($qry_all);
	$row = $libdb->next_record();
	$total_rows = $libdb->record[0];
	
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 20);
	$page = ceil($total_rows/$nh);
	
  	$qry_issue = "select * from library_issue where approve='Yes' and receipt_no is not null and date_issue >= '".$dt_from."' and date_issue <= '".$dt_to."' LIMIT ".$st.", ".$nh."";
  	$libdb->query($qry_issue);
	//echo($qry_issue);
?>
<div class="style3" id="print_content">
	<div class="module_sub_title">List of Issued Book(s) : </div><br>
	<font class="ar11_content"><strong><? echo "Total Records: " .$total_rows." book(s)"; ?></strong></font><br><br>
	<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
        <tr> 
          <td class="m2_td_fieldname"><div align="center">No.</div></td>
          <td class="m2_td_fieldname"><div align="center">Book Code</div></td>
          <td class="m2_td_fieldname"><div align="center">Book Title</div></td>
          <td class="m2_td_fieldname"><div align="center">Date Issued</div></td>
          <td class="m2_td_fieldname"><div align="center">Date Due</div></td>
          <td class="m2_td_fieldname"><div align="center">Member name</div></td>
        </tr>
        <?
		if($libdb->num_rows() == 0) {
			if($dt_from > $dt_to){
				echo "<span class=\"errormsg\">The range of date is not valid</span><br><br>";
			}
			echo "<tr><td class=\"m2_td_content\" colspan=\"6\">No records</td></tr>";
		}
		else {
		$i = $st +1;
			while($libdb->next_record()) {
			echo"<tr> ";
			  echo"<td class=\"m2_td_content\">".$i."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[1]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[6]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[7], "j M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[8], "j M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[5]."</td>";		  
			echo"</tr>";
			$i++;
			}
		  $this_page = $_SERVER['PHP_SELF']."?action=issue&from=".$_REQUEST['from']."&to=".$_REQUEST['to']."";
	
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
	</div>
	<br>
	<?
	if ($total_rows > $nh) {
	?>
	<table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
		<tr> 
		  <td width="200" class="m2_td_content">
		  <div align="right">
		  <?
		  echo "Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
	<?
	}
	?>
	<br>
	<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
<?
}

function book_due () {

	global $app_absolute_path,$root_images_folder;
	echo main_report();
	
	include("class.php");
	include_once($app_absolute_path."includes/functions.php");
	$libdb = new Modules_sql;
	$report = $_REQUEST['report'];
	$dt_from = date("Y-m-d", strtotime($_REQUEST["from"])); //Added by CB
	$dt_to = date("Y-m-d", strtotime($_REQUEST["to"])); //Added by CB

	$qry_all = "select count(*) from library_issue where date_due >= '".$dt_from."' and date_due <= '".$dt_to."' and date_return='0000-00-00'";
	$result = $libdb->query($qry_all);
	$row = $libdb->next_record();
	$total_rows = $libdb->record[0];
	
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 20);
	$page = ceil($total_rows/$nh);
	
  	$qry_issue = "select * from library_issue where date_due >= '".$dt_from."' and date_due <= '".$dt_to."' and date_return='0000-00-00' LIMIT ".$st.", ".$nh.""; //where date_issue > ".$dt_to."";
  	$libdb->query($qry_issue);
?>
<div class="style3" id="print_content">
<div class="module_sub_title">List of Overdue Book(s) :</div><br>
<font class="ar11_content"><strong><? echo "Total Records: " .$total_rows." book(s)"; ?></strong></font><br><br>
<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
  <tr> 
    <td class="m2_td_fieldname"><div align="center">No.</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Code</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Title</div></td>
    <td class="m2_td_fieldname"><div align="center">Date Issued</div></td>
    <td class="m2_td_fieldname"><div align="center">Date Due</div></td>
    <td class="m2_td_fieldname"><div align="center">Member name</div></td>
  </tr>
  <?
		if($libdb->num_rows() == 0) {
			if($dt_from > $dt_to){
					echo "<span class=\"errormsg\">The range of date is not valid</span><br><br>";
				}
			echo "<tr><td class=\"m2_td_content\" colspan=\"6\">No records</td></tr>";
		}
		else {
		$i = $st +1;
			while($libdb->next_record()) {
			echo"<tr> ";
			  echo"<td class=\"m2_td_content\">&nbsp;".$i."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[1]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[6]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[7], "j M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[8], "j M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[5]."</td>";
			echo"</tr>";
			$i++;
			}
		  $this_page = $_SERVER['PHP_SELF']."?action=due&from=".$_REQUEST['from']."&to=".$_REQUEST['to']."";
	
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
</div>
<br>
<?
if ($total_rows > $nh) {
?>
<table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
<tr> 
  <td width="200" class="m2_td_content">
  <div align="right">
  <?
  echo "Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
<?
}
?>
	<br>
	<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
<?
}

function book_available () {

	global $app_absolute_path,$root_images_folder;
	echo main_report();
	
	include("class.php");
	include_once($app_absolute_path."includes/functions.php");
	
	$libdb = new Modules_sql;
	$lib_count = new Modules_sql;
	$report = $_REQUEST['report'];
	$dt_from = date("Y-m-d", strtotime($_REQUEST["from"])); //Added by CB
	$dt_to = date("Y-m-d 23:59:59", strtotime($_REQUEST["to"])); //Added by CB

	$qry_all = "select count(*) from library_books_unit where book_status='y'";
	$qry_all .=" and date_added >= '".$dt_from."' and date_added <= '".$dt_to."'";
	$result = $libdb->query($qry_all);
	$row = $libdb->next_record();
	$total_rows = $libdb->record[0];
	
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 20);
	$page = ceil($total_rows/$nh);
	
  	$qry = "select library_books.book_recordid, library_books.book_title, library_books.book_author, library_books.book_publisher,";
	$qry .=" library_books.date_added, library_books.book_isbn, library_books_unit.book_status from library_books inner join library_books_unit";
	$qry .=" on library_books.book_recordid=library_books_unit.book_recordid where library_books_unit.book_status='y' and";
	$qry .=" library_books.date_added >= '".$dt_from."' and library_books.date_added <= '".$dt_to."'";
	$qry .=" group by library_books.book_isbn LIMIT ".$st.", ".$nh."";
  	$libdb->query($qry);
?>
<div class="style3" id="print_content">
<div class="module_sub_title">List of Available Book(s) :</div><br>
<font class="ar11_content"><strong><? echo "Total Records: " .$total_rows." book(s)"; ?></strong></font><br><br>
<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
  <tr> 
    <td class="m2_td_fieldname"><div align="center">No.</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Title</div></td>
    <td class="m2_td_fieldname"><div align="center">Publisher</div></td>
    <td class="m2_td_fieldname"><div align="center">Author</div></td>
    <td class="m2_td_fieldname"><div align="center">Date Added</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Available</div></td>
  </tr>
  <?
		if($libdb->num_rows() == 0) {
			if($dt_from > $dt_to){
				echo "<span class=\"errormsg\">The range of date is not valid</span><br><br>";
			}
			echo "<tr><td class=\"m2_td_content\" colspan=\"6\">No records</td></tr>";
		}
		else {
		$i = $st +1;
			while($libdb->next_record()) {
				$cp_book = "select count(*) from library_books_unit where book_recordid=".$libdb->record[0]." and book_status='y'";
				$lib_count->query($cp_book);
				$lib_count->next_record();
				$cp = $lib_count->record[0];
				
			echo"<tr> ";
			  echo"<td class=\"m2_td_content\">&nbsp;".$i."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;<a href=bookdetails.php?id=".$libdb->record[0].">".$libdb->record[1]."</a></td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[3]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[2]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[4],"d M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$cp."</td>";		  
			echo"</tr>";
			$i++;
			}
		  $this_page = $_SERVER['PHP_SELF']."?action=av_book&from=".$_REQUEST['from']."&to=".$_REQUEST['to']."";
	
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
</div>
<br>
<?
if ($total_rows > $nh) {
?>
<table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
	<tr> 
	  <td width="200" class="m2_td_content">
	  <div align="right">
	  <?
	  echo "Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
<?
}
?>
	<br>
	<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
<?
}

function book_list () {

	global $app_absolute_path,$root_images_folder;
	echo main_report();
	
	include("class.php");
	include_once($app_absolute_path."includes/functions.php");
	
	$libdb = new Modules_sql;
	$report = $_REQUEST['report'];
	$dt_from = date("Y-m-d", strtotime($_REQUEST["from"])); //Added by CB
	$dt_to = date("Y-m-d H:i:s", strtotime($_REQUEST["to"])); //Added by CB

	$qry_all = "select count(*) from library_books where date_added >= '".$dt_from."' and date_added <= '".$dt_to."'";
	$result = $libdb->query($qry_all);
	$row = $libdb->next_record();
	$total_rows = $libdb->record[0];
	
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 20);
	$page = ceil($total_rows/$nh);
	
  	$qry = "select library_books.book_recordid, library_books.book_title, library_books.book_publisher,library_books.book_author,";
	$qry .=" library_books.book_isbn, library_books.book_copies, library_books_unit.book_status, library_books_unit.accession_no";
	$qry .=" from library_books left join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid";
	$qry .=" where library_books.date_added >= '".$dt_from."' and library_books.date_added <= '".$dt_to."'";
	$qry .=" group by library_books.book_isbn LIMIT ".$st.", ".$nh."";
  	$libdb->query($qry);
?>
<div class="style3" id="print_content">
<div class="module_sub_title">List of Book(s) :</div><br>
<font class="ar11_content"><strong><? echo "Total Records: " .$total_rows." book(s)"; ?></strong></font><br><br>
<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
  <tr> 
    <td class="m2_td_fieldname"><div align="center">No.</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Title</div></td>
    <td class="m2_td_fieldname"><div align="center">Publisher</div></td>
    <td class="m2_td_fieldname"><div align="center">Author</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Code</div></td>
    <td class="m2_td_fieldname"><div align="center">Copies</div></td>
  </tr>
  <?
		if($libdb->num_rows() == 0) {
			if($dt_from > $dt_to){
				echo "<span class=\"errormsg\">The range of date is not valid</span><br><br>";
			}
			echo "<tr><td class=\"m2_td_content\" colspan=\"6\">No records</td></tr>";
		}
		else {
		$i = $st +1;
			while($libdb->next_record()) {
			echo"<tr> ";
			  echo"<td class=\"m2_td_content\">&nbsp;".$i."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;<a href=\"bookdetails.php?id=".$libdb->record[0]."\">".$libdb->record[1]."</a></td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[2]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[3]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[7]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[5]."</td>";		  
			echo"</tr>";
			$i++;
			}
		  $this_page = $_SERVER['PHP_SELF']."?action=book&from=".$_REQUEST['from']."&to=".$_REQUEST['to']."";
	
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
</div>
<br>
<?
if ($total_rows > $nh) {
?>
<table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
<tr> 
  <td width="200" class="m2_td_content">
  <div align="right">
  <?
  echo "Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
<?
}
?>
	<br>
	<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
<?
}

function book_returned () {

	global $app_absolute_path,$root_images_folder;
	echo main_report();
	
	include("class.php");
	include_once($app_absolute_path."includes/functions.php");
	$libdb = new Modules_sql;
	$report = $_REQUEST['report'];
	$dt_from = date("Y-m-d", strtotime($_REQUEST["from"]));
	$dt_to = date("Y-m-d", strtotime($_REQUEST["to"]));

	$qry_all = "select count(*) from library_issue where date_return >= '".$dt_from."' and date_return <= '".$dt_to."'";
	$result = $libdb->query($qry_all);
	$row = $libdb->next_record();
	$total_rows = $libdb->record[0];
	
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 20);
	$page = ceil($total_rows/$nh);
	
  	$qry_borrow = "select * from library_issue where date_return >= '".$dt_from."' and date_return <= '".$dt_to."'  LIMIT ".$st.", ".$nh."";
  	$libdb->query($qry_borrow);
?>
<div class="style3" id="print_content">
<div class="module_sub_title">List of Returned Book(s) :</div><br>
<font class="ar11_content"><strong><? echo "Total Records: " .$total_rows." book(s)"; ?></strong></font><br><br>
<table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
  <tr> 
    <td class="m2_td_fieldname"><div align="center">No.</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Code</div></td>
    <td class="m2_td_fieldname"><div align="center">Book Title</div></td>
    <td class="m2_td_fieldname"><div align="center">Date Issue</div></td>
    <td class="m2_td_fieldname"><div align="center">Date Returned</div></td>
    <td class="m2_td_fieldname"><div align="center">Member name</div></td>
  </tr>
  <?
		if($libdb->num_rows() == 0) {
			if($dt_from > $dt_to){
					echo "<span class=\"errormsg\">The range of date is not valid</span><br><br>";
				}
			echo "<tr><td class=\"m2_td_content\" colspan=\"6\">No records</td></tr>";
		}
		else {
		$i = $st +1;
			while($libdb->next_record()) {
			echo"<tr> ";
			  echo"<td class=\"m2_td_content\">&nbsp;".$i."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[1]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[6]."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[7], "j M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".DateConvert($libdb->record[9], "j M Y")."</td>";
			  echo"<td class=\"m2_td_content\">&nbsp;".$libdb->record[5]."</td>";
			echo"</tr>";
			$i++;
			}
		  $this_page = $_SERVER['PHP_SELF']."?action=return&from=".$_REQUEST['from']."&to=".$_REQUEST['to']."";
	
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
</div>
<br>
<?
if ($total_rows > $nh) {
?>
<table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
<tr> 
  <td width="200" class="m2_td_content">
  <div align="right">
  <?
  echo "Total Results:" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
<?
}
?>
	<br>
	<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
<?
}
?>
