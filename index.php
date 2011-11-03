<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Home","Available Css");
$session=new session($_GET['sid']);
$html->puthead();
$menu=new cssMenu();
$menu->showCssList();
$html->putfooter();
?>