<?php
class MailMerge 
{
	private $mm_data_dir;
	private $obj;
	private $datasource_file = 'ds.doc';
	private $header_file = 'header.doc';
	private $fieldcnt;
	private $rowcnt;
	private $template;
	private $visible = False;
	private $list;

	public function __construct($list = NULL, $data_dir = 'data') 
	{
		// this is the path to your data dir.
		//$this->mm_data_dir = 'E:/Inetpub/wwwroot/csaf/'.$data_dir;
		$this->mm_data_dir = $data_dir;
		$this->list = $list;
	}

	public function Execute() 
	{
		$this->initilize();
		if( count( $this->list ) > 0 ) 
		{
			if(isset($this->template)) 
			{
				$this->CreateHeaderFile();
				$this->CreateDataSource();
				$this->CreateDocument($this->template);
				return TRUE;
			}
		} else return FALSE;
	}
	
	public function setList($list = NULL) 
	{
		if(is_array($list))
		$this->list = $list;
	}
	
	public function Template($template = NULL) 
	{
		if(is_array($template))
		$this->template = $template;
	}

	public function __destruct() 
	{
		//remove the temp files
		unlink($this->mm_data_dir.'/Temp/'.$this->datasource_file);
		unlink($this->mm_data_dir.'/Temp/'.$this->header_file);
		$this->Quit();
	}

	private function initilize() 
	{
		$this->rowcnt = count($this->list);
		$this->fieldcnt = count($this->list[0]);
		$this->obj = new COM("word.application") or die("Unable to instanciate Word");
		$this->obj->Visible = $this->visible;
		Logger::log('Word -> Application Opened.');
	}

	private function Quit() 
	{
		$this->obj->Quit();
		Logger::log('Word -> Application Quit.');
	}

	private function CreateHeaderFile() {
		$this->obj->Documents->Add();
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Added.');
		Logger::log('Word -> rowcnt = '.$this->rowcnt);
		Logger::log('Word -> fieldcnt = '.$this->fieldcnt);
		
		
		$this->obj->ActiveDocument->Tables->Add($this->obj->Selection->Range,1,$this->fieldcnt);
		foreach($this->list[0] as $key => $value) {
			$this->obj->Selection->TypeText($key);
			$this->obj->Selection->MoveRight();
		}

		$this->obj->ActiveDocument->SaveAs($this->mm_data_dir.'/Temp/'.$this->header_file);
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Saved.');
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Closed.');
		$this->obj->ActiveDocument->Close();
	}

	private function CreateDataSource() {
		$this->obj->Documents->Add();
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Added.');
		$this->obj->ActiveDocument->Tables->Add($this->obj->Selection->Range,$this->rowcnt,$this->fieldcnt);

		for($i = 0; $i < $this->rowcnt; $i++) {
			foreach($this->list[$i] as $key => $value) {
				$this->obj->Selection->TypeText($value);
				$this->obj->Selection->MoveRight();
			}
		}
		$this->obj->ActiveDocument->SaveAs($this->mm_data_dir.'/Temp/'.$this->datasource_file);
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Saved.');
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Closed.');
		$this->obj->ActiveDocument->Close();
	}

	private function CreateDocument($template)
	 {
		$this->obj->Documents->Open($this->mm_data_dir.'/Templates/'.$template[0].'.dot');
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Opened.');

		Logger::log('Word -> Opening Header Source.');
		$this->obj->ActiveDocument->MailMerge->OpenHeaderSource($this->mm_data_dir.'/Temp/'.$this->header_file);
		Logger::log('Word -> Opening Data Source.');
		$this->obj->ActiveDocument->MailMerge->OpenDataSource($this->mm_data_dir.'/Temp/'.$this->datasource_file);
		Logger::log('Word -> Executing Merge.');
		$this->obj->ActiveDocument->MailMerge->Execute();
		$this->obj->ActiveDocument->SaveAs($this->mm_data_dir.'/'.$template[1].'.doc');
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Saved.');
		Logger::log('Word -> '.$this->obj->ActiveDocument->Name().' Document Closed.');
		$this->obj->Documents($template[0].'.dot')->Close();
		$this->obj->Documents($template[1].'.doc')->Close();
		//$this->obj->ActiveDocument->Close();
	}
}
?>