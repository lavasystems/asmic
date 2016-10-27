<?
if (isAllowed(array(501), $_SESSION['permissions'])){
?>
<table width="100%"  border="0" cellpadding="0" cellspacing="0" class="m2_table_menu">
  <tr> 
    <td height="16">
	<div align="center">
	<a href="index.php" class="link_menumodule">Main</a> |
	<a href="category.php" class="link_menumodule">Category</a> |
	<a href="bookissue.php" class="link_menumodule">Issue Book</a> | 
	<a href="bookreturn.php" class="link_menumodule">Return Book</a> | 
	<a href="search_books.php" class="link_menumodule">Search</a> | 
	<a href="reports.php" class="link_menumodule">Reports</a> | 
	<a href="settings.php" class="link_menumodule">Loan Duration</a>
    </div>
	</td>
  </tr>
</table>
<?
}
?>
