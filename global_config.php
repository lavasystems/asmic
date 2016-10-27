<?php
error_reporting(E_ALL); 
ini_set("display_errors", 0);

//DATABASE CONNECTION SETTING
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_dbname = 'asm';

$url_prefix = "http://". $_SERVER['SERVER_NAME'];
if ($_SERVER['SERVER_PORT'] != "80")
	$url_prefix.=":". $_SERVER['SERVER_PORT'];
$url_prefix .= "/";

//GLOBAL VARIABLES
//$app_absolute_path1 = "/asmic/"; //DO NOT CHANGE THIS VARIABLE NAME
$site_name = 'ASMIC - Academy of Sciences Malaysia Information Center';
$root_images_folder = "images"; //ROOT LEVEL IMAGES FOLDER'S NAME
$mod_usr_name = "user"; //MODULE NAME FOR USER MANAGEMENT
$mod_doc_name = "document"; //MODULE NAME FOR DOCUMENT MANAGEMENT
$mod_lib_name = "library"; //MODULE NAME FOR BOOK LIBRARY
$mod_img_name = "image"; //MODULE NAME FOR IMAGE GALLERY
$mod_con_name = "contact"; //MODULE NAME FOR CONTACT MANAGEMENT

$app_absolute_path = 'http://localhost/asm/';

date_default_timezone_set('Asia/Kuala_Lumpur');

session_start();