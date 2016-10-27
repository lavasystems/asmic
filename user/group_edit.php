<?php
	if (!isAllowed(array(101, 401, 501), $_SESSION['permissions'])){
		session_destroy();
		header("Location: ".$app_absolute_path."index.php?err=0");
		exit();
	}

	$error_message = '';

	$grp_name = '';
	$grp_description = '';
	$grp_permuser = 0;
	$grp_permcontact = 0;
	$grp_permlibrary = 0;
	$grp_permdocument = 0;
	$grp_permimage = 0;

	$frmAction = "add";

	if (!empty($_POST['frmAction'])){
		include_once("includes/validator.php");
		// request variables
		$grp_name = $_POST['grp_name'];
		$grp_description = $_POST['grp_description'];
		$grp_permuser = $_POST['grp_permuser'];
		$grp_permcontact = $_POST['grp_permcontact'];
		$grp_permlibrary = $_POST['grp_permlibrary'];
		$grp_permdocument = $_POST['grp_permdocument'];
		$grp_permimage = $_POST['grp_permimage'];

		$isValid = true;

		if ($grp_name=='') { $error_message.="Please fill in the group name"; $isValid=false; }

		include_once("classes/group.php");
		$objNewGroup = new Group();
		if ($_POST['frmAction']=="add"){
			if ($objNewGroup->checkDuplicateGroup($grp_name)>0){ $error_message.="The group name is already exists"; $isValid=false; }
		}

		$frmAction = $_POST['frmAction'];

		if ($isValid){
			$frmAction = $_POST['frmAction'];
			if ($frmAction=="add"){
				$sql = "INSERT INTO user_groups";
				$sql .= " (grp_name, grp_description, grp_createddate, grp_permuser, grp_permcontact, grp_permlibrary, grp_permdocument, grp_permimage)";
				$sql .= " VALUES ('".mysql_escape_string($grp_name)."', '".mysql_escape_string($grp_description)."', NOW(), $grp_permuser, $grp_permcontact, $grp_permlibrary, $grp_permdocument, $grp_permimage)";
			}
			elseif ($frmAction=="edit"){
				$grp_id = $_POST['grp_id'];
				$sql = "UPDATE user_groups";
				$sql .= " SET grp_name = '".mysql_escape_string($grp_name)."'";
				$sql .= ", grp_description = '".mysql_escape_string($grp_description)."'";
				$sql .= ", grp_permuser = $grp_permuser";
				$sql .= ", grp_permcontact = $grp_permcontact";
				$sql .= ", grp_permlibrary = $grp_permlibrary";
				$sql .= ", grp_permdocument = $grp_permdocument";
				$sql .= ", grp_permimage = $grp_permimage";
				$sql .= ", grp_modifieddate = NOW()";
				$sql .= " WHERE grp_id = $grp_id";
			}

			mysql_query($sql);
			if ($frmAction=="add")
				$grp_id = mysql_insert_id();

			include_once("classes/audit_trail.php");
			$audit_trail = new audit_trail();
			$audit_trail->writeLog($_SESSION['usr_username'], "user", $frmAction . " group $grp_name");

			header("Location: index.php?mod=user&obj=group&do=$frmAction&success=1&grp_id=$grp_id");
			if ($link)
				mysql_close($link);
			exit();
		}
	}
	elseif ((!empty($_GET['success']) && $_GET['success']) || $_GET['do'] == "view"){
		$grp_id = $_GET['grp_id'];
		$success = $_GET['success'];
		$do = $_GET['do'];

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
	?>
		<h4><b><?=ucfirst($do)?> Group</b></h4>
		<div class="alert alert-info"><?php
			if ($success){
				echo(ucfirst($do) . " group successful.");
		}
		?></div>
	  	<form class="contact-form">
		  	<div class="form-group">
				<label for="fullname">Name</label>
				<input type="text" class="form-control" name="grp_name" id="grp_name" value="<?=$grp_name?>">
			</div>
			<div class="form-group">
				<label for="description">Description</label>
				<textarea class="form-control" name="grp_description" rows="5"></textarea>
			</div>
		</form>

	<h4>Group Roles</h4>
	<dl class="dl-horizontal">
  		<dt>User Management</dt>
  		<dd><?=$grp_permuser==0?"No Access":""?><?=$grp_permuser==101?"Administrator":""?></dd>

  		<dt>Contact Management</dt>
  		<dd><?=$grp_permcontact==0?"No Access":""?><?=$grp_permcontact==402?"Normal User":""?><?=$grp_permcontact==401?"Administrator":""?></dd>

  		<dt>Document Management</dt>
  		<dd><?=$grp_permdocument==0?"No Access":""?><?=$grp_permdocument==202?"Normal User":""?><?=$grp_permdocument==201?"Administrator":""?></dd>

  		<dt>Book Library</dt>
  		<dd><?=$grp_permlibrary==0?"No Access":""?><?=$grp_permlibrary==503?"Public":""?><?=$grp_permlibrary==502?"Member":""?><?=$grp_permlibrary==501?"Librarian":""?></dd>

  		<dt>Image Gallery</dt>
  		<dd><?=$grp_permimage==0?"No Access":""?><?=$grp_permimage==302?"Normal User":""?><?=$grp_permimage==301?"Administrator":""?></dd>
	</dl>
	<?php
		if (empty($_REQUEST['popup']))
			$okUrl = "location.href='" . $_SERVER['PHP_SELF'] . "?mod=user&obj=group'";
		else
			$okUrl = "window.close()";
	?>
	<button type="button" name="btnOK" onClick="<?=$okUrl?>" style="width:80px " class="btn btn-default">OK</button>
	<?php
		exit();
	}
	elseif (!empty($_GET['grp_id'])){
		$frmAction = "edit";
		$grp_id = $_GET['grp_id'];

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
	}
printBodyHeader();
?>
<script type="text/javascript" language="javascript">
function resetForm(form){
	form.grp_name.value = "";
	form.grp_description.value = "";
	form.grp_permuser[0].checked = true;
	form.grp_permcontact[0].checked = true;
	form.grp_permdocument[0].checked = true;
	form.grp_permlibrary[0].checked = true;
	form.grp_permimage[0].checked = true;
}
</script>
	<p class="module_sub_title"><b><?=ucfirst($frmAction)?> Group</b></p>
	<font color="#FF0000"><?=$error_message?></font>
	<form class="contact-form" name="frmGroupEdit" method="post" action="<?=$_SERVER['PHP_SELF']?>?mod=user&obj=group&do=<?=$frmAction?>">
	  	<div class="form-group">
			<label for="fullname">Name</label>
			<input type="text" class="form-control" name="grp_name" id="grp_name" value="<?=$grp_name?>">
		</div>
		<div class="form-group">
			<label for="description">Description</label>
			<textarea class="form-control" name="grp_description" rows="5"><?=nl2br($grp_description)?></textarea>
		</div>

		<h4>Group Roles</h4>
		<table>
			<tr>
				<td>User Management</td>
				<td>
					<input name="grp_permuser" type="radio" value="0"<?=$grp_permuser==0?" checked":""?>> No Access 
					<input name="grp_permuser" type="radio" value="101"<?=$grp_permuser==101?" checked":""?>> Administrator
				</td>
		  	</tr>
		  	<tr>
		  		<td>Contact Management</td>
		  		<td>
		  			<input name="grp_permcontact" type="radio" value="0"<?=$grp_permcontact==0?" checked":""?>> No Access 
		  			<input name="grp_permcontact" type="radio" value="402"<?=$grp_permcontact==402?" checked":""?>> Normal User
		  			<input name="grp_permcontact" type="radio" value="401"<?=$grp_permcontact==401?" checked":""?>> Administrator
		  		</td>
		  	</tr>
		  	<tr>
		  		<td>Document Management</td>
		  		<td>
		  			<input name="grp_permdocument" type="radio" value="0"<?=$grp_permdocument==0?" checked":""?>> No Access 
		  			<input name="grp_permdocument" type="radio" value="202"<?=$grp_permdocument==202?" checked":""?>> Normal User
		  			<input name="grp_permdocument" type="radio" value="201"<?=$grp_permdocument==201?" checked":""?>> Administrator
		  		</td>
		  	</tr>
		  	<tr>
		  		<td>Book Library</td>
		  		<td>
		  			<input name="grp_permlibrary" type="radio" value="0"<?=$grp_permlibrary==0?" checked":""?>> No Access 
		  			<input name="grp_permlibrary" type="radio" value="503"<?=$grp_permlibrary==503?" checked":""?>> Public 
		  			<input name="grp_permlibrary" type="radio" value="502"<?=$grp_permlibrary==502?" checked":""?>> Member
		  			<input name="grp_permlibrary" type="radio" value="501"<?=$grp_permlibrary==501?" checked":""?>> 
Librarian
</td>
		  </tr>
		  <tr><td >Image Gallery</td><td class="m1_td_content"><input name="grp_permimage" type="radio" value="0"<?=$grp_permimage==0?" checked":""?>>
No Access 
		         
		        <input name="grp_permimage" type="radio" value="302"<?=$grp_permimage==302?" checked":""?>>
Normal User	        <input name="grp_permimage" type="radio" value="301"<?=$grp_permimage==301?" checked":""?>>
Administrator </td>
		  </tr>
	  </table>
        <input type="hidden" name="frmAction" value="<?=$frmAction?>">
        <input type="hidden" name="grp_id" value="<?=$grp_id?>">
		<button type="submit" class="btn btn-primary" name="Submit">Submit</button>
		<a href="javascript:void(0)" class="btn btn-warning" onClick="resetForm(document.frmGroupEdit)">Reset</a>
		<a href="javascript:history.go(-1)" class="btn btn-info">Back</a>
	</form>