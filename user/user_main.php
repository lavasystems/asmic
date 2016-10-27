<?php
$breadcrumbs = array();

function userMain(){
	global $breadcrumbs, $st, $nh;
	// get variables
	$st = requestNumber($_REQUEST['st'], 0);
	if (!isset($_REQUEST['st']))
		$st = requestNumber($_COOKIE['user_st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 10);
	setcookie("user_st", $st);

	if ($_REQUEST['do'] == 'view') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=user", "User"),
			1 => array("", "View User")
		);
		include_once("user/user_edit.php");
	}
	elseif ($_REQUEST['do'] == 'add') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=user", "User"),
			1 => array("", "Add User")
		);
		include_once("user/user_edit.php");
	}
	elseif ($_REQUEST['do'] == 'edit') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=user", "User"),
			1 => array("", "Edit User")
		);
		include_once("user/user_edit.php");
	}
	elseif ($_REQUEST['do'] == 'delete') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=user", "User"),
			1 => array("", "Delete User")
		);
		include_once("user/user_delete.php");
	}
	elseif ($_REQUEST['do'] == 'password') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=user", "User"),
			1 => array("", "Change Password")
		);
		include_once("user/user_password.php");
	}
	else {
		if (!isAllowed(array(101), $_SESSION['permissions'])){
			session_destroy();
			header("Location: ".$app_absolute_path."index.php?err=0");
			exit();
		}

		$breadcrumbs = array(
			0 => array("", "User")
		);
?>
		<h4>Users</h4>
		<a class="btn btn-primary" href="<?=$app_absolute_path1?>contact/edit.php?mode=new&start=user"><i class="fa fa-plus"></i> Add User</a>
<?php
		listUsers();
	}
}

function listUsers(){
	global $st, $nh;
	$condition = " WHERE usr_hidden = 0 AND usr_deleted = 0";

	include_once("classes/user.php");

	$sql = "SELECT count(*) FROM user_users LEFT JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
	$sql .= $condition;

	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	$total_rows = $row[0];

	$sql = "SELECT * FROM user_users LEFT JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
	$sql .= $condition . " ORDER BY usr_username ASC";
	$sql .= " LIMIT $st, $nh";
	$result = mysql_query($sql);

	$displayed_rows = mysql_num_rows($result);

	if ($total_rows > 0){
?>
	<p>Records <?=$st+1?> to <?=$st + $displayed_rows?> of <?=$total_rows?></p>
	<table class="table table-bordered dashboard-tables">
		<thead>
			<td><b>No</b></td>
			<td><b>Username</b></td>
			<td><b>Full Name</b></td>
			<td><b>Date Registered</b></td>
			<td><b>Action</b></td>
		</thead>
		<tbody>
<?php
		$count = $st;
		while ($row = mysql_fetch_object($result)){
			$count++;
?>
		<tr>
			<td><?=$count?></td>
			<td><a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=view&usr_id=<?=$row->usr_id?>"><?=$row->usr_username?></a></td>
			<td><?=$row->fullname?></td>
			<td><?=$row->usr_createddate?></td>
			<td><? if (!$row->usr_readonly) { ?>
				<a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=edit&usr_id=<?=$row->usr_id?>" class="btn btn-xs btn-default text-info"><i class="fa fa-pencil"></i></a> 
				<a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=delete&usr_id=<?=$row->usr_id?>" class="btn btn-xs btn-default text-danger"><i class="fa fa-times"></i></a> <? } ?>
			</td>
		</tr>
<?php
		}
?>
		</tbody>
	</table>
<?php
		$this_page = $_SERVER['PHP_SELF']. '?mod=user&obj=user';
	
		if ($st > 0){
			$prev_st = $st - $nh;
			if ($prev_st < 0) $prev_st = 0;
			$first_link = $this_page.'&st=0&nh=' . $nh;
			$prev_link = $this_page.'&st=' . $prev_st . '&nh=' .$nh;
		}
		else {
			$first_link = '';
			$prev_link = '';
		}
	
		if (($st + $nh) < $total_rows){
			$last_st = (ceil($total_rows / $nh) - 1) * $nh;
			$next_link = $this_page.'&st=' . ($st + $nh)  . '&nh=' . $nh;
			$last_link = $this_page.'&st=' . $last_st . '&nh=' .$nh;
		}
		else {
			$next_link = '';
			$last_link = '';
		}
?>
	<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-double-left"></i> First</button>', $first_link)?>
	<?=generateLink('<button class="btn btn-primary btn-xs"><i class="fa fa-angle-left"></i> Previous</button>', $prev_link)?>
	<?=generateLink('<button class="btn btn-primary btn-xs">Next <i class="fa fa-angle-right"></i></button>', $next_link)?>
	<?=generateLink('<button class="btn btn-primary btn-xs">Last <i class="fa fa-angle-double-right"></i></button>', $last_link)?>

	</div>
<?
	}
	else
		echo("No users found.");

	mysql_free_result($result);
}

userMain();
?>