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
$tablename 	= 'contact_expertise';	
$filename 	= 'ex_data.csv';

// READ FILE
$fd = fopen ($filename, "r");
while (!feof ($fd)) 
{
    $buffer = fgets($fd, 4096);
    $buffer = trim($buffer);
    $buffer = eregi_replace('\"','',$buffer);   	
    $buf = explode(',', $buffer);
    
   	// ASSIGN EXPLODED DATA INTO VARIABLES        
    $object['area_id'] 		= $buf[0]; 
    $object['area_name'] 		= $buf[1];  
    $object['area_desc'] = $buf[2];
         
    // INSERT DATA INTO TABLE
    reset($object);
	$column = '';
	$values = '';
	while (list($key, $val) = each($object)) 
	{
		if ($key > 0) continue;
		if (!empty($column)) 
		{
			$column .= ', ';
			$values .= ', ';
		}
		$column .= $key;
		$values .= "'".addslashes(trim($val))."'";
	}
	$query = "INSERT INTO $tablename ($column) VALUES ($values);";
	echo $query.'<br>';
//	$dbHandle[$DBIndex]->RunQuery($query);
	mysql_query($query);
}
fclose ($fd);
?>