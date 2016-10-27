<?php
class User
{
	function login($username, $password)
	{
        global $db;
        $password = urldecode($password);

		$r = "SELECT * FROM `user_users` WHERE `usr_username` = :username AND `usr_password` = :password";
		    $r_do = $db->prepare($r);
            $r_do->bindParam(':username', $username, PDO::PARAM_STR);
		    $r_do->bindParam(':password', sha1($password), PDO::PARAM_STR);
        try {
			$r_do->execute();
            $f2 = $db->query("SELECT FOUND_ROWS()")->fetchColumn();
		} catch(PDOException $r) {
			echo "Error evaluating the query";
		}
        
		if(!$f2) {
			//Login failed
			header("location: index.php");
			exit();
		} else {
            $f = $r_do->fetch (PDO::FETCH_ASSOC);

			session_regenerate_id();
			$_SESSION['SESS_USER_ID'] 	= clean($f['usr_id']);
			$_SESSION['SESS_USERNAME'] 	= clean($f['usr_username']);
			$_SESSION['SESS_CONTACT'] 	= clean($f['usr_contactid']);
			$_SESSION['SESS_GROUP'] 	= clean($f['usr_grpid']);

			session_write_close();
			
			header("location: user.php");
			exit();
		}
    }

    // Logout
	function logout()
	{
		unset($_SESSION['SESS_USER_ID']);
		unset($_SESSION['SESS_USERNAME']);
		unset($_SESSION['SESS_FULLNAME']);
		header("location: ../laman-utama");
		exit();
	}
}