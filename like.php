<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Liked A Css","Liked A Css");
$session=new session($_GET['sid']);
$html->puthead();
if($session->checkLogin())
{ 
  $id=$_GET['id'];
  if(GeneralProc::likeCss($session->un,$id))
  echo "<div class=\"error\">You already liked it!</div>";
  else
  {
    echo "<div class=\"notify\">You Liked It!</div>";
    mysql_query("INSERT INTO ksg_liked SET un='".$session->un."',cssid=$id ");
    GeneralProc::incrementField("ksg_cssdetail","liked","id=$id");
  }
}
$html->putfooter();