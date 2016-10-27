<? 

	ini_set('display_errors', 'on');
	error_reporting(E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
	
	// ** GET CONFIGURATION DATA **
	require_once('constants.inc');
	require_once(FILE_FUNCTIONS);
	require_once(FILE_CLASS_OPTIONS);
	require_once(FILE_CLASS_CONTACTLIST);
	require_once(FILE_CLASSES);

	// ** OPEN CONNECTION TO THE DATABASE **
	$db_link = openDatabase($db_hostname, $db_username, $db_password, $db_name);

	// ** CHECK FOR LOGIN **
	checkForLogin();

	if ($_SESSION['usertype'] != "admin") 
	{
		header("location: index.php");
	}
	
	// ** END INITIALIZATION *******************************************************	
	
	$mode = $_GET['mode'];
	$id = $_GET['id'];
	
	if ($mode = 1)
	{
		$groupsql = "SELECT * FROM " . TABLE_GROUPLIST . " WHERE groupid = '$id'
					LIMIT 1";
		$r_grouplist = mysql_query($groupsql, $db_link);
		$tlink = mysql_fetch_array($r_grouplist);
		
		$group_id = $tlink['groupid'];
		$group_name = $tlink['groupname'];
		$group_desc = $tlink['description'];
	}
		
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		$groupname = $_POST['groupname'];
		$description = $_POST['groupdesc'];
		
		$groupupd = "UPDATE " . TABLE_GROUPLIST . " SET groupname='$groupname', description='$description' WHERE groupid = '$id'";
		$execute= mysql_query($groupupd, $db_link);
		if ($execute == 1)
		{
			header("location: catman.php");
		}
	}
?>
<HTML>
<HEAD>
<TITLE>Contact Management</TITLE>
<LINK REL="stylesheet" HREF="styles.css" TYPE="text/css">
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
<META HTTP-EQUIV="EXPIRES" CONTENT="-1">
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</HEAD>
<BODY>
<A NAME="top"></A>
<P>
<CENTER>
<TABLE BORDER=0 CELLPADDING=0 CELLSPACING=0 WIDTH=570>
<TR>
      <TD>&nbsp;</TD>
</TR>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>
<table border="0" cellpadding="0" cellspacing="0" width="570">
<form name="form2" method="post" action="<? echo(FILE_SEARCHNEW); ?>">
<tr>
<td width="60%" class="listEntry">&nbsp;</td>
<td width="20%" class="listEntry"><input type="text" name="goTo"></td>
<td width="20%" class="listEntry"><input type="submit" name="Submit2" value="search"></td>
</tr>
<tr>
              <td width="60%" class="listEntry"><a href="list.php">home</a> |<a href="catman.php"> 
                category management</a> | edit category</td>
<td width="20%" class="listEntry"><strong><a href="detsearch.php">advanced search </a></strong></td>
<td width="20%" class="listEntry">&nbsp;</td>
</tr>
</form>
</table>
</td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>	
<TR>
  <TD>
  <table width="570" border="1" cellpadding="0" cellspacing="0">
    <tr>
      <td CLASS="headTitle">Add Category</td>
      </tr>
    <tr>
      <td class="infoBox">
	  
	  <table width="570" border="0" cellpadding="0" cellspacing="0">
	  <form method="post" action="addcat.php<? echo "?id=".$id; ?>" enctype="multipart/form-data">
	  <tr>
          <td class="listEntry" align="left">&nbsp;</td>
          <td class="listEntry" align="center">&nbsp;</td>
        </tr>
        <tr>
          <td width="20%" align="left" class="listEntry">&nbsp;Category Name:</td>
          <td class="listEntry" width="80%" align="left"><input type="text" name="groupname" value="<?=$group_name?>"></td>
        </tr>
		<tr>
          <td width="20%" align="left" class="listEntry">&nbsp;Category Description : </td>
          <td class="listEntry" width="80%" align="left"><textarea name="groupdesc"><? echo ($group_desc); ?></textarea></td>
        </tr>
		
<!-- This should be in the php -->
		<tr>
          <td width="20%" align="left" class="listEntry">&nbsp;</td>
          <td class="listEntry" width="80%" align="left"><input type="submit" name="Submit" value="Submit">
                    </td>
        </tr>
		</form>
      </table>	  
	  </td>
      </tr>
  </table> 
  </TD>
</TR>
</TABLE>
</CENTER>
</BODY>
</HTML>
