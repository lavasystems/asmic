<?
/*
class : Document
*/
class Document {
	function __construct($doc_id=0) {
		if ($doc_id != 0)
			$this->initDocument($doc_id);			
	}

	private function initDocument($doc_id){	  	
		// initiate document properties
		$sql = "SELECT * FROM document_files";
		$sql .= " WHERE doc_id = $doc_id";

		$result = mysql_query($sql) or die("SELECT Failed".mysql_error());				

		$row = mysql_fetch_assoc($result);
		
		foreach ($row as $k => $v) {
			$this->{$k} = $v;
		}
		
		// initiate document indexing properties
		$this->jarLocation = JAR_PATH . "dms.jar";
		$this->propLocation = JAR_PATH;
	}

	// prerequisite : define doc_path
	function deleteDocument($doc_id=0){
		if ($doc_id==0)
			$doc_id = $this->doc_id;
		if (empty($this->doc_path)){
			return false;
		}
		$sql = "DELETE FROM document_files";
		$sql .= " WHERE doc_id = $doc_id";

		$result = mysql_query($sql);

		if ($result){
			return true;
		}
		else
			return false;
	}

	// prerequisite : define doc_path
	function deleteBackups(){
		if (empty($this->doc_path)){
			return false;
		}
		$sql = "SELECT * FROM document_files";
		$sql .= " WHERE doc_parentid = $this->doc_id";

		$result = mysql_query($sql);

		$backup_doc_ids = "";
		$id_count = 0;

		if ($result){
			while ($row=mysql_fetch_object($result)){
				if ($id_count == 0)
					$id_count++;
				else
					$backup_doc_ids .= ",";
				$backup_doc_ids .= $row->doc_id;
												
				//Remove backup document's index
				$objBackupDocument = new Document($row->doc_id);
				$objBackupDocument->RemoveIndex("backup/");
				
				//Remove the back up document physical file
				unlink($this->doc_path . "/backup/" . $row->doc_filename);
			}

			if (!empty($backup_doc_ids)){
				$sql = "DELETE FROM document_files WHERE doc_parentid = $this->doc_id";
				mysql_query($sql);
			}
		}
		return true;
	}

	function add(){
		$sql = "INSERT INTO document_files
				(doc_name, doc_filename, doc_description, doc_keywords
				, doc_size, doc_type, doc_createddate, doc_modifieddate, doc_fldid)
				VALUES ('". mysql_escape_string($this->doc_name) ."', '".mysql_escape_string($this->doc_filename)."'
				, '".mysql_escape_string($this->doc_description)."', '".mysql_escape_string($this->doc_keywords)."'
				, $this->doc_size, '$this->doc_type', NOW(), NOW(), $this->doc_fldid)";

		$result = mysql_query($sql);
		
		if ($result){
			$this->doc_id = mysql_insert_id();
			return true;
		}
		else
			return false;
	}

	function update(){
		$sql = "UPDATE document_files";
		$sql .= " SET doc_name='". mysql_escape_string($this->doc_name) . "'";
		$sql .= ", doc_description='". mysql_escape_string($this->doc_description) . "'";
		$sql .= ", doc_keywords='". mysql_escape_string($this->doc_keywords) . "'";
		$sql .= ", doc_modifieddate=NOW()";
		$sql .= ", doc_filename='". mysql_escape_string($this->doc_filename) . "'";		
		$sql .= " WHERE doc_id=$this->doc_id";
		
		$result = mysql_query($sql);

		if ($result){
			return true;
		}
		else
			return false;
	}

	function backup($doc_filename){
		$backup_doc_filename = Document::getFilename($this->doc_filename)."_". $this->doc_version . "." .$this->doc_type;
		
		$sql = "INSERT INTO document_files
				(doc_name, doc_filename
				, doc_description, doc_keywords
				, doc_size, doc_type
				, doc_version, doc_createddate, doc_modifieddate, doc_fldid, doc_parentid)
				VALUES ('". mysql_escape_string($this->doc_name) ."', '".mysql_escape_string($backup_doc_filename)."'
				, '".mysql_escape_string($this->doc_description)."', '".mysql_escape_string($this->doc_keywords)."'
				, $this->doc_size, '$this->doc_type'
				, $this->doc_version, '". $this->doc_createddate ."', '". $this->doc_modifieddate ."', $this->doc_fldid, $this->doc_id)";
		
		$result = mysql_query($sql);		
		
		
		if ($result){
			//return true;
			return(mysql_insert_id());
		}
		else{
			//return false;
			return 0;
		}	
	}

	function updateVersion($doc_filename){
		
		$sql = "UPDATE document_files
				 SET doc_name='". mysql_escape_string($doc_filename) ./*"'
				, doc_filename='". mysql_escape_string($this->doc_filename) ."'
				, doc_description='". mysql_escape_string($this->doc_description) ."'
				, doc_keywords='". mysql_escape_string($this->doc_keywords) ."*/"'
				, doc_type='$this->doc_type'
				, doc_size=$this->doc_size
				, doc_version=doc_version+1
				, doc_createddate=NOW()
				, doc_modifieddate=NOW() 
				 WHERE doc_id=$this->doc_id";
				
		$result = mysql_query($sql);

		if ($result){
			return true;
		}
		else
			return false;
	}

	function generateFilename($filename){
		$filename = stripslashes($filename);
		$filename = str_replace(" ", "_", $filename);
		$filename = str_replace("'", "", $filename);
		$filename = str_replace("\\", "", $filename);
		$filename = str_replace("/", "", $filename);
		$filename = str_replace(":", "", $filename);
		$filename = str_replace("*", "", $filename);
		$filename = str_replace("?", "", $filename);
		$filename = str_replace("\"", "", $filename);
		$filename = str_replace("<", "", $filename);
		$filename = str_replace(">", "", $filename);
		$filename = str_replace("|", "", $filename);
		$filename = strtolower($filename);
		return $filename;
	}

	static function getFilename($strFileName)
	{
		return substr( $strFileName , 0, strrpos( $strFileName, '.' ));
	}

	//static function checkDuplicateDocument($doc_name, $doc_filename, $doc_fldid){
  	static function checkDuplicateDocument($doc_filename, $doc_fldid, $doc_id=0){
		$sql = "SELECT doc_id FROM document_files";
		//$sql .= " WHERE doc_fldid = $doc_fldid AND (doc_name = '$doc_name' OR doc_filename = '$doc_filename')";
		$sql .= " WHERE doc_fldid = $doc_fldid AND doc_filename = '$doc_filename' AND doc_id != $doc_id";
		//$sql .= " WHERE doc_fldid = $doc_fldid AND doc_filename = '$doc_filename'";
		
		$result = mysql_query($sql);

		if ($result && mysql_num_rows($result) > 0) {
			$row = mysql_fetch_object($result);
			return $row->doc_id;
		}
		else
			return 0;
	}
		
	function AddIndex($strBackupPath = ""){
	
		// + sweewan 14042006
		// add in the jar java stuff
		java_require($this->jarLocation);
		// false used to append the current index to the existing index
		$simpleIndexing = new Java("indexing.SimpleIndexing", $this->propLocation);
		$action = new Java("util.Action");
		
		// get the pem_objid from the user session
		$pem_objid = $_SESSION['usr_grpid'];

		$folderIds = new Java("java.util.ArrayList");
		$folderIds->add($this->doc_fldid);
		$data = $simpleIndexing->createDocumentFieldValue($this->doc_description, $this->doc_keywords, $folderIds, $this->doc_name, $this->doc_id);
		
		$file = DOCUMENT_ROOT . Folder::getFolderPath($this->doc_fldid) . "/". $strBackupPath;		
		$file .= $this->doc_filename;		
		$filePath = new Java("java.io.File", $file);		
		$simpleIndexing->indexDocument($filePath, $data, $action->NEW);		
	}
	
	function UpdateIndex(){		

		// + sweewan 14042006
		// add in the jar java stuff
		java_require($this->jarLocation);
		// false used to append the current index to the existing index
		$simpleIndexing = new Java("indexing.SimpleIndexing", $this->propLocation);
		$action = new Java("util.Action");
		
		// get the pem_objid from the user session
		$pem_objid = $_SESSION['usr_grpid'];

		$folderIds = new Java("java.util.ArrayList");
		$folderIds->add($this->doc_fldid);
		$data = $simpleIndexing->createDocumentFieldValue($this->doc_description, $this->doc_keywords, $folderIds, $this->doc_name, $this->doc_id);
		
		$file = DOCUMENT_ROOT . Folder::getFolderPath($this->doc_fldid) . "/";		
		$file .= $this->doc_filename;		
		$filePath = new Java("java.io.File", $file);
		$simpleIndexing->indexDocument($filePath, $data, $action->UPDATE);
	}

	function RemoveIndex($strBackupPath = ""){	 			  	
		// + sweewan 14042006
		java_require($this->jarLocation);
		
		// false used to append the current index to the existing index
		$simpleIndexing = new Java("indexing.SimpleIndexing", $this->propLocation);
		$action = new Java("util.Action");

		$file = DOCUMENT_ROOT . Folder::getFolderPath($this->doc_fldid) . "/" . $strBackupPath;
		$file .= $this->doc_filename;
		$filePath = new Java("java.io.File", $file);

		$returnValues = $simpleIndexing->deleteIndex($filePath);				
	}
	
	function ContainVersions(){
		$sql = "SELECT doc_id FROM document_files";
		$sql .= " WHERE doc_parentid = ".$this->doc_id;

		$result = mysql_query($sql) or die("SELECT Failed".mysql_error());				
		if(mysql_num_rows($result) > 0)
			return true; //if this document contain versions
		else
			return false; //if this document don't have versions
	}
}
?>