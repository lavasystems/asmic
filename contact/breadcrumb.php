<?php if ($_SERVER['PHP_SELF'] == "/contact/list.php") {
	echo "Main";
}else if ($_SERVER['PHP_SELF'] == "/contact/edit.php") {
	if ($mode == 'new') 
	{
		echo "<a href=\"list.php\">Main</a> &gt; Add New Entry</td><td>";
	}
	else
	{
		echo "<a href=\"list.php\">Main</a> &gt; Edit Entry</td><td>";
	}
}
else if ($_SERVER['PHP_SELF'] == "/contact/export.php")
{
?>
	<a href="list.php">Main</a> &gt; Export
<?	
}
else if ($_SERVER['PHP_SELF'] == "/contact/address.php")
{
?>
	<a href="list.php">Main</a> &gt; Contact Details
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/addressnew.php")
{
?>
	<a href="list.php">Main</a> &gt; Search Results
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/catman.php")
{
?>
	<a href="list.php">Main</a> &gt; Category Management
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/addcatnew.php")
{
?>
	<a href="list.php">Home</a> > <a href="catman.php">Category Management</a> > Edit Category
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/areaedit.php")
{
?>
	<a href="list.php">Home</a> > <a href="areaman.php">Area of Expertise Management</a> > Edit Area of Expertise
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/areaman.php")
{
?>
	<a href="list.php">Main</a> &gt; Area of Expertise Management
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/areanew.php")
{
?>
	<a href="list.php">Home</a> > <a href="areaman.php">Area of Expertise Management</a> > Add New Area of Expertise
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/confirm.php")
{
?>
	<a href="list.php">Main</a> &gt; Confirmation 
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/searchnew.php")
{
?>
	<a href="list.php">Main</a> &gt; Search Results
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/detsearch.php")
{
?>
	<a href="list.php">Main</a> &gt; Detailed Search
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/catass.php")
{
?>
	<a href="list.php">Main</a> &gt; <a href="catman.php">Category Management</a> &gt; Re-assign Categories
<?
}
else if ($_SERVER['PHP_SELF'] == "/contact/contriedit.php")
{
?>
	<a href="list.php">Home</a> > <a href="edit.php?id=<? echo $id; ?>"> Edit Contact</a> > Edit Contact Contribution
<?
}
?>