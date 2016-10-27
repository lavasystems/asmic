<?
session_start();

include_once("local_config.php");

require_once($app_absolute_path . "includes/functions.php");

if (!isAllowed(array(502, 503), $_SESSION['permissions'])){
  session_destroy();
  header("Location: ".$app_absolute_path."index.php");
  exit();
}
?>
<script type="text/javascript" src="expand.js"></script>
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
		
		  //$isbn = $_REQUEST['isbn'];
		  $id = $_REQUEST['id'];
		  $libdb = new Modules_sql;
		  $libdb_detail = new Modules_sql;
		  $qry_str = "select * from library_books where book_recordid ='".$id."'";
		  $libdb_detail->query($qry_str);
		  $libdb_detail->next_record();
		  $split = explode("|", $libdb_detail->record[11]);
	      $sub1 = $split[0];
	      $sub2 = $split[1];				
	      $sub3 = $split[2];
	  ?>
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
          <td>
<table width="100%" border="0" cellspacing="0" cellpadding="2">
              <tr> 
                <td>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr> 
                      <td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
                          <tr> 
                            <td class="module_sub_title">Book Details<br></td>
                            <td width="28%" rowspan="24" valign="top" bgcolor="#F0F0F0"><? include("pubrightbar.php"); ?></td>
                          </tr>
                          <tr> 
                            <td><div align="center"><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_reserve_step1.gif"></div><br></td>
                          </tr>
                          <tr> 
                            <td>&nbsp;</td>
                          </tr>
                          <tr> 
                            <td> <table width="100%" border="0" align="center" cellpadding="2" cellspacing="0">
                                <?
							  $libdb2 = new Modules_sql;
							  $qry_cat = "select * from library_category where category_id ='".$libdb_detail->record[1]."'";
							  $libdb2->query($qry_cat);
							  $libdb2->next_record();
							  
							?>
                                <tr> 
                                  <td width="30%" class="ar11_content" valign="top"> 
                                    <?
								  if(empty($libdb_detail->record[20])) {
								  	echo"<img src=\"".$app_absolute_path.$root_images_folder."/m2/m2_img_nopicture.gif\"><br>";
								  }
								  else {
								  ?>
                                    <img src="uploads/<? echo $libdb_detail->record[20]; ?>" border="1" align="center"><br> 
                                    <?
								  }
								  ?>
                                  </td>
                                  <td width="70%" class="ar11_content" valign="top"> 
                                    <?					
									$qry_all = "select count(*) from library_books_unit where book_recordid ='".$libdb_detail->record[0]."' and book_status='y'";
									$result = $libdb->query($qry_all);
									$row = $libdb->next_record();
									$total_rows = $libdb->record[0];
			
									$qry_book = "select library_books.book_recordid, library_books.book_isbn, library_books_unit.book_recordid,";
									$qry_book .=" library_books_unit.accession_no from library_books inner join library_books_unit on";
									$qry_book .=" library_books.book_recordid=library_books_unit.book_recordid where";
									$qry_book .=" library_books.book_recordid='".$libdb_detail->record[0]."' and";
									$qry_book .=" library_books_unit.book_status='y' limit 1";
									//echo $qry_book;
									$libdb->query($qry_book);
									if($libdb->num_rows() == 0) {
									?>
                                    <table width="223" height="101" border="0" cellpadding="0" cellspacing="0" id="Table_01">
                                      <tr> 
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_01.gif" width="16" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_02.gif" width="191" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_03.gif" width="16" height="15" alt=""></td>
                                      </tr>
                                      <tr> 
                                        <td background="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_04.gif"> 
                                          <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_04.gif" width="16" height="71" alt=""></td>
                                        <td valign="top" bgcolor="#CAE7A1"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr> 
                                              <td><strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_bookstatus.gif" width="76" height="12"></strong></td>
                                            </tr>
                                            <tr> 
                                              <td class="ar12_content">This book 
                                                is <strong>NOT AVAILABLE</strong> 
                                              </td>
                                            </tr>
                                            <tr> 
                                              <td>&nbsp;</td>
                                            </tr>
                                          </table></td>
                                        <td background="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_06.gif"> 
                                          <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_06.gif" width="16" height="71" alt=""></td>
                                      </tr>
                                      <tr> 
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_07.gif" width="16" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_08.gif" width="191" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_09.gif" width="16" height="15" alt=""></td>
                                      </tr>
                                    </table>
									<div class="ar11_content">
										<h5 class="m2_trigger"></h5>
										<div align="justify" style="width:95%">
											<? echo ereg_replace(";", ",", $libdb_detail->record[14]); ?><br><br>
											<a href="bookabstract.php?id=<? echo $_REQUEST['id']; ?>" target="name" onclick="window.open('bookabstract.php?id=<? echo $_REQUEST['id']; ?>','name','height=520,width=450,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no');">
											<img src="<?=$app_absolute_path?><?=$root_images_folder?>/icon_print.gif" border="0" alt="Print Summary"></a>
										</div>
									</div>
                                    <?
									}
									else {
									$libdb->next_record();
									?>
                                    <table width="223" height="101" border="0" cellpadding="0" cellspacing="0" id="Table_01">
                                      <tr> 
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_01.gif" width="16" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_02.gif" width="191" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_03.gif" width="16" height="15" alt=""></td>
                                      </tr>
                                      <tr> 
                                        <td background="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_04.gif"> 
                                          <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_04.gif" width="16" height="71" alt=""></td>
                                        <td valign="top" bgcolor="#CAE7A1"><table width="100%"  border="0" cellspacing="0" cellpadding="0">
                                            <tr> 
                                              <td><strong><img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_bookstatus.gif" width="76" height="12"></strong></td>
                                            </tr>
                                            <tr> 
                                              <td class="ar12_content">This book 
                                                is <strong>AVAILABLE</strong></td>
                                            </tr>
                                            <tr> 
                                              <td>&nbsp;</td>
                                            </tr>
                                            <tr> 
                                              <td>
											  <div align="center"> <a href="bookreserve.php?action=reserve&book_code=<? echo $libdb->record[3]; ?>"><img border="0" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_btn_clickreserve.gif" width="144" height="33"></a></div>
											  </td>
                                            </tr>
											<?
											if (is_array($permissions) && in_array(503, $_SESSION['permissions'])){ 
											?>
											<tr>
											  <td align="center" class="errormsg"><br>* Kindly register as a member to borrow this book</td>
											</tr>
											<?
											}
											?>
                                          </table></td>
                                        <td background="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_06.gif"> 
                                          <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_06.gif" width="16" height="71" alt=""></td>
                                      </tr>
                                      <tr> 
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_07.gif" width="16" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_08.gif" width="191" height="15" alt=""></td>
                                        <td> <img src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/m2_tblsta_09.gif" width="16" height="15" alt=""></td>
                                      </tr>
                                    </table>
									<div class="ar11_content">
										<h5 class="m2_trigger"></h5>
										<div align="justify" style="width:95%">
											<? echo ereg_replace(";", ",", $libdb_detail->record[14]); ?><br><br>
											<a href="bookabstract.php?id=<? echo $_REQUEST['id']; ?>" target="name" onclick="window.open('bookabstract.php?id=<? echo $_REQUEST['id']; ?>','name','height=520,width=450,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no');">
											<img src="<?=$app_absolute_path?><?=$root_images_folder?>/icon_print.gif" border="0" alt="Print Summary"></a>
										</div>
									</div>
                                    <?
									}
									?>
                                  </td>
                                </tr>
                              </table></td>
                          </tr>
                          <tr>
                            <td>&nbsp;</td>
                          </tr>
                          <tr> 
                            <td background="<?=$app_absolute_path?><?=$root_images_folder?>/separator2.gif" class="ar11_content">&nbsp;</td>
                          </tr>
                          <tr> 
                            <td height="25" class="ar11_content">
							<table width="100%"  border="0" cellspacing="1" cellpadding="2">
                                <!--<tr>
                                  <td width="26%"><span class="ar11_content"><strong>CATEGORY</strong></span></td>
                                  <td width="74%"><span class="ar11_content"><strong> 
                                    : <b><? echo $libdb2->record[2]; ?></b></strong></span></td>
                                </tr>--><!--
                                <tr> 
                                  <td><span class="ar11_content"><strong>BOOK 
                                    RACK </strong></span></td>
                                  <td><span class="ar11_content"><strong>: </strong><? echo $libdb_detail->record[22]; ?></span></td>
                                </tr>-->
                                <tr> 
                                  <td><span class="ar11_content"><strong>AUTHOR</strong></span></td>
                                  <td><span class="ar11_content"> <strong>: </strong><? echo ereg_replace(";", ",", $libdb_detail->record[3]); ?></span></td>
                                </tr>
                                <tr> 
                                  <td valign="top"><span class="ar11_content"><strong>TITLE</strong></span></td>
                                  <td><span class="ar11_content"> <strong>:</strong> 
                                    <? echo ereg_replace(";", ",", $libdb_detail->record[2]); ?></span></td>
                                </tr><!--
                                <tr> 
                                  <td><span class="ar11_content"><strong>EDITION</strong></span></td>
                                  <td><span class="ar11_content"> <strong>:</strong> 
                                    <? echo $libdb_detail->record[4]; ?></span></td>
                                </tr>-->
                                <tr> 
                                  <td><span class="ar11_content"><strong>PUBLICATION</strong></span></td>
                                  <td><span class="ar11_content"> <strong>:</strong> 
                                    <? echo $libdb_detail->record[5]; ?> / <? echo $libdb_detail->record[6]; ?> 
                                    </span></td>
                                </tr><!--
                                <tr> 
                                  <td><span class="ar11_content"><strong>DESCRIPTION</strong></span></td>
                                  <td><span class="ar11_content"> <strong>:</strong> 
                                    <? echo $libdb_detail->record[7]; ?> / <? echo $libdb_detail->record[8]; ?>&nbsp;cm 
                                    / <? echo $libdb_detail->record[9]; ?>&nbsp;pg</span></td>
                                </tr>--><!--
                                <tr> 
                                  <td><span class="ar11_content"><strong>INDEXES</strong></span></td>
                                  <td><span class="ar11_content"> <strong>:</strong> 
                                    <? echo $libdb_detail->record[10]; ?></span></td>
                                </tr>-->
                                <!--<tr>
                                  <td valign="top"><span class="ar11_content"><strong>SUBJECT</strong><br>
                                    &nbsp;&nbsp;</span></td>
                                  <td><span class="ar11_content"> 1) <? echo $sub1; ?><br>
                                    2) <? echo $sub2; ?><br>
                                    3) <? echo $sub3; ?></span></td>
                                </tr>-->
                                <!--<tr>
                                  <td><span class="ar11_content"><strong>EDITOR</strong></span></td>
                                  <td><span class="ar11_content"><strong>:</strong> 
                                    <? echo ereg_replace(";", ",", $libdb_detail->record[12]); ?></span></td>
                                </tr>-->
                                <!--<tr>
                                  <td><span class="ar11_content"><strong>SECOND 
                                    AUTHOR</strong></span></td>
                                  <td class="ar11_content"><strong>:</strong> 
                                    <? echo ereg_replace(";", ",", $libdb_detail->record[13]); ?></td>
                                </tr>-->
                                <tr>
                                  <td><span class="ar11_content"><strong>ISBN 
                                    NO</strong></span></td>
                                  <td class="ar11_content"><span class="ar11_content"> 
                                    <strong>:</strong> <? echo $libdb_detail->record[15]; ?>
                                    / <? echo $libdb_detail->record[16]; ?> copy(s)</span></td>
                                </tr>
                                <!--<tr>
                                  <td><span class="ar11_content"><strong>ISSN 
                                    NO</strong></span></td>
                                  <td><span class="ar11_content"> <strong>:</strong><? echo $libdb_detail->record[17]; ?> 
                                    / <? echo $libdb_detail->record[18]; ?> /
                                    <? echo $libdb_detail->record[19]; ?></span></td>
                                </tr>-->
								<? if(!empty($libdb_detail->record[23])) { ?>
								<tr>
                                  <td><span class="ar11_content"><strong>TABLE OF CONTENT</strong></span></td>
                                  <td class="ar11_content"><span class="ar11_content"><strong>:</strong>
								  <img align="absmiddle" src="<?=$app_absolute_path?><?=$root_images_folder?>/m2/pdf.gif">&nbsp;&nbsp;
								  <a target="_blank" href="toc/<? echo $libdb_detail->record[23]; ?>"><? echo $libdb_detail->record[23]; ?></a>
								  </span></td>
                                </tr>
								<? } ?>
                                <tr> 
                                  <td colspan="2" class="ar11_content"><div align="right" valign="middle"> 
                                    </div></td>
                                </tr>
                                <tr> 
                                  <td background="<? echo $app_absolute_path.$root_images_folder; ?>/separator2.gif" colspan="2" class="ar11_content">&nbsp;</td>
                                </tr>
                                <!-- <tr> 
                                  <td colspan="2" class="ar11_content">
                                    <small>Empty content</small>
                                  </td>
                                </tr> -->
                              </table></td>
                          </tr>
                        </table
						></td>
                    </tr>
                  </table>
				  </td>
		  </tr>
		</table>	
		  </td>
        </tr>
        <tr> 
          <td></td>
        </tr>
      </table></td>
  </tr>
  <tr> 
    <td>&nbsp;</td>
  </tr>
</table>
