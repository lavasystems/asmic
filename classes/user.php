<?php
/*
class : User
*/
class User {
	var $roles;
	var $groups;
	var $permissions;
	
	function __construct($usr_id=0) {
		if ($usr_id != 0)
			$this->initUser($usr_id);
	}

	private function initUser($usr_id){
		// init user variables
		$sql = "SELECT * FROM user_users";
		$sql .= " WHERE usr_id = ".$usr_id." LIMIT 1";

		$result = mysql_query($sql);

		$row = mysql_fetch_assoc($result);
		foreach ($row as $k => $v) {
			$this->{$k} = $v;
		}

		// init groups
		$sql = "SELECT * FROM user_groups";
		$sql .= " WHERE grp_id = " . $this->usr_grpid." LIMIT 1";

		$result = mysql_query($sql);
		$i = 0;

		$row = mysql_fetch_object($result);

		if ($row->grp_permuser != 0){
			$this->permissions[$i] = $row->grp_permuser;
			$i++;
		}
		if ($row->grp_permcontact != 0){
			$this->permissions[$i] = $row->grp_permcontact;
			$i++;
		}
		if ($row->grp_permlibrary != 0){
			$this->permissions[$i] = $row->grp_permlibrary;
			$i++;
		}
		if ($row->grp_permdocument != 0){
			$this->permissions[$i] = $row->grp_permdocument;
			$i++;
		}
		if ($row->grp_permimage != 0){
			$this->permissions[$i] = $row->grp_permimage;
			$i++;
		}
	}

	private function validateUser($username, $password){
		$sql = "SELECT usr_id FROM user_users";
		$sql .= " WHERE usr_username = '$username'";
		$sql .= " AND usr_password = password('$password') AND usr_deleted = 0 LIMIT 1";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0){
			$row = mysql_fetch_object($result);
			return $row->usr_id;
			die(var_dump($row->usr_id));
		}
		else
			return 0;
	}

	function isPermitted($role_id){
		if (!empty($this->permissions) && in_array($role_id, $this->permissions))
			return true;
		else
			return false;
	}
	
	function doLogin($username, $password){
		$usr_id = $this->validateUser($username, $password);
		if ($usr_id){
			$this->initUser($usr_id);
			// set sessions
			$_SESSION['usr_id'] = $usr_id;
			$_SESSION['usr_username'] = $username;
			$_SESSION['permissions'] = $this->permissions;
			$_SESSION['usr_grpid'] = $this->usr_grpid;

			if (count($this->permissions) > 0){
				foreach ($this->permissions as $permission){
					switch($permission){
						case '401':
							$_SESSION['username'] = "admin";
							$_SESSION['usertype'] = "admin";
							break;
						case '402':
							$_SESSION['username'] = "user";
							$_SESSION['usertype'] = "user";
							break;
						default:
					}
				}
			}
			$sql = "UPDATE user_users SET usr_lastvisit = NOW()";
			$sql .= " WHERE usr_id = $usr_id LIMIT 1";
	
			mysql_query($sql);
			return true;
		}
		else
			return false;
	}

	function doLogout(){
		$_SESSION = array(); 
		session_destroy();
	}

	function checkDuplicateUser($username){
		$sql = "SELECT usr_id FROM user_users";
		$sql .= " WHERE usr_username = '$username'";
		$sql .= " AND usr_deleted = 0 LIMIT 1";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0){
			$row = mysql_fetch_object($result);
			return $row->usr_id;
		}
		else
			return 0;
	}
	
	static function deleteUser($usr_id){
		$sql = "UPDATE user_users SET usr_deleted = 1";
		$sql .= " WHERE usr_id = ".$usr_id." LIMIT 1";

		mysql_query($sql);

		if (mysql_affected_rows() > 0){
			return true;
		}
		else
			return false;
	}

	static function deleteUserByContactId($usr_contactid){
		$sql = "UPDATE user_users SET usr_deleted = 1";
		$sql .= " WHERE usr_contactid = ".$usr_contactid." LIMIT 1";

		mysql_query($sql);

		if (mysql_affected_rows() > 0){
			return true;
		}
		else
			return false;
	}
	
	static function isUser($usr_contactid){
		$sql = "SELECT usr_id FROM user_users";
		$sql .= " WHERE usr_contactid = ".$usr_contactid." AND usr_deleted = 0 LIMIT 1";

		$result = mysql_query($sql);

		if (mysql_num_rows($result) > 0){
			$row = mysql_fetch_object($result);
			return $row->usr_id;
		}
		else
			return 0;
	}

	function updatePassword($newpassword, $oldpassword=''){
		$sql = "UPDATE user_users SET usr_password = 1";
		$sql .= " WHERE usr_id = ".$this->usr_id." LIMIT 1";

		mysql_query($sql);

		if (mysql_affected_rows() > 0){
			return true;
		}
		else
			return false;
	}
}