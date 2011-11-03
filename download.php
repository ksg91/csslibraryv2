<?php
include("class/classes.php");
$id=$_GET['id'];
$link=mysql_result(mysql_query("SELECT link FROM ksg_cssdetail WHERE id=$id"),0);
header('Content-disposition: attachment; filename="stylesheet.css" ');
header('Content-type: text/css');
readfile("css/$link");
?>