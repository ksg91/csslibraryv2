<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$un=$_GET['un'];
$html=new html("Css Library | View Profile","Profile Of $un");
$session=new session($_GET['sid']);
$html->puthead();
$profile=new profile($un);
$profile->showProfile();
$html->putfooter();
?>