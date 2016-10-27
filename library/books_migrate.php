<?
/*-------------------------------------------------------------------------------------------
TITLE: Upload CSV to MySQL 
IMPORTANT STEPS: 
1.	Define table name at $tablename
2.	Define CSV filename at $filename
3.	Assign variables to be inserted
4.	Define INSERT query at $query
---------------------------------------------------------------------------------------------*/

// INCLUDE CLASSESS DEFINE BY NF (need to define full path if running cronjob)
//require('file:///D|/ASM/migration%20script/config.inc');	
//include('class.php');
//
//$wsHandle = new Website($config['websiteId']);
//$dbHandle = $wsHandle->WebsiteDatabaseServerAccounts();
//$DBIndex = 0;

//CONNECTION TO DB CODE
$link = mysql_connect('127.0.0.1:3306', 'asmic', 'asmic123');
mysql_select_db('asmic', $link);


// DEFINE VARIABLES
$tablename 	= 'library_books';	
$filename 	= 'book_record.csv';

// READ FILE
$fd = fopen ($filename, "r");
while (!feof ($fd)) {
    $buffer = fgets($fd, 4096);
    $buffer = trim($buffer);
    $buffer = eregi_replace('\"','',$buffer);
    $buf = explode(',', $buffer);
    
	$today = date("Y-m-d H:i:s");
   	// ASSIGN EXPLODED DATA INTO VARIABLES        
	$object['category_id'] = $buf[0];  
	$object['book_callno'] = $buf[1];
    $object['book_author'] = ereg_replace(";", ",", $buf[2]);
	$object['book_title '] = ereg_replace(";", ",", $buf[3]); 
    $object['book_publisher'] = ereg_replace(";", ",", $buf[4]);  
    $object['book_illustration'] = $buf[5];
	$object['book_height '] = $buf[6]; 
    $object['book_page'] = $buf[7];  
    $object['book_subject'] = ereg_replace(";", ",", $buf[8]);
	$object['book_year '] = $buf[9]; 
    $object['book_edition'] = $buf[10];  
    $object['book_indexes'] = $buf[11];
	$object['book_editor '] = ereg_replace(";", ",", $buf[12]); 
    $object['book_elt'] = ereg_replace(";", ",", $buf[13]); 
    $object['book_summary'] = ereg_replace(";", ",", $buf[14]);
	$object['book_isbn'] = $buf[15];
	$object['book_copies'] = $buf[16];
	$object['book_issn'] = $buf[17];
	$object['book_volume'] = $buf[18];		
	$object['book_number'] = $buf[19];	
	$object['date_added'] = $today;  
         
	$copy = $buf[16];
	$isbn = $buf[15];
    // INSERT DATA INTO TABLE
    reset($object);
	$column = '';
	$values = '';
	while (list($key, $val) = each($object)) {
		if ($key > 0) continue;
		if (!empty($column)) {
			$column .= ', ';
			$values .= ', ';
		}
		$column .= $key;
		$values .= "'".addslashes(trim($val))."'";
	}
	$query = "INSERT INTO $tablename ($column) VALUES ($values);";
	echo $query.'<br>';
	if(mysql_query($query)) {
		$id_record = mysql_insert_id();
			$c = 0;
			while($c<$copy) {
				$aplh = $c+1;
				$qry_unit = "insert into library_books_unit(book_recordid, accession_no, book_status, date_added) values";
				$qry_unit .="('".$id_record."', '".$isbn."-".$aplh."', 'y', '".$today."')";
				mysql_query($qry_unit);
				echo "unit :".$copy."<br>";
				echo "kueri unit :".$qry_unit."<br>";
				$c++;
			}
		
	}
}
fclose ($fd);
?>