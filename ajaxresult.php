<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$search=new search($_GET['sb'],$_GET['q']);
$search->showResult();
