<?php
include("class/classes.php");
$html=new html("Css Library | Upload Css","Upload Css");
$session=new session($_GET['sid']);
$html->puthead();
$uploader=new uploader();
$uploader->uploadCss();
$uploader->showUploadForm();
$html->putfooter();