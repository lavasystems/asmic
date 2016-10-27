<?php
$breadcrumbs = array();

function groupMain(){
	global $breadcrumbs, $st, $nh;

	// get variables
	$st = requestNumber($_REQUEST['st'], 0);
	if (!isset($_REQUEST['st']))
		$st = requestNumber($_COOKIE['group_st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 10);
	setcookie("group_st", $st);

	if ($_REQUEST['do'] == 'view') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=group", "Group"),
			1 => array("", "View Group")
		);
		include_once("user/group_edit.php");
	}
	elseif ($_REQUEST['do'] == 'add') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=group", "Group"),
			1 => array("", "Add Group")
		);
		include_once("user/group_edit.php");
	}
	elseif ($_REQUEST['do'] == 'edit') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=group", "Group"),
			1 => array("", "Edit Group")
		);
		include_once("user/group_edit.php");
	}
	elseif ($_REQUEST['do'] == 'delete') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=group", "Group"),
			1 => array("", "Delete Group")
		);
		include_once("user/group_delete.php");
	}
	else {
		$breadcrumbs = array(
			0 => array("", "Group")
		);
?>
		<a class="btn btn-primary" href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group&do=add"><i class="fa fa-plus"></i> Add Group</a>
<?php
		listGroups();
	}
}

function listGroups(){
	global $st, $nh;
	$condition = " WHERE grp_hidden = 0";

	$sql = "SELECT count(*) FROM user_groups";
	$sql .= $condition;
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	$total_rows = $row[0];

	$sql = "SELECT * FROM user_groups";
	$sql .= $condition . " ORDER BY grp_name ASC";
	$sql .= " LIMIT $st, $nh";
	$result = mysql_query($sql);

	$displayed_rows = mysql_num_rows($result);

	if ($total_rows){
?>
	<p>Records <?=$st+1?> to <?=$st + $displayed_rows?> of <?=$total_rows?></p>
	<table class="table tabe-striped">
		<thead>
			<td><b>No</b></td>
			<td><b>Name</b></td>
			<td><b>Description</b></td>
			<td><b>Date Created</b></td>
			<td><b>Action</b></td>
		</thead>
		<tbody>
<?php
		$count = $st;
		while ($row = mysql_fetch_object($result)){
			$count++;
?>
		<tr>
			<td class="text-center"><?=$count?></td>
			<td><a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group&do=view&grp_id=<?=$row->grp_id?>"><?=$row->grp_name?></a></td>
			<td><?=$row->grp_description?></td>
			<td><?=$row->grp_createddate?></td>
			<td><? if (!$row->grp_readonly) { ?> <a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group&do=edit&grp_id=<?=$row->grp_id?>" class="btn btn-xs btn-default text-info"><i class="fa fa-pencil"></i></a> 
			 <a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group&do=delete&grp_id=<?=$row->grp_id?>" class="btn btn-xs btn-default text-danger"><i class="fa fa-times"></i></a> <? } else echo("&nbsp;"); ?></td>
		</tr>
<?php
		}
?>
		</tbody>
	</table>
<?php
		if ($st > 0){
			$prev_st = $st - $nh;
			if ($prev_st < 0) $prev_st = 0;
			$first_link = $_SERVER['PHP_SELF']. '?mod=user&obj=group&st=0&nh=' . $nh;
			$prev_link = $_SERVER['PHP_SELF']. '?mod=user&obj=group&st=' . $prev_st . '&nh=' .$nh;
		}
		else {
			$first_link = '';
			$prev_link = '';
		}
	
		if (($st + $nh) < $total_rows){
			$last_st = (ceil($total_rows / $nh) - 1) * $nh;
			$next_link = $_SERVER['PHP_SELF']. '?mod=user&obj=group&st=' . ($st + $nh)  . '&nh=' . $nh;
			$last_link = $_SERVER['PHP_SELF']. '?mod=user&obj=group&st=' . $last_st . '&nh=' .$nh;
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

<?php
	}
	else 
		echo("No groups found.");

	mysql_free_result($result);
}

groupMain();
?>