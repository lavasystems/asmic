<?php
include("class.php");
$libdb = new Modules_sql;
$qry_back = "select * from library_books where book_isbn = '".$_REQUEST['isbn']."'";
$libdb->query($qry_back);

$libdb->next_record();

$cp = $_REQUEST['cp'];
$i =1;
echo $cp;

if(isset($_POST['submit'])) {
	
while($i<=$cp) {
	$code_a = $_POST['othercode'];
	$dt_added = date("Y-m-d H:i:s");
$i++;
}
	
	if($othercode) {
		  $qry_str = "insert into library_books (accession_no, category_id, book_title, book_author, book_edition, book_publisher, book_year,";
		  $qry_str .="book_illustration, book_height, book_page, book_publication, book_indexes, book_editor, book_elt, book_summary,";
		  $qry_str .="book_isbn, book_copies, book_issn, book_volume, book_number, book_image, book_status, date_added) values";
		  $qry_str .=" ('".$libdb->record[0]."','".$libdb->record[1]."','".$libdb->record[2]."','".$libdb->record[3]."','".$libdb->record[4]."',";
		  $qry_str .="'".$libdb->record[5]."','".$libdb->record[6]."','".$libdb->record[7]."','".$libdb->record[8]."','".$libdb->record[9]."',";
		  $qry_str .="'".$libdb->record[10]."','".$libdb->record[11]."','".$libdb->record[12]."','".$libdb->record[13]."','".$libdb->record[14]."',";
		  $qry_str .="'".$code_a."','".$libdb->record[16]."','".$libdb->record[17]."','".$libdb->record[18]."','".$libdb->record[19]."','".$libdb->record[20]."','NULL','".$dt_added."')";	
		  
		  if($libdb->query($qry_str)) {
		  echo'<meta http-equiv="refresh" content="0;URL=booklist.php?action=viewbook&isbn='.$accession_no.'">';
		  }
		  else {
		  	echo"<p><font color=\"red\">THE SUBMISSION COULD NOT BE PROCESSED DUE TO OUR SYSTEM ERROR!</font></p>";
		  }
	}
}

echo "<form action=\"".$PHP_SELF."\" method=\"post\" name=\"books\">";
while($i<=$cp) {
	echo "Book Code Unit ".$i." : <input name=\"othercode\" type=\"text\" size=\"15\" maxlength=\"50\"><br>";
	$ex_cp = $_POST['othercode'];
	echo $ex_cp;
$i++;
}
echo "<input name=\"submit\" type=\"submit\" id=\"submit\" value=\"Add\">";
echo "</form>";
?>
