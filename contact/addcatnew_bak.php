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

	// ** END INITIALIZATION *******************************************************	
	
	if ((!empty($_POST["Submit"])) && ($_POST["Submit"] == "Submit"))
	{
		$groupname = $_POST['groupname'];
		$groupdesc = $_POST['groupdesc'];
		
		$r_newGroupID = mysql_query("SELECT groupid FROM " . TABLE_GROUPLIST . " ORDER BY groupid DESC LIMIT 1", $db_link);
		$t_newGroupID = mysql_fetch_array($r_newGroupID);
		$newGroupID = $t_newGroupID['groupid'];
		$newGroupID = $newGroupID + 1;
		
		$groupupd = "INSERT INTO " . TABLE_GROUPLIST . " VALUES ($newGroupID, '$groupname', '$groupdesc')";
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
category management</a> | add category</td>
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
	  <form method="post" action="addcatnew.php" enctype="multipart/form-data">
	  <tr>
          <td class="listEntry" align="left">&nbsp;</td>
          <td class="listEntry" align="center">&nbsp;</td>
        </tr>
        <tr>
          <td width="20%" align="left" class="listEntry">&nbsp;Category Name:</td>
          <td class="listEntry" width="80%" align="left" class="formTextbox"><input type="text" name="groupname" value=""></td>
        </tr>
		<tr>
          <td width="20%" align="left" class="listEntry">&nbsp;Category Description : </td>
          <td class="listEntry" width="80%" align="left" class="formTextarea"><textarea name="groupdesc"></textarea></td>
        </tr>
		
		<tr>
          <td width="20%" align="left" class="listEntry">&nbsp;</td>
          <td class="listEntry" width="80%" align="left"><input type="submit" name="Submit" value="Submit">
            <input type="reset" name="Submit2" value="Reset"></td>
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
