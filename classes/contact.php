<?
/*
class : Contact
*/
class Contact {
	function __construct($id=0) {
		$this->id = $id;
//		if ($usr_id != 0)
//			$this->initUser($usr_id);
	}

	static function getName($id){
		// init user variables
		$sql = "SELECT fullname, title FROM contact_contact";
		$sql .= " WHERE id = $id";

		$result = mysql_query($sql);

		$row = mysql_fetch_object($result);
		return $row->title . " " . $row->fullname;
	}
	
	static function deleteContact($id){
/*
		$sql = "DELETE FROM contact_address WHERE id=$id";
		mysql_query($sql);

		$sql = "DELETE FROM contact_email WHERE id=$id";
		mysql_query($sql);

		$sql = "DELETE FROM contact_contribution WHERE id=$id";
		mysql_query($sql);

		$sql = "DELETE FROM contact_contact WHERE ID=$id LIMIT 1";
		mysql_query($sql);
*/
		$sql = "UPDATE contact_contact SET delflag = 1 WHERE ID=$id LIMIT 1";
		mysql_query($sql);

//		$sql = "OPTIMIZE TABLE contact_contact";
//		mysql_query($sql);

		return true;
	}
}
?>