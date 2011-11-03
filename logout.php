<?php
include("class/settings.php");
$sid=$_GET['sid'];
mysql_query("DELETE FROM ksg_online WHERE sid='".$sid."' ");
header("Location: index.php");
?>