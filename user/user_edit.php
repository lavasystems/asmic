<?php
	if (!isAllowed(array(101, 401, 501), $_SESSION['permissions'])){
		session_destroy();
		header("Location: ".$app_absolute_path."index.php?err=0");
		exit();
	}

	include_once("user/local_config.php");
	$error_message = '';

	$usr_username = '';
	$usr_password = '';
	$usr_password2 = '';
	$fullname = '';
	$icnum = '';
	$usr_grpid = 0;
	$usr_contactid = 0;
	$usr_start = 'user';
	$bookcode = '';
	if (!empty($_GET['contact_id'])) $usr_contactid = $_GET['contact_id'];
	if (!empty($_GET['start'])) $usr_start = $_GET['start'];
	if (!empty($_GET['bookcode'])) $bookcode = $_GET['bookcode'];

	$frmAction = "add";

	if (!empty($_POST['frmAction'])){
		$frmAction = $_POST['frmAction'];
		include_once("includes/validator.php");
		// request variables
		$usr_id = $_POST['usr_id'];
		$usr_username = $_POST['usr_username'];
		$usr_password = $_POST['usr_password'];
		$usr_password2 = $_POST['usr_password2'];
		$usr_grpid = $_POST['usr_grpid'];
		if (!empty($_POST['usr_start'])) $usr_start = $_POST['usr_start'];
		if (!empty($_POST['bookcode'])) $bookcode = $_POST['bookcode'];
		if (!empty($_POST['usr_contactid'])) $usr_contactid = $_POST['usr_contactid'];

		$isValid = true;

		if ($usr_username=='') { $error_message.="Please fill in the username<br>"; $isValid=false; }
		elseif (preg_match("/[^a-zA-Z0-9]/i", $usr_username)) { $error_message.="Username can only contains alphanumeric without spaces.<br>"; $isValid=false; }

		if (!($frmAction == "edit" && empty($usr_password) && empty($usr_password2))){
				if ($usr_password=='') { $error_message.="Please fill in the password<br>"; $isValid=false; }
				if ($usr_password2=='') { $error_message.="Please confirm the password<br>"; $isValid=false; }
		}

		if (!(empty($usr_password) || empty($usr_password2))){
			if ($usr_password!=$usr_password2) { $error_message.="The password does not match<br>"; $isValid=false; }
			elseif (ereg("[^a-zA-Z0-9]", $usr_password)) { $error_message.="Password can only contains alphanumeric without spaces.<br>"; $isValid=false; }
		}

		include_once("classes/user.php");
		$objNewUser = new User();

		if ($_POST['frmAction']=="add"){
			if ($objNewUser->checkDuplicateUser($usr_username)>0){ $error_message.="The username is already taken<br>"; $isValid=false; }
		}

		if ($isValid){
			if ($_POST['frmAction']=="add"){
				$sql = "INSERT INTO user_users";
				$sql .= " (usr_contactid, usr_username, usr_password,  usr_createddate, usr_grpid)";
				$sql .= " VALUES ($usr_contactid, '$usr_username', password('".mysql_escape_string($usr_password)."'), NOW(), $usr_grpid)";
			}
			elseif ($_POST['frmAction']=="edit"){
				$usr_id = $_POST['usr_id'];
				$sql = "UPDATE user_users";
				$sql .= " SET usr_username='$usr_username'";
				if (!empty($usr_password))
					$sql .= ", usr_password=password('".mysql_escape_string($usr_password)."')";
				$sql .= ", usr_modifieddate=NOW()";
				$sql .= ", usr_grpid = $usr_grpid";
				$sql .= " WHERE usr_id=$usr_id";
			}

			mysql_query($sql);
			if ($frmAction=="add")
				$usr_id = mysql_insert_id();
				
			$objUser = new User($usr_id);
			$usr_contactid = $objUser->usr_contactid;

			include_once($app_absolute_path . "classes/library.php");
			$objLibrary = new Library(true);
			$temp_card = $objLibrary->cards_issued;

			if ($usr_start == 'library'){
				$sqllibrary = "INSERT INTO library_member 
								(contact_id, member_cards, join_date, date_added) 
								VALUES ('$usr_contactid', '$temp_card', CURDATE(), NOW())";
				mysql_query($sqllibrary);
			}
			elseif ($objUser->isPermitted(501) || $objUser->isPermitted(502)){
				$sqllibrary = "SELECT COUNT(*) AS member_count FROM library_member 
								WHERE contact_id = '$usr_contactid'";

				$libResult = mysql_query($sqllibrary);
				$libRow = mysql_fetch_object($libResult);
				if ($libRow->member_count == 0){
					$sqllibrary = "INSERT INTO library_member 
									(contact_id, member_cards, join_date, date_added) 
									VALUES ('$usr_contactid', '$temp_card', CURDATE(), NOW())";
					mysql_query($sqllibrary);
				}
			}

			include_once("classes/audit_trail.php");
			$audit_trail = new audit_trail();
			$audit_trail->writeLog($_SESSION['usr_username'], "user", $frmAction . " user $usr_username");

			header("Location: index.php?mod=user&obj=user&do=$frmAction&success=1&usr_id=$usr_id&start=$usr_start&bookcode=$bookcode&test=$test");
			if ($link)
				mysql_close($link);
			exit();
		}
	}
	if ((!empty($_GET['success']) && $_GET['success']) || $_GET['do'] == "view"){
		$usr_id = $_GET['usr_id'];
		$success = $_GET['success'];
		$do = $_GET['do'];

		// retrieve data

		$sql = "SELECT usr_id, usr_contactid, usr_username, fullname, icnum, usr_grpid FROM user_users LEFT JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
		$sql .= " WHERE usr_id = $usr_id";
		$result = mysql_query($sql);

		while ($row = mysql_fetch_object($result)){
			$usr_contactid = $row->usr_contactid;
			$usr_username = $row->usr_username;
			$fullname = $row->fullname;
			$icnum = $row->icnum;
			$usr_grpid = $row->usr_grpid;
		}
	?>
		<h4><?=ucfirst($do)?> User</h4>
		<div class="info">
		<?php
			if ($success){
				echo(ucfirst($do) . " user successful.");
			}
		?>
	  	</div>
	<table class="table table-striped">
        <tr>
          	<td>Group</td>
          	<td>
          	<?php
				$sql = "SELECT grp_name FROM user_groups WHERE grp_id = $usr_grpid";
				$result = mysql_query($sql);
				while($row = mysql_fetch_object($result)){
			?>
		  	<?=$row->grp_name?>
			<?php } ?>
			<a href="index.php?mod=user&obj=group&do=view&grp_id=<?=$usr_grpid?>" class="btn btn-primary btn-xs pull-right">Check Roles</a>
          	</td>
		</tr>
		<tr>
			<td>Username</td>
			<td><?=$usr_username?></td>
		</tr>
		<tr>
			<td>Full name </td>
			<td><?=$fullname?></td>
		</tr>
		<tr>
			<td>IC number</td>
			<td><?=$icnum?></td>
		</tr>
	</table>
	<a class="btn btn-info" data-rel="prettyPhoto" href="<?=$app_absolute_path?>contact/address_popup.php?id=<?=$usr_contactid?>?iframe=true">View contact details</a>
	<?php
		$usr_start = $_REQUEST['start'];
		$bookcode = $_REQUEST['bookcode'];
		switch($usr_start){
			case 'contact':
				$okUrl = $app_absolute_path . "contact/list.php";
				break;
			case 'library':
				$okUrl = $app_absolute_path . "library/bookissue.php?book_code=$bookcode&member_ic=$icnum";
				break;
			default :
				$okUrl = $_SERVER['PHP_SELF'] . "?mod=user&obj=user";
		}
	?>
	<input class="btn btn-default" name="btnOK" value="OK" onClick="location.href='<?=$okUrl?>'">
	<?php
		//exit();
	}
	if (!empty($_GET['usr_id']) || !empty($_GET['contact_id'])){
		$usr_id = $_GET['usr_id'];
		$contact_id = $_GET['contact_id'];

		$sql = "SELECT usr_id, usr_contactid, usr_username, usr_grpid FROM user_users";
		if (!empty($contact_id))
			$sql .= " WHERE usr_contactid = $contact_id";
		else
			$sql .= " WHERE usr_id = $usr_id";
		$sql .= " AND usr_deleted=0";
		$result = mysql_query($sql);

		if ($result){

			while ($row = mysql_fetch_object($result)){
				$frmAction = "edit";
				$usr_id = $row->usr_id;
				$usr_username = $row->usr_username;
				$usr_password = $row->usr_password;
				$usr_grpid = $row->usr_grpid;
			}
		}
	}
?>
	<h4><?=ucfirst($frmAction)?> User</h4>
	<div class="danger"><?=$error_message?></div>
	<form name="frmUserEdit" method="post" action="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=<?=$frmAction?>" class="form-horizontal">
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">Group:</label>
			<div class="col-sm-9">
				<?php if ($usr_start != 'library'){ ?>
				<select name="usr_grpid" class="form-control">
					<option value="0"<?=$usr_grpid==0?" selected":""?>>Unassigned</option>
					<?php
						$sql = "SELECT grp_id, grp_name FROM user_groups WHERE grp_hidden = 0 ORDER BY grp_name";
						$result = mysql_query($sql);
						while($row = mysql_fetch_object($result)){
					?>
				  	<option value="<?=$row->grp_id?>"<?=$usr_grpid==$row->grp_id?" selected":""?>><?=$row->grp_name?></option>
					<?php } ?>
		        </select>
		        <?php } else { ?>
		        <input type="hidden" name="usr_grpid" value="<?=$library_public_user?>"><?
					include_once("classes/group.php");
					$objGroup = new group($library_public_user);
					echo($objGroup->grp_name);
				} ?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">Username:</label>
			<div class="col-sm-9">
				<input name="usr_username" type="text" id="usr_username" value="<?=$usr_username?>" class="form-control"<?=$frmAction=="edit"?" readonly":""?>>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">Password:</label>
			<div class="col-sm-9">
				<input name="usr_password" type="password" id="usr_password" value="<?=$usr_password?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">Confirm Password:</label>
			<div class="col-sm-9">
				<input name="usr_password2" type="password" id="usr_password2" value="<?=$usr_password2?>" class="form-control">
			</div>
		</div>

	  	<input type="hidden" name="usr_id" value="<?=$usr_id?>">
	  	<input type="hidden" name="frmAction" value="<?=$frmAction?>">
	  	<input type="hidden" name="usr_contactid" value="<?=$usr_contactid?>">
	  	<input type="hidden" name="usr_start" value="<?=$usr_start?>">
	  	<input type="hidden" name="bookcode" value="<?=$bookcode?>">

	  	<div class="form-group">
			<label class="control-label col-sm-3"></label>
			<div class="col-sm-9">
				<button type="submit" class="btn btn-primary" name="Submit">Submit</button>
				<a href="javascript:void(0)" class="btn btn-warning" onClick="resetForm(document.frmGroupEdit)">Reset</a>
				<a href="javascript:history.go(-1)" class="btn btn-info">Back</a>
			</div>
		</div>
	</form>