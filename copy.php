<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Copy Css","Copy Css");
$session=new session($_GET['sid']);
$html->puthead();
$copy=new copy($_GET['id']);
$copy->showTextArea();
$html->putfooter();
?>