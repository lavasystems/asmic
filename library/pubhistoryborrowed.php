<?
session_start();
$id = $_SESSION['usr_id'];
//$_SESSION['usr_username']; //Unused, commented - Ganee
include_once("local_config.php");

require_once($app_absolute_path . "includes/functions.php");

if (!isAllowed(array(501,502), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}

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
<?
include_once("local_config.php");
include_once($app_absolute_path."includes/template_header.php");
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td valign="top">
      <?
		include("class.php");
		include_once($app_absolute_path."includes/functions.php");
		include_once($app_absolute_path."classes/audit_trail.php");
	  ?>
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td width="4%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="24" height="8"></td>
          <td colspan="2">&nbsp;</td>
          <td width="1%" rowspan="15"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" width="12" height="8"></td>
        </tr>
        <tr> 
          <td colspan="2" class="module_title"><? include("breadcrumb.php"); ?></td>
        </tr>
        <tr> 
          <td colspan="2" background="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/separator.gif" width="2" height="13"></td>
        </tr>
        <tr> 
          <td colspan="2"> <? include("body_nav.php"); ?> </td>
        </tr>
        <tr> 
          <td valign="top">
<div class="module_sub_title">History Of Borrowed Book(s)<br>
              <?
		  	global $app_absolute_path,$root_images_folder;
			$libdb = new Modules_sql;
			$libdb_info = new Modules_sql;
			$id = $_REQUEST['id'];
			
			$qry_all = "select count(*) from library_issue";
			$qry_all .=" where contact_id =".$id." and approve='Yes' and issue_status=5";
			$result = $libdb->query($qry_all);
			$row = $libdb->next_record();
			$total_rows = $libdb->record[0];
				
			$st = requestNumber($_REQUEST['st'], 0);
			$nh = requestNumber($_REQUEST['nh'], 20);
			$page = ceil($total_rows/$nh);
			
			$qry = "select accession_no, book_title, date_issue, date_return from library_issue where";
			$qry .= " contact_id = ".$id." and approve='Yes' and issue_status=5 order by library_issue.date_added DESC LIMIT ".$st.", ".$nh."";
			$libdb->query($qry);
		?>
            </div>
            <table width="100%" border="0" cellspacing="0" cellpadding="1">
		      <tr> 
                <td>
				<div class="style3" id="print_content">
                    <table width="100%" border="0" align="center" cellpadding="1" cellspacing="0">
                      <tr> 
                        <td width="100%" class="ar11_content">&nbsp;</td>
                      </tr>
                      <tr> 
                        <td class="ar11_content">Below are the history of book(s) borrowed by <?=$_SESSION['usr_username']?>.</td>
                      </tr>
                      <tr> 
                        <td class="ar11_content">&nbsp;</td>
                      </tr>
                      <tr> 
                        <td><table width="100%"  border="0" cellpadding="0" cellspacing="1" class="m2_table_outline">
                            <tr> 
                              <td width="8%" align="center" class="m2_td_fieldname">No.</td>
                              <td width="20%" class="m2_td_fieldname">Book Code</td>
                              <td width="35%" class="m2_td_fieldname">Book Title</td>
                              <td width="15%" class="m2_td_fieldname">Date Issue</td>
                              <td width="15%" class="m2_td_fieldname">Date Return</td>
                            </tr>
                            <?
						if($libdb->num_rows() == 0) {
							echo "<tr><td class=\"m2_td_content\" colspan=\"5\">No records</td></tr>";
						}
						else {
						$i = $st+1;
						while($libdb->next_record()) {
					  ?>
                            <tr> 
                              <td align="center" class="m2_td_content" valign="top"><? echo $i; ?></td>
                              <td class="m2_td_content" valign="top"><? echo $libdb->record[0]; ?></td>
                              <td class="m2_td_content" valign="top"><? echo $libdb->record[1]; ?></td>
                              <td class="m2_td_content" valign="top"><? echo DateConvert($libdb->record[2], "j M Y"); ?></td>
                              <td class="m2_td_content" align="center"> 
                                <? 
								if($libdb->record[3]=='0000-00-00') {
									echo "On-Loan";
									echo "<br><a href=\"#\">Renew Loan</a>";
								}
								elseif(($libdb->record[3] == '0000-00-00') && ($libdb->record[3] < date("Y-m-d"))) {
									echo "<font color=\"red\"><b>Overdue</b></font>";
									echo "<br><a href=\"#\">Renew Loan</a>";									
								}
								else {
									echo "Returned"; 
								}
								?>
                              </td>
                            </tr>
                            <?
					  $i++;
					  }
					  $this_page = $_SERVER['PHP_SELF']."?id=".$id."";
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
                         </td>
                      </tr>
                      <tr>
                        <td><img src="<?=$app_absolute_path?><?=$root_images_folder?>/spacer.gif" height="3" width="1"></td>
                      </tr>
                      <tr>
                        <td>
						<?
						if($total_rows > $nh) {
						?>
                          <table width="350"  border="0" align="right" cellpadding="0" cellspacing="1">
							<tr> 
							  <td width="200" class="m2_td_content">
							  <div align="right">
							  <?
							  echo "Total Book(s):" .$total_rows.". Page ".(ceil($st/$nh)+1)." of ".$page;
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
						</td>
                      </tr>
                      <tr> 
                        <td>&nbsp;</td>
                      </tr>
                    </table> 
			  </div>
			  </td>
		  </tr>
		  <tr>
			<td>
				<input type="image" onClick="javascript:Clickheretoprint()" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_print.gif">
				<a href="pubcategory.php"><img border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_back.gif"></a>
			</td>
		  </tr>
		</table>
		  </td>
         <td width="200" valign="top" bgcolor="#F0F0F0"><? include("pubrightbar.php"); ?></td>
        </tr>
      </table>
	  </td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
<?
include_once($app_absolute_path."includes/template_footer.php");
?>