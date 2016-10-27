<?
if (eregi('index.php', $_SERVER['PHP_SELF'])) {
?>
<span class="ar11_content">Logged in as: <b><? echo $_SESSION['usr_username']; ?></b></span>
<?
}
?>