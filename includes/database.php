<?php
/*
includes : database
*/

function db_mysql_connect() 
{
    global $db_host, $db_username, $db_password, $db_dbname; 
    global $MYSQL_ERRNO, $MYSQL_ERROR; 
       
    $link_id = mysql_connect($db_host, $db_username, $db_password); 
    if(!$link_id) { 
      $MYSQL_ERRNO = 0; 
      $MYSQL_ERROR = "Connection failed to the host $dbhost."; 
      return 0; 
    } 
    else if(!mysql_select_db($db_dbname)) { 
      $MYSQL_ERRNO = mysql_errno(); 
      $MYSQL_ERROR = mysql_error(); 
      return 0; 
    } 
    else return $link_id; 
}

?>