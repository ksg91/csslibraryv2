<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Search Result","Search Result");
$session=new session($_GET['sid']);
$html->puthead();
$search=new search($_GET['sb'],$_GET['q']);
$search->showResult();
$html->putfooter();
?>