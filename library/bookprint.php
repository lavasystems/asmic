<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
include_once("local_config.php");
include("class.php");
include($app_absolute_path."includes/functions.php");
$id = $_REQUEST['id'];

$libdb = new Modules_sql;
$qry_book = "select library_books.*, library_books_unit.* from library_books inner join library_books_unit on library_books.book_recordid=library_books_unit.book_recordid";
$qry_book .=" where library_books_unit.accession_no = '".$id."'";
$libdb->query($qry_book);
$libdb->next_record();

 $split = explode("|", $libdb->record[11]);
 $sub1 = $split[0];
 $sub2 = $split[1];
 $sub3 = $split[2];
?>
<html>
<head><title>Book Details</title></head>
<body onLoad="window.print()">
<?
//ASM A4 Paper - Print Header Space
/*************************************************************/
echo(file_get_contents("../includes/print_header_space.php"));
/*************************************************************/
?>
<link href="<?php echo $app_absolute_path ?>css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $app_absolute_path ?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
<link href="<?php echo $app_absolute_path ?>css/bootstrap-theme.css" rel="stylesheet" type="text/css">
<link href="<?php echo $app_absolute_path ?>css/style.css" rel="stylesheet" type="text/css">
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="5">
        <tr> 
          <td colspan="2"><div align="center"><br>
              ASM CATALOGING-IN-PUBLICATION DATA<br>
              <br>
              <br>
            </div></td>
        </tr>
		 <tr> 
          <td colspan="2">Book Rack&nbsp;:&nbsp;<u><? echo $libdb->record[22]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">100 10&nbsp;:&nbsp;<u><? echo $libdb->record[3]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">245 10&nbsp;:&nbsp;<u><? echo $libdb->record[2]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">250 00&nbsp;:&nbsp;<u><? echo $libdb->record[4]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">260 00 :&nbsp;<u><? echo $libdb->record[5]; ?></u>&nbsp;/ 
            <u><? echo $libdb->record[6]; ?></u>&nbsp;Y</td>
        </tr>
        <tr> 
          <td colspan="2">300 00 : <u><? echo $libdb->record[7]; ?></u> /<u> <? echo $libdb->record[8]; ?></u> 
            cm / <u><? echo $libdb->record[9]; ?></u> p</td>
        </tr>
        <tr> 
          <td colspan="2">500 00&nbsp;:&nbsp;<u><? echo $libdb->record[10]; ?></u></td>
        </tr>
        <tr> 
          <td width="11%">600 00&nbsp;:&nbsp;</td>
          <td width="89%">1) <u><? echo $sub1; ?></u></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="6">2) <u><? echo $sub2; ?></u></td>
        </tr>
        <tr> 
          <td>&nbsp;</td>
          <td colspan="6">3) <u><? echo $sub3; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">700 00&nbsp;:&nbsp;<u><? echo $libdb->record[12]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">700 10&nbsp;:&nbsp;<u><? echo $libdb->record[13]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">800 00 : <u><? echo $libdb->record[15]; ?></u> / <u><? echo $libdb->record[16]; ?></u> 
            q</td>
        </tr>
        <tr> 
          <td colspan="2">800 10 : <u><? echo $libdb->record[17]; ?></u> / <u><? echo $libdb->record[18]; ?></u> / <u><? echo $libdb->record[19]; ?></u></td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
        <tr> 
          <td colspan="2">&nbsp;</td>
        </tr>
      </table></td>
  </tr>
</table>
</body>
</html>