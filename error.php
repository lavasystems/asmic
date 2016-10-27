<?php
	$http_err = array (
		"400" => "Bad request",
		"401" => "Unauthorized",
		"402" => "Payment required",
		"403" => "Forbidden",
		"404" => "Not found",
		"500" => "Internal Error",
		"501" => "Not implemented",
		"502" => "Server overloaded",
		"503" => "Gateway timeout"
	);

	include_once("global_config.php");
?>
<p><?=!empty($http_err[$_REQUEST['err']])?$http_err[$_REQUEST['err']]:"Error " . $_REQUEST['err']?></p>
<p><a href="<?=$app_absolute_path?>index.php">Click here to return to ASMIC</a></p>