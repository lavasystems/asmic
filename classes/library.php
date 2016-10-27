<?
/*
class : Library
*/
class Library {
	function __construct($init_setting=false) {
		if ($init_setting){
			$this->init_setting();
		}
	}

	private function init_setting () {
		$library = mysql_query("SELECT issue_period, fine, cards_issued FROM library_settings LIMIT 1");
		$t_library = mysql_fetch_array($library);
		foreach ($t_library as $field=>$value){
			$this->{$field} = $value;
		}
	}
}
?>