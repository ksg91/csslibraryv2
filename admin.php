<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Admin Panel","Admin Panel");
$session=new session($_GET['sid']);
$admin=new admin();
$html->puthead();
if($_GET['task']=="showPendingValidation")
$admin->showPendingValidation();
else if($_GET['task']=="banuser")
$admin->banUser();
else if($_GET['task']=="log")
$admin->showLog();
else if($_GET['task']=="deletecss")
$admin->deleteCss($_GET['id']);
else if($_GET['task']=="deletecomment")
$admin->deleteComment($_GET['id']);
else
$admin->showAdminMenu();
$html->putfooter();
