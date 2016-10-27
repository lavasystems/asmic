<?
/*
class : Group
*/

class Group {
	function __construct($grp_id=0) {
		if ($grp_id != 0)
			$this->initGroup($grp_id);
	}
	
	private function initGroup($grp_id){
		// init group variables
		$sql = "SELECT * FROM user_groups";
		$sql .= " WHERE grp_id = $grp_id";

		$result = mysql_query($sql);

		$row = mysql_fetch_assoc($result);
		foreach ($row as $k => $v) {
			$this->{$k} = $v;
		}
	}

	function checkDuplicateGroup($groupname){
		$sql = "SELECT grp_id FROM user_groups";
		$sql .= " WHERE grp_name = '".mysql_escape_string($groupname)."'";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0){
			$row = mysql_fetch_object($result);
			return $row->grp_id;
		}
		else
			return 0;
	}

	static function deleteGroup($grp_id, $new_grpid=0){
		$sql = "UPDATE user_users";
		$sql .= " SET usr_grpid = $new_grpid";
		$sql .= " WHERE usr_grpid = $grp_id";

		mysql_query($sql);

		$sql = "DELETE FROM user_groups";
		$sql .= " WHERE grp_id = $grp_id";

		mysql_query($sql);

		if (mysql_affected_rows() > 0){
			return true;
		}
		else
			return false;
	}
}
?>