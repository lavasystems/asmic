<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<td>
<?php
require_once('../includes/functions.php');
if (isAllowed(array(401, 501, 101), $_SESSION['permissions']))
{
?>
	<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="m5_table_menu">
	<tr> 
	<td height="16">
	<div align="center"> <A HREF="list.php" class="link_menumodule">Main</A>
	<font color="#FFFFFF">&nbsp;|</font> 
	<A HREF="edit.php?mode=new" class="link_menumodule">Add New Entry</A>
	<font color="#FFFFFF">&nbsp;|</font> 
	<A HREF="export.php" class="link_menumodule">Export</A>
	<font color="#FFFFFF">&nbsp;|</font> 
	<A HREF="catman.php" class="link_menumodule">Category Management</A> 
	<font color="#FFFFFF">&nbsp;|&nbsp;</font>
	<A HREF="areaman.php" class="link_menumodule">Area of Expertise</A> 
	</div></td>
	</tr>
	</table>
<?php
}
else
{
?>
	<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="m5_table_menu">
	<tr> 
	<td height="16"> <div align="center"> <A HREF="list.php" class="link_menumodule">Main</A><font color="#FFFFFF">&nbsp;|</font> 
	<A HREF="export.php" class="link_menumodule">Export</A> 
	</div></td>
	</tr>
	</table>
<?php
}
?>
</td>
</tr>
<tr>
<td>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td class="module_sub_title">
<br>
<? 
if ($_SERVER['PHP_SELF'] == "/contact/list.php")
{
	echo "<tr><td class=\"ar11_content\">";
	echo "Logged in as: <b>".$_SESSION['usr_username']."</b>";
	echo "</td></tr>";
}
else if ($_SERVER['PHP_SELF'] == "/contact/edit.php")
{
	echo "Details Form";
}
else if ($_SERVER['PHP_SELF'] == "/contact/export.php")
{
	echo "Export";
}
else if ($_SERVER['PHP_SELF'] == "/contact/address.php")
{
	echo "Contact details";
}
else if ($_SERVER['PHP_SELF'] == "/contact/addressnew.php")
{
	echo "Contact details";
}
else if ($_SERVER['PHP_SELF'] == "/contact/catman.php")
{
	echo "Category management";
}
else if ($_SERVER['PHP_SELF'] == "/contact/addcatnew.php")
{
	echo "Add new category";
}
else if ($_SERVER['PHP_SELF'] == "/contact/areaedit.php")
{
	echo "Edit";
}
else if ($_SERVER['PHP_SELF'] == "/contact/areaman.php")
{
	echo "Area of expertise management";
}
else if ($_SERVER['PHP_SELF'] == "/contact/areanew.php")
{
	echo "Add New Area of Expertise";
}
else if ($_SERVER['PHP_SELF'] == "/contact/confirm.php")
{
	echo "Confirmation";
}
else if ($_SERVER['PHP_SELF'] == "/contact/searchnew.php")
{
	echo "Search results";
}
else if ($_SERVER['PHP_SELF'] == "/contact/detsearch.php")
{
	echo "Detailed search";
}
else if ($_SERVER['PHP_SELF'] == "/contact/catass.php")
{
	echo "Re-assign Categories";
}
else if ($_SERVER['PHP_SELF'] == "/contact/contriedit.php")
{
	echo "Edit Contact Contribution";
}
?>
</td>
</tr>
</table>
</td>
</tr>
</table>