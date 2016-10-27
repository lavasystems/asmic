<?php
if (!isAllowed(array(101), $_SESSION['permissions'])){
	session_destroy();
	header("Location: ".$app_absolute_path."index.php?err=0");
	exit();
}

$usr_id = $_REQUEST['usr_id'];

if ($_REQUEST['confirm']){

	$usr_contactid = $_POST['usr_contactid'];
	$objUser = new User($usr_id);
	$usr_username = $objUser->usr_username;
	$success = $objUser->deleteUser($usr_id);

	require_once($app_absolute_path1 . "classes/contact.php");
	$objContact = new Contact();
	if (!empty($usr_contactid))
		$success = $objContact->deleteContact($objUser->usr_contactid);

	include_once("classes/audit_trail.php");
	$audit_trail = new audit_trail();
	$audit_trail->writeLog($_SESSION['usr_username'], "user", "delete user $usr_username");

	header("location: ".$app_absolute_path."index.php?mod=user&obj=user&do=delete&success=$success");
	exit();
}
elseif ($_REQUEST['success']){
	printBodyHeader();
	$success = $_REQUEST['success'];
	if ($success)
		echo("User delete successful.");
	else
		echo("User delete failed.");
?>
	<p><a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user">Click here to continue</a></p>
<?php
	exit();
}

// retrieve data

$sql = "SELECT usr_id, usr_contactid, usr_username, fullname, icnum, usr_grpid FROM user_users LEFT JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
$sql .= " WHERE usr_id = $usr_id";

$result = mysql_query($sql);

while ($row = mysql_fetch_object($result)){
	$usr_contactid = $row->usr_contactid;
	$usr_username = $row->usr_username;
	$fullname = $row->fullname;
	$icnum = $row->icnum;
	$usr_password = $row->usr_password;
	$usr_grpid = $row->usr_grpid;
}

printBodyHeader();
?>
<h4><b>Delete User</h4>
<div class="bg-danger">Are you sure to delete this user?</div>
	  <table border="0" cellspacing="1" cellpadding="3" class="m1_table_outline">
        <tr>
          <td class="m1_td_fieldname">Group</td>
          <td class="m1_td_content">:</td>
          <td class="m1_td_content">
<?php
		$sql = "SELECT grp_name FROM user_groups WHERE grp_id = $usr_grpid";
		$result = mysql_query($sql);
		while($row = mysql_fetch_object($result)){
?>
		  	<?=$row->grp_name?>
<?php
		}
?>
          </td>
        </tr>
        <tr>
          <td class="m1_td_fieldname">Username</td>
          <td class="m1_td_content">:</td>
          <td class="m1_td_content"><?=$usr_username?></td>
        </tr>
        <tr>
          <td class="m1_td_fieldname">Full Name</td>
          <td class="m1_td_content">:</td>
          <td class="m1_td_content"><?=$fullname?></td>
        </tr>
        <tr>
          <td class="m1_td_fieldname">IC Number</td>
          <td class="m1_td_content">:</td>
          <td class="m1_td_content"><?=$icnum?></td>
        </tr>
      </table>
<form name="frmUserDelete" method="post" action="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=delete">
<p><input name="usr_contactid" type="checkbox" value="<?=$usr_contactid?>"> Delete contact for this user</p>
  <input type="image" name="Submit" src="<?=$app_absolute_path1?>images/m1/m1_btn_delete.gif" border="0" alt="">&nbsp;<a href="javascript:history.go(-1)"><img src="<?=$app_absolute_path1?>images/m1/m1_btn_back.gif" border="0" alt=""></a>
  <input type="hidden" name="usr_id" value="<?=$usr_id?>">
  <input type="hidden" name="confirm" value="1">
</form>