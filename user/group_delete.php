<?php
if (!isAllowed(array(101), $_SESSION['permissions'])){
	session_destroy();
	header("Location: ".$app_absolute_path."index.php?err=0");
	exit();
}

$grp_id = $_REQUEST['grp_id'];

if ($_REQUEST['confirm']){
	$new_grpid = $_REQUEST['new_grpid'];
	require_once("classes/group.php");

	$objGroup = new Group($grp_id);
	$grp_name = $objGroup->grp_name;
	$success = $objGroup->deleteGroup($grp_id, $new_grpid);

	include_once("classes/audit_trail.php");
	$audit_trail = new audit_trail();
	$audit_trail->writeLog($_SESSION['usr_username'], "user", "delete group $grp_name");

	if ($link)
		mysql_close($link);

	header("location: ".$app_absolute_path."index.php?mod=user&obj=group&do=delete&success=$success");
	exit();
}
elseif ($_REQUEST['success']){
	printBodyHeader();
	$success = $_REQUEST['success'];

	if ($success)
		echo("Group delete successful.");
	else
		echo("Group delete failed.");
?>
	<a class="btn btn-primary" href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group">Click here to continue</a>
<?php
	exit();
}

// retrieve data

$sql = "SELECT * FROM user_groups";
$sql .= " WHERE grp_id = $grp_id";
$result = mysql_query($sql);
while ($row = mysql_fetch_object($result)){
	$grp_name = $row->grp_name;
	$grp_description = $row->grp_description;
	$grp_permuser = $row->grp_permuser;
	$grp_permcontact = $row->grp_permcontact;
	$grp_permlibrary = $row->grp_permlibrary;
	$grp_permdocument = $row->grp_permdocument;
	$grp_permimage = $row->grp_permimage;
}

$sql = "SELECT count(*) as usr_count FROM user_users";
$sql .= " WHERE usr_grpid = $grp_id";
$result = mysql_query($sql);
$row = mysql_fetch_object($result);
$usr_count = $row->usr_count;

printBodyHeader();
?>
<h4><b>Delete Group</b></h4>
<form name="frmGroupDelete" method="post" action="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group&do=delete">
<?php
if ($usr_count > 0){
?>
<div class="alert alert-warning">There are <?=$usr_count?> user<?=$usr_count>1?"s":""?> under this group.</div>
<div class="alert alert-info">Please select a new group for these users.</div>
<div class="form-group">
	<label for="fullname">New group:</label>
	<select name="new_grpid" class="form-control">
		<option value="0"<?=$usr_grpid==0?" selected":""?>>Unassigned</option>
		<?php
			$sql = "SELECT grp_id, grp_name FROM user_groups ORDER BY grp_name";
			$result = mysql_query($sql);
			while($row = mysql_fetch_object($result)){
		?>
		<option value="<?=$row->grp_id?>"<?=$usr_grpid==$row->grp_id?" selected":""?>><?=$row->grp_name?></option>
		<?php } ?>
	</select>
</div>
<?php
} else {
?>
<div class="alert alert-danger" role="alert">Are you sure to delete this group?</div>
<?
}
?>
	<form class="contact-form">
	  	<div class="form-group">
			<label for="fullname">Name</label>
			<input type="text" class="form-control" name="grp_name" id="grp_name" value="<?=$grp_name?>">
		</div>
		<div class="form-group">
			<label for="description">Description</label>
			<textarea class="form-control" name="grp_description" rows="5"><?=nl2br($grp_description)?></textarea>
		</div>
	</form>

	<h4>Group Roles</h4>
	  <table>
		  <tr><td class="m1_td_fieldname" width="150">User Management</td><td class="m1_td_content" width="100">
		  <?=$grp_permuser==0?"No Access":""?><?=$grp_permuser==101?"Administrator":""?>
		  </td>
		  </tr>
		  <tr><td class="m1_td_fieldname">Contact Management</td><td class="m1_td_content">
		  <?=$grp_permcontact==0?"No Access":""?><?=$grp_permcontact==402?"Normal User":""?><?=$grp_permcontact==401?"Administrator":""?>
		  </td>
		  </tr>
		  <tr><td class="m1_td_fieldname">Document Management</td><td class="m1_td_content">
		  <?=$grp_permdocument==0?"No Access":""?><?=$grp_permdocument==202?"Normal User":""?><?=$grp_permdocument==201?"Administrator":""?>
		  </td>
		  </tr>
		  <tr><td class="m1_td_fieldname">Book Library</td><td class="m1_td_content">
		  <?=$grp_permlibrary==0?"No Access":""?><?=$grp_permlibrary==502?"Normal User":""?><?=$grp_permlibrary==501?"Librarian":""?>
		  </td>
		  </tr>
		  <tr><td class="m1_td_fieldname">Image Bank</td><td class="m1_td_content">
		  <?=$grp_permimage==0?"No Access":""?><?=$grp_permimage==302?"Normal User":""?><?=$grp_permimage==301?"Administrator":""?>
		  </td>
		  </tr>
	  </table><br />
	<button type="submit" class="btn btn-primary" name="Submit">Submit</button>
  	<a href="javascript:history.go(-1)" class="btn btn-info">Back</a>
  	<input type="hidden" name="grp_id" value="<?=$grp_id?>">
  	<input type="hidden" name="confirm" value="1">
</form>