<?php
/*************************************************************
 *  THE ADDRESS BOOK  :  version 1.04
 *  
 *  lib/class-contactlist.php
 *  Object: Retrieves information relating to the contact list to be displayed.
 *
 *************************************************************/

// Original code by hannelore
// Edited by DinMundo
 
class ContactList 
{
	
	var $group_id;
	var $group_name;
	var $current_letter;
	var $max_entries;
	var $current_page;
	var $total_pages;
	var $sql; 
	var $title;
	var $nav_menu;
	
	function ContactList() 
	{
		global $options;
		
		// DEPENDENT VARIABLES -- Values for these variables are passed to the object after ContactList is created
		// If no values are provided, then it uses some defaults
		$this->group_id = 0;                       // defaults to 0 upon creation of object
		$this->current_page = 1;                   // defaults to first page
		$this->current_letter = $options->defaultLetter;	// defaults to value set in options
		$this->max_entries = $options->limitEntries; 		// defaults to value set in options; 0=no maximum (display all on page 1)

		// RESULTANT VARIABLES -- Values for these variables start out blank and will be filled in by this object's methods
		$this->group_name = "";                    // determined in $this->group_name()
		$this->total_pages = 1;                    // total # of pages, determined in $this->retrieve()
		$this->sql = "";                           // determined in $this->retrieve(), useful for debugging purposes
		$this->title = "";                         // determined in $this->title()
		$this->nav_menu = "";                      // determined in $this->create_nav()
	}
	
	function group_name() 
	{
		global $db_link;
		global $lang;
		
		// OBTAIN NAME OF GROUP IN DISPLAYED LIST
		// Force $this->group_id to an integer equal to 0 or greater
		$this->group_id = intval($this->group_id);
		if ($this->group_id <= 0) $this->group_id = 0;
		
		// group_id = 0 --> "All Entries"
		if ($this->group_id == 0) $this->group_name = $lang['GROUP_ALL_LABEL'];
		// group_id = 1 --> "Ungrouped Entries"
		elseif ($this->group_id == 1) $this->group_name = $lang['GROUP_UNGROUPED_LABEL'];
		// group_id = 2 --> "Hidden Entries"
		elseif ($this->group_id == 2) 
		{
			// Admin check
			if ($_SESSION['usertype'] != "admin") 
			{
				reportScriptError("URL tampering detected.");
				exit();
			}
			$this->group_name = $lang['GROUP_HIDDEN_LABEL']; // "Hidden Entries"
		}
		// group_id >= 3 --> Check the database for user-defined group
		else 
		{
			$tbl_grouplist = mysql_fetch_array(mysql_query("SELECT * FROM " . TABLE_GROUPLIST . " AS grouplist WHERE groupid=$this->group_id", $db_link));
			$this->group_name = $tbl_grouplist['groupname'];
			// Reassign to "All Entries" if given a groupid that doesn't exist
			if ($this->group_name == "") 
			{
				$this->group_id = 0;
				$this->group_name = "All Entries";
			}
		}
		// Return value
		return $this->group_name;
	}



	function title() 
	{
		$this->title = $this->group_name;
		
		if (!empty($this->current_letter)) $this->title .= " - $this->current_letter";
		if ($this->total_pages > 1) $this->title .= " (page $this->current_page of $this->total_pages)";
		
		return $this->title;
	}


	function retrieve() 
	{
		global $db_link;
			
	 	// CREATE INITIAL SQL FRAGMENT
		/*$this->sql = "SELECT contact.id, CONCAT(lastname,', ',firstname) AS fullname, lastname, firstname,
						refid, line1, line2, city, state, zip, phone1, phone2, country, whoAdded
						FROM " . TABLE_CONTACT . " AS contact";*/
		$this->sql = "SELECT contact.id, 
							fullname, 
							refid, 
							line1, 
							city, 
							state, 
							zip, 
							phone1, 
							phone2, 
							country, 
							whoAdded
						FROM " . TABLE_CONTACT . " AS contact";

	    // CREATE SQL FRAGMENTS TO FILTER BY GROUP
		// group_id = 0 --> "All Entries"
		if ($this->group_id == 0) 
		{
			$sql_group = " LEFT JOIN " . TABLE_ADDRESS . " 
							AS address ON contact.id=address.id 
							AND contact.primaryAddress=address.refid";
    	}

		// ASSEMBLE THE SQL QUERY
		$this->sql .= $sql_group . $sql_letter . " ORDER BY fullname" . $sql_limit;
		
		// EXECUTE THE SQL QUERY
		$r_contact = mysql_query($this->sql, $db_link)
			or die(reportSQLError($this->sql));
			
		// RETURN RESULTS OF QUERY
		return $r_contact;
	}
}
// END ContactList
?>
