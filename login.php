<?php
include("class/classes.php");
GeneralProc::CleanOnlineUsers();
$html=new html("Css Library | Login","Login Into Css Library");
$html->puthead();
if(isset($_POST['un']) && !empty($_POST['un']) && isset($_POST['pw']) && !empty($_POST['pw']))
{
  $login=new login($_POST['un'],$_POST['pw']);
  $login->doLogin();
}
else
{
  $login=new login();
  $login->showLoginForm(NULL,NULL);
}
$html->putfooter();
?>