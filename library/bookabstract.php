<html>
<head>
<title>ASMIC : Book's Abstact</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<link href="../css/style.css" rel="stylesheet" type="text/css">
<body onLoad="window.print();">
<?php
//error_reporting(E_ALL); 
ini_set("display_errors", 0);
include_once("local_config.php");
include("class.php");
include_once($app_absolute_path."includes/functions.php");
$id = $_REQUEST['id'];
$libdb = new Modules_sql;
$qry_str = "select book_author,book_title,book_summary from library_books where book_recordid ='".$id."'";
$libdb->query($qry_str);
$libdb->next_record();
?>
<table width="90%" border="0" cellspacing="0" cellpadding="4">
  <tr>
    <td><table width="100%" border="0" cellspacing="0" cellpadding="0" class="m2_table2_outline">
        <tr> 
          <td colspan="2" class="module_sub_title">Book Abstract:</td>
        </tr>
		<tr> 
          <td colspan="2" class="ar11_content">&nbsp;</td>
        </tr>
		<tr> 
          <td valign="top" class="ar11_content"><strong>Title:</strong></td>
          <td class="ar11_content"><?php echo $libdb->record[1]; ?></td>
        </tr>
        <tr> 
          <td width="15%" valign="top" class="ar11_content"><strong>Author:</strong></td>
          <td width="85%" class="ar11_content"><?php echo $libdb->record[0]; ?></td>
        </tr>
        <tr> 
          <td valign="top" class="ar11_content"><strong>Abstract:</strong></td>
          <td class="ar11_content"><?php echo $libdb->record[2]; ?></td>
        </tr>
		<tr> 
          <td colspan="2" valign="top"></td>
        </tr>
        <tr> 
          <td colspan="2" valign="top"></td>
        </tr>
      </table>
	  </td>
  </tr>
  <tr>
  	<td class="ar11_content"><a href="#" class="btn btn-default" onclick="self.close();return false;">Close Window</a></td>
  </tr>
</table>
</body>
</html>
