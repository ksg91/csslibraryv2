<?php
include("class/classes.php");
$html=new html("Css Library | Comments","Comments");
$session=new session($_GET['sid']);
$html->puthead();
$comments=new comments($_GET['id']);
if(isset($_POST['comment']))
$comments->addComment();
$comments->showComments();
echo "<div class=\"gap\"></div>";
$html->putfooter();
?>
