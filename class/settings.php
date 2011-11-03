<?php

$db_host = "localhost";  // DB Host
$db_user = "root";  // DB User
$db_pass = "";  // DB Pass
$db_name = "csslibrary";  // DB Name
$dbc = mysql_connect($db_host, $db_user, $db_pass);
$dbs = mysql_select_db($db_name);
if(!$dbc)
{
	die("Error!!!! Cannot Connect to mysql database!!!!!!!");
}
?>