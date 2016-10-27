<?php

	include_once("resizeimage.inc.php");
	$rimg=new RESIZEIMAGE("Blue.jpg");
	echo $rimg->error();
	//$rimg->resize_percentage(50);
	$rimg->resize(140, 140);
	//$rimg->close();
?>