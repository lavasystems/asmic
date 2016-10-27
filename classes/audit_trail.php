<?php
class audit_trail {
	function __construct() {

	}

	function writeFileLog ($user, $module, $action, $objid=0){
		$audit_trail_log_path = realpath("log/audit_trail/");
		$dtime = date('r');
		$filename = date('y').date('m').date('d');
		$sessionid = session_id();

		$entry_line = "$dtime|$user|$module|$action|$sessionid\r\n";
		$fp = fopen($audit_trail_log_path."/".$filename.".log", "a");
		fputs($fp, $entry_line);
		fclose($fp);
	}

    function writeDbLog($user, $module, $action, $objid=0){
		$sessionid = session_id();
		$action = mysql_escape_string($action);
        $query = "INSERT INTO user_audittrail (date_modified, user, module, action, sessionid, doc_id) VALUES (NOW(), '$user', '$module', '$action', '$sessionid', '$objid')";
        $result = mysql_query($query) or die("Query failed : audit_trail = " . mysql_error());

//        mysql_free_result($result);
    }
	
	function writeLog($user, $module, $action, $objid=0){
		$this->writeFileLog($user, $module, $action);
		$this->writeDbLog($user, $module, $action, $objid);
	}
}

?>
