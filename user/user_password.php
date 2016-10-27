<?php
	include_once("user/local_config.php");
	$error_message = '';

	$usr_username = '';
	$usr_password = '';
	$usr_password2 = '';

	$frmAction = "password";

	if (!empty($_POST['frmAction'])){
		include_once("includes/validator.php");
		// request variables
		$usr_id = $_POST['usr_id'];
		$usr_username = $_POST['usr_username'];
		$usr_password = $_POST['usr_password'];
		$usr_password2 = $_POST['usr_password2'];

		$isValid = true;

		if ($usr_password=='') { $error_message.="Please fill in the password"; $isValid=false; }
		if ($usr_password2=='') { $error_message.="Please confirm the password"; $isValid=false; }
		if ($usr_password!=$usr_password2) { $error_message.="The password does not match"; $isValid=false; }

		include_once("classes/user.php");
		$objNewUser = new User();

		$frmAction = $_POST['frmAction'];

		if ($isValid){
			$usr_id = $_POST['usr_id'];
			$sql = "UPDATE user_users SET";
			$sql .= " usr_password=password('$usr_password')";
			$sql .= ", usr_modifieddate=NOW()";
			$sql .= " WHERE usr_id=$usr_id";

			mysql_query($sql);

			include_once("classes/audit_trail.php");
			$audit_trail = new audit_trail();
			$audit_trail->writeLog($_SESSION['usr_username'], "user", "Change password");

			if ($link)
				mysql_close($link);

			header("Location: index.php?mod=user&obj=user&do=password&success=1");
			exit();
		}
	}
	elseif ((!empty($_GET['success']) && $_GET['success'])){
		$usr_id = $_SESSION['usr_id'];
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
	<h4>Change Password</h4>
	<?php if ($success){
		echo("<div class=\"alert alert-info\">Your password has been changed successfully.</div>");
	} ?>
	<?php
		if ($link)
			mysql_close($link);
		exit();
	}
	else {
		$usr_id = $_SESSION['usr_id'];

		$sql = "SELECT usr_id, usr_contactid, usr_username, usr_grpid FROM user_users";
		if (!empty($contact_id))
			$sql .= " WHERE usr_contactid = $contact_id";
		else
			$sql .= " WHERE usr_id = $usr_id";
		$result = mysql_query($sql);

		while ($row = mysql_fetch_object($result)){
			$usr_id = $row->usr_id;
			$usr_username = $row->usr_username;
			$usr_grpid = $row->usr_grpid;
			$fullname = $row->fullname;
			$icnum = $row->icnum;
		}
	}
	?>
	<h4>Change Password</h4>
	<?php if($error_message): ?>
	<div class="alert-danger"><?=$error_message?></div>
	<?php endif; ?>
	
	<div class="row">
		<div class="col-lg-6">
			<form name="frmUserEdit" method="post" action="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=<?=$frmAction?>">
				<div class="form-group">
					<label>Username</label>
					<input type="text" class="form-control" readonly value="<?=$usr_username?>">
				</div>
		        <div class="form-group">
					<label>Password</label>
					<input name="usr_password" type="password" id="usr_password" class="form-control" value="<?=$usr_password?>">
				</div>
		        <div class="form-group">
					<label>Confirm password</label>
					<input name="usr_password2" type="password" id="usr_password2" class="form-control" value="<?=$usr_password2?>">
		        </div>
		        <input type="hidden" name="usr_id" value="<?=$usr_id?>">
				<input type="hidden" name="usr_username" value="<?=$usr_username?>">
				<input type="hidden" name="frmAction" value="<?=$frmAction?>">
				<input type="submit" name="Submit" value="Submit" class="btn btn-primary">
				<input type="reset" name="Reset" value="Reset" class="btn btn-default">
			</form>
		</div>
		<div class="col-lg-6">
			<?php
				$sql = "SELECT grp_name FROM user_groups WHERE grp_id = $usr_grpid";
				$result = mysql_query($sql);
				while($row = mysql_fetch_object($result)){
			?>

			<dl class="dl-horizontal">
				<dt>Group</dt>
				<dd><?=$row->grp_name?></dd>
			
			<?php } ?>

				<dt>Username</dt>
				<dd><?=$usr_username?></dd>
				<dt>Full name</dt>
				<dd><?=$fullname?></dd>
				<dt>IC number</dt>
				<dd><?=$icnum?></dd>
			</dl>
		    <input class="btn btn-default" type="button" name="btnOK" value="OK" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">
		</div>
	</div>