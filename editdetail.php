<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Update Detail","Put New Details");
$EditCss=new EditCssDetail($_GET['id']);
$html->puthead();
$un=mysql_result(mysql_query("SELECT un FROM ksg_online WHERE sid='".$_GET['sid']."'"),0);
$uploader=mysql_result(mysql_query("SELECT uploader FROM ksg_cssdetail WHERE id='".$_GET['id']."'"),0);
if(GeneralProc::isAdmin($un) || GeneralProc::ownsCss($_GET['id']))
{
$EditCss->showForm();
if(!empty($_POST['name']) && !empty($_POST['desc']))
$EditCss->updateDetail();
}
$html->putfooter();
?>