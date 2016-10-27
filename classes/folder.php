<?
/*
class : Folder
*/
class Folder {
	function __construct($fld_id=0) {
		if ($fld_id != 0)
			$this->initFolder($fld_id);
	}

	private function initFolder($fld_id){
		// init user variables
		$sql = "SELECT * FROM document_folders";
		$sql .= " WHERE fld_id = $fld_id";
		$result = mysql_query($sql);

		$row = mysql_fetch_assoc($result);
		foreach ($row as $k => $v) {
			$this->{$k} = $v;
		}
	}

	function add(){
		$sql = "INSERT INTO document_folders
				 (fld_name, fld_createddate, fld_modifieddate, fld_parentid)
				 VALUES ('".mysql_escape_string($this->fld_name)."', NOW(), NOW(), $this->fld_parentid)";
		
		$result = mysql_query($sql);
		if ($result){
			$this->fld_id = mysql_insert_id();
			return true;
		}
		else
			return false;
	}

	function update(){
		$sql = "UPDATE document_folders";
		$sql .= " SET fld_name='". mysql_escape_string($this->fld_name). "'";
		$sql .= ", fld_modifieddate=NOW()";
		$sql .= " WHERE fld_id=$this->fld_id";
		
		$result = mysql_query($sql);

		if ($result)
			return true;
		else
			return false;
	}

	static function deleteFolder($fld_id){
		$sql = "DELETE FROM document_permissions";
		$sql .= " WHERE pem_fldid = $fld_id";

		$result = mysql_query($sql);

		if ($result){

			$sql = "DELETE FROM document_folders";
			$sql .= " WHERE fld_id = $fld_id";
	
			$result = mysql_query($sql);
	
			if ($result)
				return true;
			else
				return false;
		}
		else
			return false;
	}

	static function checkDuplicateFolder($fld_name, $fld_parentid){
		$sql = "SELECT fld_id FROM document_folders";
		$sql .= " WHERE fld_name = '$fld_name' AND fld_parentid = $fld_parentid";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0){
			return true;
		}
		else
			return false;
	}
	
	static function getFolderPath($parentid){
		$arrFolderPath = array();
		$arrFolderPath = Folder::recurseFolder($parentid, $arrFolderPath);
		$folderPath = implode("/", array_reverse($arrFolderPath));
		return $folderPath;
	}
	
	static function recurseFolder($parentid, $arrFolderPath){
		$sql = "SELECT * FROM document_folders WHERE fld_id = $parentid";
		$result = mysql_query($sql);
		if ($result){
			$row = mysql_fetch_object($result);
			$parent_parentid = $row->fld_parentid;
			array_push($arrFolderPath, $row->fld_name);
			if ($parent_parentid > 0)
				$arrFolderPath = Folder::recurseFolder($parent_parentid, $arrFolderPath);
		}
//		else 
//			echo(mysql_error());
		return $arrFolderPath;
	}

	static function getFolderPathArray($parentid){
		$arrFolderPath = array();
		$arrFolderPath = Folder::recurseBreadcrumbFolder($parentid, $arrFolderPath);
		$arrReversedFolderPath = array_reverse($arrFolderPath);
		return $arrReversedFolderPath;
	}

	static function recurseBreadcrumbFolder($fld_id, $arrFolderPath){
		$sql = "SELECT * FROM document_folders WHERE fld_id = $fld_id";
		$result = mysql_query($sql);
		if ($result){
			$row = mysql_fetch_object($result);
			$fld_parentid = $row->fld_parentid;
			array_push($arrFolderPath, array('fld_id'=>$row->fld_id, 'fld_name'=>$row->fld_name));
			if ($fld_parentid > 0)
				$arrFolderPath = Folder::recurseBreadcrumbFolder($fld_parentid, $arrFolderPath);
		}
		return $arrFolderPath;
	}

	function addPermission($pem_objtype, $pem_objid, $arr_pem){

		$sql = "INSERT INTO document_permissions
				 (pem_fldid, pem_objtype, pem_objid, pem_read, pem_write, pem_createddate)
				 VALUES ($this->fld_id, $pem_objtype, $pem_objid, ".$arr_pem['pem_read'].", ".$arr_pem['pem_write'].", NOW())";

		$result = mysql_query($sql);

		if ($result)
			return true;
		else
			return false;
	}

	function updatePermission($pem_id, $pem_objtype, $pem_objid, $arr_pem){

		$sql = "UPDATE document_permissions
				 SET pem_objtype = $pem_objtype, pem_objid = $pem_objid
				 , pem_read = ".$arr_pem['pem_read'].", pem_write = ".$arr_pem['pem_write']."
				 , pem_modifieddate = NOW()
				 WHERE pem_id = $pem_id";

		$result = mysql_query($sql);

		if ($result)
			return true;
		else
			return false;
	}

	// delete folder permission
	function deletePermission($pem_id){

		$sql = "DELETE FROM document_permissions
				 WHERE pem_id = $pem_id";

		$result = mysql_query($sql);

		if ($result) 
			return true;
		else
			return false;
	}

	function getUserPermission($usr_id=0, $usr_grpid=0){
		$fld_id = 0;

		if (!empty($this->fld_id))
			$fld_id = $this->fld_id;

		$sql = "SELECT * FROM document_permissions";
		$sql .= " WHERE pem_fldid = ". $fld_id;
		if (!isAllowed(array(101, 201), $_SESSION['permissions']))
			$sql .= " AND ((pem_objtype=0 AND pem_objid=".$_SESSION['usr_grpid'].") OR (pem_objtype=1 AND pem_objid=".$_SESSION['usr_id']."))";

		$arr_permission = array();

		$result = mysql_query($sql);
	
		if ($result){
			if ($row = mysql_fetch_object($result))
				array_push($arr_permission, $row->pem_read, $row->pem_write);
		}
		return $arr_permission;
	}
}
?>