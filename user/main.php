<?php
include_once("user/functions.php");

function modMain(){
	$link = db_mysql_connect();
	if (!$link)
		die('connection failed<br>' . $MYSQL_ERROR);

	switch($_REQUEST['obj']){
		case 'user':
			include_once("user/user_main.php");
			break;
		case 'group':
			include_once("user/group_main.php");
			break;
		case 'search':
			include_once("user/search.php");
			break;
		case 'audittrail':
			include_once("user/audittrail.php");
			break;
		default:
			printBodyHeader();
			showUserMenu();
	}
	if ($link)
		mysql_close($link);
}

function showUserMenu(){
?>
<p>Logged in as <?php echo $_SESSION['usr_username']; ?></p>
<div class="col-md-6">
	<h4 class="site-heading">New Users</h4>
	<ul>
	<?php
		$sql = "SELECT usr_username, contact_contact.fullname FROM user_users INNER JOIN contact_contact ON user_users.usr_contactid = contact_contact.id";
		$sql .= " WHERE usr_hidden = 0 AND usr_deleted=0";
		$sql .= " ORDER BY usr_createddate DESC limit 0, 5";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_object($result)){
			echo '<li>'.$row->fullname.'</li>';
		}
	?>
	</ul>
</div>
<div class="col-md-6">
	<h4 class="site-heading">Registered Users</h4>
	<ul>
	<?php
		$sql = "SELECT grp_id, grp_name, count(usr_id) as user_count FROM user_groups";
		$sql .= " INNER JOIN user_users ON user_groups.grp_id = user_users.usr_grpid";
		$sql .= " WHERE usr_hidden = 0 AND usr_deleted = 0";
		$sql .= " GROUP BY usr_grpid";
		$sql .= " ORDER BY grp_name";
		$result = mysql_query($sql);
		while ($row = mysql_fetch_object($result)){
			if (empty($row->grp_name)){
				$row->grp_name = "Unassigned";
			}
			else {
				$grp_name_a_tag_open = "<a href=\"index.php?mod=user&obj=search&do=search&usr_grpid=$row->grp_id\">";
				$grp_name_a_tag_close = "</a>";
			}
		echo "<li>".$grp_name_a_tag_open.$row->grp_name.$grp_name_a_tag_close." : ".$row->user_count."</li>";
		}
	?>
	</ul>
</div>
<?php
	mysql_free_result($result);
}
modMain(); 
?>
</div>