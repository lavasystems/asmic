<?php
if (!isAllowed(array(101), $_SESSION['permissions'])){
	session_destroy();
	header("Location: ".$app_absolute_path."index.php?err=0");
	exit();
}

$breadcrumbs = array();

function userMain(){
	global $breadcrumbs;

	if ($_REQUEST['do'] == 'purge') {
		$breadcrumbs = array(
			0 => array($_SERVER['PHP_SELF'] . "?mod=user&obj=audittrail", "Audit Trail"),
			1 => array("", "Purge")
		);
	}
	else {
		$breadcrumbs = array(
			0 => array("", "Audit Trail")
		);
		printBodyHeader();
		listUsers();
	}
}

function listUsers(){

	// get variables
	$st = requestNumber($_REQUEST['st'], 0);
	$nh = requestNumber($_REQUEST['nh'], 10);

	$dateFrom = '';
	$dateTo = '';
	$module = '';
	$user = '';
	$sessionid = '';
	
	if (!empty($_REQUEST['dateFrom'])) $dateFrom = $_REQUEST['dateFrom'];
	if (!empty($_REQUEST['dateTo'])) $dateTo = $_REQUEST['dateTo'];
	if (!empty($_REQUEST['module'])) $module = $_REQUEST['module'];
	if (!empty($_REQUEST['user'])) $user = $_REQUEST['user'];
	if (!empty($_REQUEST['sessionid'])) $sessionid = $_REQUEST['sessionid'];
?>
	<h4>Audit Trail</h4>
	<form name="frmReport" method="post" action="<?=$_SERVER['PHP_SELF']?>" class="form-horizontal">
		<input type="hidden" name="action" value="Hantar">
		<input type="hidden" name="mod" value="user">
		<input type="hidden" name="obj" value="audittrail">
		<div class="form-group">
			<label class="control-label col-sm-3">Date Registered:</label>
			<div class="col-sm-4">
				<div class="input-group date" id="dateFrom">
		            <input name="dateFrom" type="text" value="<?=htmlspecialchars($dateFrom) ?>" class="form-control" placeholder="Select start date">
		            <span class="input-group-addon">
		                <span class="fa fa-calendar"></span>
		            </span>
		        </div>
			</div>
			<div class="col-sm-4">
				<div class="input-group date" id="dateTo">
		            <input name="dateTo" type="text" value="<?=htmlspecialchars($dateTo) ?>" class="form-control" placeholder="Select end date">
		            <span class="input-group-addon">
		                <span class="fa fa-calendar"></span>
		            </span>
		        </div>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3" for="email">Group:</label>
			<div class="col-sm-9">
				<select name="module" class="form-control">
					<option value="">ALL</option>
					<option value="user"<?=$module=='user'?" selected":""?>>User Management</option>
					<option value="library"<?=$module=='library'?" selected":""?>>Book Library</option>
					<option value="document"<?=$module=='document'?" selected":""?>>Document Management</option>
					<option value="image"<?=$module=='image'?" selected":""?>>Image Management</option>
					<option value="contact"<?=$module=='contact'?" selected":""?>>Contact Management</option>
				  </select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-3"></label>
			<div class="col-sm-9">
				<button type="submit" class="btn btn-primary" name="Submit">Submit</button>
				<a href="javascript:void(0)" class="btn btn-warning" onClick="resetForm(document.frmGroupEdit)">Reset</a>
				<a href="javascript:history.go(-1)" class="btn btn-info">Back</a>
			</div>
		</div>
	</form>
<?php
	include_once("classes/user.php");

	$condition = " WHERE 1";
	$nav_url = "";

	// condition : filter username
	if ($module){
		$condition .= " AND module = '$module'";
		$nav_url  .= "&module=$module";
	}
	// condition : filter registration date
	if ($dateFrom){
		$condition .= " AND date_modified >= '$dateFrom 00:00:00'";
		$nav_url  .= "&dateFrom=$dateFrom";
	}
	if ($dateTo){
		$condition .= " AND date_modified <= '$dateTo 23:59:59'";
		$nav_url  .= "&dateTo=$dateTo";
	}
	// condition : filter user
	if ($user){
		$condition .= " AND user = '$user'";
		$nav_url  .= "&user=$user";
	}
	// condition : filter session
	if ($sessionid){
		$condition .= " AND sessionid = '$sessionid'";
		$nav_url  .= "&sessionid=$sessionid";
	}

	$sql = "SELECT count(*) FROM user_audittrail";
	$sql .= $condition;
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	$total_rows = $row[0];

	$sql = "SELECT * FROM user_audittrail";
	$sql .= $condition . " ORDER BY date_modified DESC";
	$sql .= " LIMIT $st, $nh";

	$result = mysql_query($sql);
	
	$displayed_rows = mysql_num_rows($result);

	if ($total_rows > 0){
		if (!empty($sessionid)){
			echo("<p>Filter: Session ID $sessionid</p>");
		}
		if (!empty($user)){
			echo("<p>Filter: User $user</p>");
		}
?>
		<p>Records <?=$st+1?> to <?=$st + $displayed_rows?> of <?=$total_rows?></p>
		<table class="table table-bordered dashboard-tables">
			<tr>
				<td><b>No</b></td>
				<td><b>Date</b></td>
				<td><b>User</b></td>
				<td><b>Module</b></td>
				<td><b>Action</b></td>
				<td><b>Filter</b></td>
			</tr>
<?php
		$count = $st;
		while ($row = mysql_fetch_object($result)){
			$count++;
?>
			<tr>
				<td><?=$count?></td>
				<td><?=$row->date_modified?></td>
				<td><?=$row->user?></td>
				<td><?=$row->module?></td>
				<td><?=$row->action?></td>
				<td>
					<? if (empty($user)){ ?><a href="index.php?mod=user&obj=audittrail&user=<?=$row->user?>">[User]</a><? } ?>
					<? if (empty($sessionid) && !empty($row->sessionid)){ ?><a href="index.php?mod=user&obj=audittrail&sessionid=<?=$row->sessionid?>">[Session]</a><? } ?>
				</td>
			</tr>
<?php
		}
?>
	</table>
<?php
		$this_page = $_SERVER['PHP_SELF']. '?mod=user&obj=audittrail';
	
		if ($st > 0){
			$prev_st = $st - $nh . $nav_url;
			if ($prev_st < 0) $prev_st = 0;
			$first_link = $this_page.'&st=0&nh=' . $nh . $nav_url;
			$prev_link = $this_page.'&st=' . $prev_st . '&nh=' .$nh . $nav_url;
		}
		else {
			$first_link = '';
			$prev_link = '';
		}
	
		if (($st + $nh) < $total_rows){
			$last_st = (ceil($total_rows / $nh) - 1) * $nh . $nav_url;
			$next_link = $this_page.'&st=' . ($st + $nh)  . '&nh=' . $nh . $nav_url;
			$last_link = $this_page.'&st=' . $last_st . '&nh=' .$nh . $nav_url;
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
<?php
	}
	else
		echo("No records found.");

	mysql_free_result($result);
}

userMain();
?>
</div>