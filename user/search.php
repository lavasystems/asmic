<?php
if (!isAllowed(array(101), $_SESSION['permissions'])){
	session_destroy();
	header("Location: ".$app_absolute_path."index.php?err=0");
	exit();
}

$st = 0;
$nh = 10;
if (!empty($_REQUEST['st'])) $st = $_REQUEST['st'];
if (!empty($_REQUEST['nh'])) $nh = $_REQUEST['nh'];

$usr_grpid = "-1";
$usr_username = "";
$fullname = '';
$icnum = '';
$dateFrom = '';
$dateTo = '';

if ($_REQUEST['usr_grpid'] != '') $usr_grpid = $_REQUEST['usr_grpid'];
if (!empty($_REQUEST['usr_username'])) $usr_username = $_REQUEST['usr_username'];
if (!empty($_REQUEST['fullname'])) $fullname = $_REQUEST['fullname'];
if (!empty($_REQUEST['icnum'])) $icnum = $_REQUEST['icnum'];
if (!empty($_REQUEST['dateFrom'])) $dateFrom = $_REQUEST['dateFrom'];
if (!empty($_REQUEST['dateTo'])) $dateTo = $_REQUEST['dateTo'];

global $breadcrumbs;

$breadcrumbs = array(
	0 => array("", "Search")
);

printBodyHeader();
?>
<h4>Search</h4>
<script language="javascript" type="text/javascript">
	function clearUserSearch (form){
		form.usr_grpid.options[0].selected = true;
		form.usr_username.value = '';
		form.fullname.value = '';
		form.icnum.value = '';
		form.dateFrom.value = '';
		form.dateTo.value = '';
	}

	var calFrom = new CalendarPopup();
	calFrom.showYearNavigation();
	var calTo = new CalendarPopup();
	calTo.showYearNavigation();
</script>
<form name="frmUserSearch" id="frmUserSearch" method="post" action="<?=$_SERVER['PHP_SELF']?>" class="form-horizontal">
	<div class="form-group">
		<label class="control-label col-sm-3" for="email">Group Name:</label>
		<div class="col-sm-9">
			<select name="usr_grpid" class="form-control">
			<option value="-1"<?=$usr_grpid=='-1'?" selected":""?>>All</option>
		  	<option value="0"<?=$usr_grpid=='0'?" selected":""?>>Unassigned</option>
		  	<?php
				$sql = "SELECT grp_id, grp_name FROM user_groups WHERE grp_hidden = 0 ORDER BY grp_name";
				$result = mysql_query($sql);
				while($row = mysql_fetch_object($result)){
			?>
		  	<option value="<?=$row->grp_id?>"<?=$usr_grpid==$row->grp_id?" selected":""?>><?=$row->grp_name?></option>
			<?php } ?>
          </select>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3" for="usr_username">Username:</label>
		<div class="col-sm-9">
			<input name="usr_username" type="text" id="usr_username" value="<?=htmlspecialchars($usr_username)?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3" for="fullname">Name:</label>
		<div class="col-sm-9">
			<input name="fullname" type="text" id="fullname" value="<?=htmlspecialchars($fullname)?>" class="form-control">
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-3" for="icnum">IC No:</label>
		<div class="col-sm-9">
			<input name="icnum" type="text" id="icnum" value="<?=htmlspecialchars($icnum)?>" class="form-control">
		</div>
	</div>
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
		<label class="control-label col-sm-3"></label>
		<div class="col-sm-9">
			<input type="hidden" name="mod" value="user">
			<input type="hidden" name="obj" value="search">
			<input type="hidden" name="do" value="search">
			<button type="submit" class="btn btn-primary" name="Submit">Submit</button>
			<a href="javascript:void(0)" class="btn btn-warning" onClick="resetForm(document.frmGroupEdit)">Reset</a>
			<a href="javascript:history.go(-1)" class="btn btn-info">Back</a>
		</div>
	</div>
</form>
<?php
$displayed_rows = 0;
if ($_REQUEST['do'] == 'search'){
	$condition = ' WHERE 1';
	$condition .= " AND usr_hidden = 0 AND usr_deleted = 0";
	$nav_url = '';
	// condition : filter group
	if ($usr_grpid > -1){
		$condition .= " AND usr_grpid = $usr_grpid";
		$nav_url  .= "&usr_grpid=$usr_grpid";
	}
	// condition : filter username
	if (!empty($usr_username)){
		$condition .= " AND usr_username LIKE '%".mysql_escape_string($usr_username)."%'";
		$nav_url  .= "&usr_username=$usr_username";
	}
	// condition : filter registration date
	if (!(empty($dateFrom) || $dateFrom == "")){
		$loc_dateFrom = dateFormat($dateFrom, 'm/d/y', 'Y-m-d');
		$condition .= " AND usr_createddate >= '$loc_dateFrom 00:00:00'";
		$nav_url  .= "&dateFrom=$dateFrom";
	}
	if (!(empty($dateTo) || $dateTo == "")){
		$loc_dateTo = dateFormat($dateTo, 'm/d/y', 'Y-m-d');
		$condition .= " AND usr_createddate <= '$loc_dateTo 23:59:59'";
		$nav_url  .= "&dateTo=$dateTo";
	}

	if (!empty($fullname)){
		$condition .= " AND contact_contact.fullname LIKE '%".mysql_escape_string($fullname)."%'";
	}
	
	if (!empty($icnum)){
		$condition .= " AND contact_contact.icnum = '".mysql_escape_string($icnum)."'";
	}

	$sql = "SELECT count(*) FROM user_users LEFT JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
	$sql .= $condition;
	$result = mysql_query($sql);
	$row = mysql_fetch_row($result);
	$total_rows = $row[0];

	$sql = "SELECT * FROM user_users LEFT JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
	$sql .= $condition;
	$sql .= " LIMIT $st, $nh";
	
	$result = mysql_query($sql);
	$counter = 0;
	
	$displayed_rows = mysql_num_rows($result);
	
	if ($total_rows > 0){
?>		
		<div id="Table_Search_Result">
			<p>Records <?=$st+1?> to <?=$st + $displayed_rows?> of <?=$total_rows?></p>
			<table class="table table-bordered dashboard-tables">
				<tr>
					<td><b>Id</b></td>
					<td><b>Username</b></td>
					<td><b>Full Name</b></td>
					<td><b>Date Registered</b></td>
					<td id="Action_Label"><b>Action</b></td>
				</tr>
	<?php
			while ($row = mysql_fetch_object($result)){
	?>
				<tr>
					<td><?=$row->usr_id?></td>
					<td><a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=view&usr_id=<?=$row->usr_id?>"><?=$row->usr_username?></a></td>
					<td><?=$row->fullname?></td>
					<td><?=$row->usr_createddate?></td>
					<td id="Action_Buttons_<?=$counter?>"><? if (!$row->usr_readonly) { ?> 
						<a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=edit&usr_id=<?=$row->usr_id?>" class="btn btn-xs btn-default text-info"><i class="fa fa-pencil"></i></a> 
					 	<a href="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=user&do=delete&usr_id=<?=$row->usr_id?> "class="btn btn-xs btn-default text-danger"><i class="fa fa-times"></i></a> <? } ?></td>
				</tr>
	<?
				$counter++;
			}
	?>
			</table>
			<script language="JavaScript">
			document.getElementById("Action_Label").style.display = "none";
			for(var i=0; i<<?=$displayed_rows?>; i++){
			 	document.getElementById("Action_Buttons_"+i).style.display = "none"; 	 	
			}			
			</script>
		</div>							
<?
		$this_page = $_SERVER['PHP_SELF']. "?mod=user&obj=search&do=search$nav_url";
	
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
	<a href="#" onClick="printSearchResult()" class="btn btn-default btn-xs pull-right"><i class="fa fa-print"></i> Print</a>

<?php
	}
	else
		echo("No results found.");
} // end do search
?>
<script language="JavaScript">
function printSearchResult(){
	var html = "<html>\n";
	html += "<head>\n";	
	html += "<title>Group & User Management - Search Result</title>\n";
	html += "<link href=\"" + '<?="http://".$_SERVER['SERVER_NAME']?>' + "/asmic.css\" rel=\"stylesheet\" type=\"text/css\">\n";		
	html += "</head>\n";	
	html += "<body>\n";
	html += "<?=addslashes(file_get_contents("includes/print_header_space.php"))?>";
 	html += document.getElementById("Table_Search_Result").innerHTML;
 	html += "</body>\n"; 	
 	html += "</html>\n";	

 	var printWin = window.open("", "_blank", "printSpecial");
 	printWin.document.open();
 	printWin.document.write(html); 	
 	printWin.document.close();	
 	printWin.print();
}
</script>
<script language="JavaScript">
if(document.getElementById("Action_Label") != null){
	document.getElementById("Action_Label").style.display = "";
	for(var i=0; i<<?=$displayed_rows?>; i++){
	 	document.getElementById("Action_Buttons_"+i).style.display = ""; 	 	
	}
}		
</script>