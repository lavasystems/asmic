<?php
/* PDO database values for the application.*/
$db_server      = "localhost";
$db_user        = "lavakode_asmic";
$db_password    = "uNQyJKVTD1q4";
$db_name        = "lavakode_asmic";

/* connecting to the database using PHP PDO. all the database connectivity in the application is handled using PDO only for injection proof queries.*/
try {
	$db = new PDO("mysql:host={$db_server};dbname={$db_name}", $db_user, $db_password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
	echo "Error connecting to the database.";
}