<?php
include('class/classes.php');
GeneralProc::CleanOnlineUsers();
$preview=new preview();
$preview->head();
if($_GET['view']==2)
$preview->view2();
else
$preview->view1();
?>
