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
$tablename 	= 'library_category';	
$filename 	= 'category.csv';

// READ FILE
$fd = fopen ($filename, "r");
while (!feof ($fd)) {
    $buffer = fgets($fd, 4096);
    $buffer = trim($buffer);
    $buffer = eregi_replace('\"','',$buffer);   	
    $buf = explode(',', $buffer);
    
   	// ASSIGN EXPLODED DATA INTO VARIABLES        
	$object['category_id '] = $buf[0]; 
    $object['category_name'] = ereg_replace(";", ",", $buf[1]); 
	$object['date_added'] = date("Y-m-d H:i:s");  
            
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
	$qry = "INSERT INTO $tablename ($column) VALUES ($values);";
	echo $qry.'<br>';
	mysql_query($qry);
	echo "success";
}
fclose ($fd);
?>