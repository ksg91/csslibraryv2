<?php
error_reporting(0);
include("settings.php");
class html extends session
{
  var $title,$header;
  function __construct($title,$header)
  {
    parent::__construct($_GET['sid']);
    $this->title=$title;
    $this->header=$header;
  }
  function puthead()
  {
    echo "<?xml version=\"1.0\" encoding=\"utf-8\" ? >";
    echo "<!DOCTYPE html PUBLIC \"-//WAPFORUM//DTD XHTML Mobile 1.0//EN\" \"http://www.wapforum.org/DTD/xhtml-mobile10.dtd\">";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
    echo "<head>";
    echo "<title>$this->title</title>";
    echo "<meta http-equiv=\"Content-Type\" content=\"application/vnd.wap.xhtml+xml; charset=UTF-8\" />";
    echo "<meta http-equiv=\"Pragma\" content=\"no-cache\" />";
    echo "<meta http-equiv=\"Cache-Control\" content=\"max-age=0\" />";
    echo "<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />";
    echo "<meta http-equiv=\"Cache-Control\" content=\"no-store\" />";
    echo "<meta http-equiv=\"Expires\" content=\"-1\"/>";
    echo "<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\" />";
    echo "</head>";
    echo "<body>";
    echo "<div class=\"logo\"><img src=\"imgs/css_library_png.png\" alt=\"*\" /></div>";
    echo "<div class=\"head\"><div class=\"center\">$this->header&hellip;</div></div>";
  }
  function  putfooter()
  {
    echo "<div class=\"gap\"></div>";
    echo "<div class=\"footer\"><div class=\"center\">";
    if($this->checkLogin())
    {
      echo "<a class=\"button\" href=\"upload.php?sid=".$_GET['sid']."\">Upload CSS</a>";
      if(GeneralProc::isAdmin($this->un))
      echo "<br /><a class=\"button\" href=\"admin.php?sid=".$_GET['sid']."\">Admin Panel</a>";
      echo "<br /><a class=\"button\" href=\"logout.php?sid=".$_GET['sid']."\">Logout</a>";
    }
    else
    echo "<br /><a class=\"button\" href=\"login.php\">Login</a>";
    echo "</div>";
    echo "<div class=\"title\">We have total ".GeneralProc::totalCss()." Css</div>";
    echo "<div class=\"head\"><a href=\"index.php?sid=".$_GET['sid']."\">Home</a>|<a href=\"http://mobile.web.tr\">Mobile.Web.tr</a></div>";
    echo "</body>";
    echo "</html>";
  }
};
class session
{
  public $un;
  protected $session;
  function __construct($sess)
  { 
    $this->session=$sess;
    if($this->checkLogin())
    {
      $this->un=mysql_result(mysql_query("SELECT un FROM ksg_online WHERE sid='".$this->session."' "),0);
      mysql_query("UPDATE ksg_online SET lastact=SYSDATE() WHERE sid='".$this->session."' ");
    }
  }
  function checkLogin()
  {
    $check=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_online WHERE sid='".$this->session."' "),0);
    return $check;
  }
};
class uploader extends session
{
  var $fname,$name,$desc,$size,$type;
  function __construct()
  {
    parent::__construct($_GET['sid']);
    $this->fname=$_FILES['file']['name'];
    $this->size=$_FILES['file']['size'];
    $this->type=$_FILES['file']['type'];
    $this->name=$_POST['name'];
    $this->desc=$_POST['desc'];
  }
  function showUploadForm()
  {
    if(parent::checkLogin())
    {
      echo "<form action=\"upload.php?sid=$this->session\" method=\"post\" enctype=\"multipart/form-data\">";
      echo "<b>Css Name:</b><br/>
            <input type=\"text\" maxlength=\"20\" name=\"name\" />
            <br/>
            <b>Description:</b><br/>
            <input type=\"text\" maxlength=\"100\" name=\"desc\" />
            <br/>
            <b>Select File:</b><br/>
            <input type=\"file\" name=\"file\" />
            <br />
            <input type=\"submit\" name=\"submit\" value=\"Add\" />
            </form>";
    }
    else
    {
      echo "<div class=\"error\">You Need To Be Logged In Inorder To Upload CSS.</div>";
    }
  }
  function verifyCss()
  {
    if($this->size==0)
    {
      echo "<div class=\"notify\">Please Select File.</div>";
      return false;
    }
    if(empty($_POST['name']) OR empty($_POST['desc']))
    {
      echo "<div class=\"error\">All Fields Are Mandetory.</div>";
      return false;
    }
    if($_FILES['file']['size']>10000 OR $_FILES['file']['size']<1000)
    {
      echo "<div class=\"warn\">File size is invalid.</div>";
      return false;
    }
    if($this->type!="text/css")
    {
      echo "<div class=\"warn\">File Type Is Invalid.</div>";
      return false;
    }
    else
    {
      return true;
    }
  }
  function uploadCss()
  {
    if($this->verifyCss())
    {
      $filename = md5(uniqid(rand(), true));
      $ncl="css/".$filename.".css";
      $us=move_uploaded_file($_FILES["file"]["tmp_name"],$ncl);
      if($us)
      {
        $q=mysql_query("INSERT INTO ksg_cssdetail SET name='".$this->name."',`desc`='".$this->desc."',link='$filename.css',uploader='".$this->un."',date=SYSDATE() " );
        if($q)
        {
          echo "<div class=\"notify\">Css is uploaded successfully and will be shown after verified by admins.";
        }
      }
    }
  }
};
class login
{
  private $un,$pw;
  function __construct($user,$pass)
  {
    $this->un=$user;
    $this->pw=$pass;
  }
  function showLoginForm()
  {
    echo "<div class=\"notify\">Use Your Mobile.Web.Tr Control Panel username and password to login</div>";
    echo "<form action=\"login.php\" method=\"post\"";
    echo "<b>Username</b>:<br />";
    echo "<input type=\"text\" name=\"un\" maxlength=\"30\" /><br />";
    echo "<b>Password</b><br />";
    echo "<input type=\"password\" name=\"pw\" maxlength=\"30\" /><br />";
    echo "<input type=\"submit\" value=\"Login!\"";
    echo "</form>";
  }
  function doLogin()
  {
    //$login=file_get_contents("http://mobile.web.tr/api/CheckOwnerUserNameAndPw.ashx?AspxAutoDetectCookieSupport=1&txtUsername=$this->un&txtPassword=$this->pw&subdomain=support");
    $login=1;
    if($login==1)
    {
      if(GeneralProc::isBanned($this->un))
      {
        echo "<div class=\"warn\">You are Banned.</div>";
        return;
      }
      if(!GeneralProc::userExist($this->un))
      {
        mysql_query("INSERT INTO ksg_users SET un='".$this->un."'");
      }
      $token=rand(0,100).$this->un.rand(0,100);
      $sess=md5($token);
      $add=mysql_query("INSERT INTO ksg_online SET un='".$this->un."',sid='".$sess."',lastact=SYSDATE()");
      if($add)
      {
        echo "<div class=\"notify\">You Are Logged In Successfully.</div>";
        echo "<a href=\"index.php?sid=$sess\">Continue</a>";
      }
    }
    else if(!$login)
    {
      echo "<div class=\"warn\">Username and Password do not match</div>";
    }
    else
    {
      echo "Contact Administrator: $login";
    }
  }
};
class cssMenu extends session
{
  var $ob,$pg,$totalcss,$totalpage;
  function __construct()
  {
    parent::__construct($_GET['sid']);
    $this->ob=$_GET['ob'];
    $this->totalcss=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_cssdetail WHERE validated=1"),0);
    $this->totalpage=ceil($this->totalcss/10);
    $this->pg=(($this->totalpage>=$_GET['pg'] AND $_GET['pg']!=0)?$_GET['pg']:1);
    
  }
  function showCssList()
  {
    $i=1;
    $from = (($this->pg * 10) - 10);
    if($this->ob=="liked")
    $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE deleted=0 AND validated=1 ORDER BY liked desc LIMIT $from,10");
    else if($this->ob=="recent")
    $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE deleted=0 AND validated=1 ORDER BY date desc LIMIT $from, 10");
    else if($this->ob=="commented")
    $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE deleted=0 AND validated=1 ORDER BY comments desc LIMIT $from, 10");
    else
    $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE deleted=0 AND validated=1 ORDER BY name LIMIT $from, 10");
    echo "<ul>";
    while($css=mysql_fetch_array($sql))
    {
      $id=$css['id'];
      $name=$css['name'];
      $desc=$css['desc'];
      $liked=$css['liked'];
      $comments=$css['comments'];
      $uploader=$css['uploader'];
      $date=$css['date'];
      if($i%2==0)
      echo "<li class=\"listli even\">";
      else
      echo "<li class=\"listli odd\">";
      echo "<img src=\"imgs/code.png\" alt=\"\" /><b>$name</b>";
      echo "<br />";
      echo "<small>$desc</small><br />";
      echo "<span class=\"xxsmall\">$liked users liked this css<br />";
      echo "Uploaded by <a href=\"user.php?un=$uploader&sid=".$_GET['sid']."\">$uploader</a> on $date<br />";
      echo "<a class=\"two\" href=\"preview.php?id=$id&sid=".$_GET['sid']."\">Preview</a> | ";
      echo "<a class=\"two\" href=\"copy.php?id=$id&sid=".$_GET['sid']."\">Copy</a> | "; 
      echo "<a class=\"two\" href=\"download.php?id=$id&sid=".$_GET['sid']."\">Download</a> | "; 
      echo "<a class=\"two\" href=\"comments.php?id=$id&sid=".$_GET['sid']."\">Comments ($comments)</a>";
      if(session::checkLogin())
      {
        if(GeneralProc::likeCss($this->un,$id))
        echo " | <a>You Like This</a>";
        else
        echo " | <a class=\"two\" href=\"like.php?id=$id&sid=".$_GET['sid']."\">Like!</a>";
        if(GeneralProc::isAdmin($this->un))
        {
          echo "<br /><a href=\"admin.php?id=$id&sid=$this->session&task=deletecss\">Delete Css</a> | ";
        }
        if(GeneralProc::isAdmin($this->un) || GeneralProc::ownsCss($id))
        echo "<a href=\"editdetail.php?id=$id&sid=$this->session\"> Edit Detail</a>";
      }
      echo "</span></li>";
      $i++;
    }
    echo "</ul>";
    echo "<b>Page $this->pg Of $this->totalpage";
    echo "<form action=\"index.php\" >";
    echo "Jump To Page:<br /><input type=\"text\" name=\"pg\" />";
    echo "<input type=\"hidden\" name=\"sid\" value=\"$this->session\" />";
    echo "<input type=\"hidden\" name=\"ob\" value=\"$this->ob\" />";
    echo "<input type=\"submit\" value=\"Go\" /></form>";
    echo "<form action=\"search.php\" >";
    echo "Search:<br /><input type=\"text\" name=\"q\" />";
    echo "<input type=\"checkbox\" name=\"sb\" value=\"user\" />User's Css";
    echo "<input type=\"hidden\" name=\"sid\" value=\"$this->session\" />";
    echo "<input type=\"hidden\" name=\"ob\" value=\"$this->ob\" />";
    echo "<input type=\"submit\" value=\"Go\" /></form>";
    echo "<hr />";
    echo "<span class=\"xxsmall\"><a href=\"index.php?ob=liked&sid=$this->session\">Most Liked</a> | ";
    echo "<a href=\"index.php?ob=commented&sid=$this->session\">Most Commented</a> | ";
    echo "<a href=\"index.php?ob=recent&sid=$this->session\">Recently Uploaded</a> </span> ";
    echo "<hr />";
  }
};
class comments extends session
{
  var $cssid;
  function __construct($id)
  {
    parent::__construct($_GET['sid']);
    $this->cssid=$id;
  }
  function showComments()
  {
    if(parent::checkLogin())
    {
      echo "</div class=\"gap\"></div>";
      echo "<div class=\"title\">Add Comment</div>";
      echo "<form action=\"comments.php?id=$this->cssid&sid=".$this->session."\" method=\"post\">";
      echo "<textarea name=\"comment\">";
      echo "</textarea><br />";
      echo "<input type=\"submit\" class=\"button\" value=\"comment\" />";
      echo "</form>";
    }
    $i=0;
    $comments=mysql_query("SELECT * FROM ksg_comment WHERE cssid='".$this->cssid."' ORDER BY date desc ");
    echo "<ul>";
    while($css=mysql_fetch_array($comments))
    {
      $id=$css['id'];
      $un=$css['un'];
      $comment=$css['comment'];
      $date=$css['date'];
      if($i%2==0)
      echo "<li class=\"listli even\" style=\"text-align:left\">";
      else
      echo "<li class=\"listli odd\" style=\"text-align:left\">";
      echo "<img src=\"imgs/code.png\" alt=\"\" /><b>$un on $date</b><a href=\"admin.php?id=$id&sid=$this->session&task=deletecomment\" class=\"button\">Delete Comment</a>";
      echo "<br />";
      echo "<span class=\"xxsmall\"><pre>$comment</pre><br /></span>";
      echo "</li>";
      $i++;
    }
    echo "</ul>";
  }
  function addComment()
  {
    if(parent::checkLogin())
    {
      $comment=mysql_real_escape_string($_POST['comment']);
      $q=mysql_query("INSERT INTO ksg_comment SET cssid='".$this->cssid."',comment='".$comment."',date=SYSDATE(),un='".$this->un."'");
      GeneralProc::incrementField("ksg_cssdetail","comments","id=$this->cssid");
      echo "<div class=\"notify\">Comment Added Successfully."; 
    }
    else
    {
      echo "<div class=\"warn\">You are not logged in.</div>";
    }
  }
};
class profile
{
  var $exist=true,$un,$avatar,$csscount,$ban,$online;
  function __construct($un)
  {
    if(!GeneralProc::userExist($un))
    {
      $this->exist=false;
      return;
    }
    $online=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_online WHERE un='".$un."'"),0);
    $sql=mysql_fetch_array(mysql_query("SELECT * FROM ksg_users WHERE un='".$un."'"));
    $this->un=$un;
    $this->avatar="<img src=\"http://support.mobile.web.tr/mobile/avatar.ashx?subdomain=support.mobile.web.tr&un=$this->un\" alt=\"$un\" />";
    $this->csscount=$sql['csscount'];
    $this->ban=$sql['ban'];
    $this->online=($online>0?true:false);
  }
  function showProfile()
  {
    if(!$this->exist)
    {
      echo "<div class=\"warn\">User Doesnt Exist.</div>";
      return;
    }
    echo $this->avatar;
    echo "<ul>";
    echo "<li class=\"listli odd\">";
    echo "Total Css Uploaded:<a href=\"search.php?q=$this->un&sb=user&sid=&ob=\">$this->csscount</a></li>";
    echo "<li class=\"listli even\">";
    if($this->online)
    echo "Online";
    else
    echo "Offline";
    echo "</li>";
  }
};
class Search
{
  var $searchby,$query;
  function __construct($sb,$q)
  {
    $this->searchby=$sb;
    $this->query=$q;
  }
  function showResult()
  {
    if(strlen($this->query)<3)
    {
      echo "<div class=\"warn\">Search term must be greater than 2 char.";
      return;
    }
    if($this->searchby=="user")
    {
      $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE uploader='".$this->query."' AND validated=1 AND deleted=0");
      $count=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_cssdetail WHERE uploader='".$this->query."' AND validated=1 AND deleted=0"),0);
    }
    else
    {
      $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE `desc` LIKE '%$this->query%' OR name LIKE '%$this->query%' AND validated=1 AND deleted=0");
      $count=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_cssdetail WHERE `desc` LIKE '%$this->query%' OR name LIKE '%$this->query%' AND validated=1 AND deleted=0"),0);
    }
    echo "<div class=\"title\">We got $count result for $this->query</div>";
    echo "<ul>";
    $i=1;
    while($css=mysql_fetch_array($sql))
    {
      $id=$css['id'];
      $name=$css['name'];
      $desc=$css['desc'];
      $liked=$css['liked'];
      $comments=$css['comments'];
      $uploader=$css['uploader'];
      $date=$css['date'];
      if($i%2==0)
      echo "<li class=\"listli even\">";
      else
      echo "<li class=\"listli odd\">";
      echo "<img src=\"imgs/code.png\" alt=\"\" /><b>$name</b>";
      echo "<br />";
      echo "<small>$desc</small><br />";
      echo "<span class=\"xxsmall\">$liked users liked this css<br />";
      echo "Uploaded by <a href=\"user.php?un=$uploader&sid=".$_GET['sid']."\">$uploader</a> on $date<br />";
      echo "<a class=\"two\" href=\"preview.php?id=$id&sid=".$_GET['sid']."\">Preview</a> | "; 
      echo "<a class=\"two\" href=\"download.php?id=$id&sid=".$_GET['sid']."\">Download</a> | "; 
      echo "<a class=\"two\" href=\"comments.php?id=$id&sid=".$_GET['sid']."\">Comments ($comments)</a>";
      if(session::checkLogin())
      {
        if(GeneralProc::likeCss($this->un,$id))
        echo " | <a>You Like This</a>";
        else
        echo " | <a class=\"two\" href=\"like.php?id=$id&sid=".$_GET['sid']."\">Like!</a>";
        if(GeneralProc::isAdmin($this->un))
        {
          echo "<br /><a href=\"admin.php?id=$id&sid=$this->session&task=deletecss\" class=\"button\">Delete Css</a>";
        }
      }
      echo "</span></li>";
      $i++;
      echo "</ul>";
    }
  }
};
class admin extends session
{
  var $isAdmin;
  function __construct()
  {
    parent::__construct($_GET['sid']);
    $this->isAdmin=mysql_result(mysql_query("SELECT isAdmin FROM ksg_users WHERE un='".$this->un."' "),0);
  }
  function showPendingValidation()
  {
    if(isset($_GET['id']))
    $this->validateCss($_GET['id']);
    $sql=mysql_query("SELECT * FROM ksg_cssdetail WHERE deleted=0 AND validated=0 ");
    echo "<ul>";
    while($css=mysql_fetch_array($sql))
    {
      $id=$css['id'];
      $name=$css['name'];
      $desc=$css['desc'];
      $uploader=$css['uploader'];
      $date=$css['date'];
      if($i%2==0)
      echo "<li class=\"listli even\">";
      else
      echo "<li class=\"listli odd\">";
      echo "<img src=\"imgs/code.png\" alt=\"\" /><b>$name</b>";
      echo "<br />";
      echo "<small>$desc</small><br />";
      echo "Uploaded by <a href=\"user.php?un=$uploader&sid=".$_GET['sid']."\">$uploader</a> on $date<br />";
      echo "<a class=\"two\" href=\"preview.php?id=$id&sid=".$_GET['sid']."\">Preview</a> | ";
      echo "<a href=\"admin.php?id=$id&task=showPendingValidation&sid=$this->session\">Accept</a> | "; 
      echo "<a href=\"admin.php?id=$id&task=deletecss&sid=$this->session\">Reject</a>";
      echo "<a href=\"editdetail.php?id=$id&sid=$this->session\"> | Edit Detail</a>";
    }   
  }
  function validateCss($id)
  {
    if($this->isAdmin)
    {
      $uploader=mysql_result(mysql_query("SELECT uploader FROM ksg_cssdetail WHERE id=$id"),0);
      GeneralProc::incrementField("ksg_users","csscount","un='".$uploader."'");
      mysql_query("UPDATE ksg_cssdetail SET validated=1 WHERE id=$id");
      echo "<div class=\"notify\">Css Validated Successfully.</div>";
      $log="$this->un Validated Css Id $id.";
      mysql_query("INSERT INTO ksg_log SET log='".$log."',date=SYSDATE()");
    }
  }
  function showLog()
  {
     if(!isset($_GET['page'])){ 
	   $page = 1; 
	   } else { 
	   $page = $_GET['page']; 
	   } 
	   $from = (($page * 20) - 20);
	   $log=mysql_query("SELECT * FROM ksg_log ORDER By date desc LIMIT $from, 20 ");
	   if($log<1)
	   echo "<div class=\"warn\">No Log.</div>";
	   else
	   {
	     $i=0;
	     echo "<ul>";
	     while($ksg=mysql_fetch_array($log))
	     {
	       if($i%2==0)
         echo "<li class=\"listli even\">";
         else
         echo "<li class=\"listli odd\">";
         echo "<span style=\"text-align:left\">".$ksg[2]."</span>";
         echo "<br /><span class=\"xxsmall\">$ksg[1]</span></li>";
         $i++;
       }
       echo "</ul>";
     }
	   $total_results = mysql_result(mysql_query("SELECT COUNT(*) as Num FROM ksg_log "),0); 			
	   $total_pages = ceil($total_results / 20); 
    	if($page < $total_pages){ 
        	$next = ($page + 1); 
	       echo "<br /><a class=\"button\" href=\"admin.php?page=$next&amp;task=log&amp;sid=$this->sid\">Next &#187;</a></div>"; 
	       } 
  	if($page > 1){ 
      	$prev = ($page - 1);
      	echo "<br /><a class=\"button\" href=\"admin.php?page=$prev&amp;task=log&amp;sid=$this->sid\">&#171; Prev</a></div>"; 
	  } 
	  echo "<br/>Page $page of $total_pages";
  }
  function deleteCss($id)
  {
    if($this->isAdmin)
    {
      mysql_query("UPDATE ksg_cssdetail SET deleted=1 WHERE id=$id");
      echo "<div class=\"notify\">Css Deleted Successfully.</div>";
      $log="$this->un Deleted Css Id $id.";
      mysql_query("INSERT INTO ksg_log SET log='".$log."',date=SYSDATE()");
    }
    else
    {
      echo "<div class=\"warn\">You Are Not An Admin.</div>";
    }
  }
  function deleteComment($id)
  {
    if($this->isAdmin)
    {
      mysql_query("DELETE FROM ksg_comment WHERE id=$id");
      echo "<div class=\"notify\">Comment Deleted Successfully.</div>";
      $log="$this->un Deleted Comment Id $id.";
      mysql_query("INSERT INTO ksg_log SET log='".$log."',date=SYSDATE()");
    }
    else
    {
      echo "<div class=\"warn\">You Are Not An Admin.</div>";
    }
  }
  function banUser($user)
  {
    if($this->isAdmin)
    {
      echo "<div class=\"title\">Ban User</div>";
      if(!empty($_POST['user']))
      {
        if(GeneralProc::userExist($_POST['user']))
        {
          $sql=mysql_query("UPDATE ksg_users SET ban=1 WHERE un='".$_POST['user']."' ");
          if($sql)
          {
            echo "<div class=\"notify\">".$_POST['user']." is banned.</div>";
          }
        }
        else
        echo "<div class=\"warn\">User Does not Exist.</div>";
      }
      echo "<form action=\"admin.php?sid=$this->session&task=banuser\" method=\"post\">";
      echo "<b>Username</b>:<br />";
      echo "<input type=\"text\" name=\"user\" /><br />";
      echo "<input type=\"submit\" value=\"Ban\" />";
      echo "</form>";
    }
    else
    {
      echo "<div class=\"warn\">You Are Not An Admin.</div>";
    }
  }
  function showAdminMenu()
  {
    if($this->isAdmin)
    {
      $pending=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_cssdetail WHERE deleted=0 AND validated=0 "),0);
      echo "<br /><a href=\"admin.php?sid=$this->session&task=showPendingValidation\" class=\"button\">Pending Validation($pending)</a>";
      echo "<br /><a href=\"admin.php?sid=$this->session&task=log\" class=\"button\">Log</a>";
      echo "<br /><a href=\"admin.php?sid=$this->session&task=banuser\" class=\"button\">Ban User</a>"; 
    }
    else
    {
      echo "<div class=\"warn\">You Are Not An Admin.</div>";
    }
  }
};
class GeneralProc
{
  static function ownsCss($cssid)
  {
    $un=strtolower(mysql_result(mysql_query("SELECT un FROM ksg_online WHERE sid='".$_GET['sid']."'"),0));
    $uploader=strtolower(mysql_result(mysql_query("SELECT uploader FROM ksg_cssdetail WHERE id='".$cssid."'"),0));
    if($un==$uploader)
    return true;
    else
    return false;
  }
  static function totalCss()
  {
    return $count=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_cssdetail WHERE validated=1 AND deleted=0"),0);
  }
  static function OnlineUserCount()
  {
    $online=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_online WHERE 1"),0);
    return $online;
  }
  static function isValidated($id)
  {
    $v=mysql_result(mysql_query("SELECT validated FROM ksg_cssdetail WHERE id=$id"),0);
  }
  static function incrementField($table,$field,$condition)
  {
    $i=mysql_result(mysql_query("SELECT $field FROM $table WHERE $condition"),0);
    $i++;
    $q=mysql_query("UPDATE $table SET $field=$i WHERE $condition");
    if(!$q)
    echo mysql_error();
  }
  static function isBanned($un)
  {
    $ban=mysql_result(mysql_query("SELECT ban FROM ksg_users WHERE un='".$un."'"),0);
    return $ban;
  }
  static function isAdmin($un)
  {
    $ia=mysql_result(mysql_query("SELECT isAdmin FROM ksg_users WHERE un='".$un."'"),0);
    return $ia;
  }
  static function likeCss($un,$id)
  {
    $liked=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_liked WHERE un='".$un."' AND cssid=$id"),0);
    return $liked;  
  }
  static function userExist($un)
  {
    $user=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_users WHERE un='".$un."'"),0);
    return $user;  
  }
  function cleanOnlineUsers()
  {
    $time=mysql_result(mysql_query("SELECT SYSDATE() FROM dual"),0);
    $ot=strtotime($time);
    $et=strtotime($time)-600;
    $time=date("Y-m-d H:i:s",$et);
    mysql_query("DELETE FROM ksg_online WHERE lastact<'".$time."'");
  }
};
class preview extends session
{
  var $csslink,$cssid;
  function __construct()
  {
    parent::__construct($_GET['sid']);
    $this->cssid=$_GET['id'];
    $this->csslink=mysql_result(mysql_query("SELECT link FROM ksg_cssdetail WHERE id='".$this->cssid."'"),0);
  }
  function head()
  {
    echo "<?xml version=\"1.0\" encoding=\"utf-8\" ? >";
    echo "<!DOCTYPE html PUBLIC \"-//WAPFORUM//DTD XHTML Mobile 1.0//EN\" \"http://www.wapforum.org/DTD/xhtml-mobile10.dtd\">";
    echo "<html xmlns=\"http://www.w3.org/1999/xhtml\">";
    echo "<head>";
    echo "<title>Preview Css</title>";
    echo "<meta http-equiv=\"Content-Type\" content=\"application/vnd.wap.xhtml+xml; charset=UTF-8\" />";
    echo "<meta http-equiv=\"Pragma\" content=\"no-cache\" />";
    echo "<meta http-equiv=\"Cache-Control\" content=\"max-age=0\" />";
    echo "<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />";
    echo "<meta http-equiv=\"Cache-Control\" content=\"no-store\" />";
    echo "<meta http-equiv=\"Expires\" content=\"-1\"/>";
    echo "<link rel=\"stylesheet\" href=\"css/$this->csslink\" type=\"text/css\" />";
    echo "</head>";    
  }
  function view1()
  {
    include("preview1.html");
    echo "<div class=\"footer\">";
    echo "<a class=\"button\">Home View</a> | ";
    echo "<a href=\"preview.php?id=$this->cssid&view=2&sid=$this->session\" class=\"button\">Forum View</a>";
    if(parent::checkLogin())
    {
      echo "<br /><a href=\"like.php?id=$this->cssid&sid=$this->session\" class=\"button\">Like It!</a>";
      if(GeneralProc::isAdmin($this->un))
      {
        echo "<br /><a href=\"admin.php?id=$this->cssid&sid=$this->session&task=deletecss\" class=\"button\">Delete Css</a>";
        if(!GeneralProc::isValidated($this->cssid))
        echo "<br /><a href=\"admin.php?id=$this->cssid&sid=$this->session&task=showPendingValidation\" class=\"button\">Validate Css</a>";
      }
    }
    echo "<hr /><a href=\"index.php?sid=$this->session\" class=\"button\">Home</a>";
  }
    function view2()
  {
    include("preview2.html");
    echo "<div class=\"footer\">";
    echo "<a href=\"preview.php?id=$this->cssid&sid=$this->session\" class=\"button\">Home View</a> | ";
    echo "<a class=\"button\">Forum View</a>";
    if(parent::checkLogin())
    {
      echo "<br /><a href=\"like.php?id=$this->cssid&sid=$this->session\" class=\"button\">Like It!</a>";
      if(GeneralProc::isAdmin($this->un))
      {
        echo "<br /><a href=\"admin.php?id=$this->cssid&sid=$this->session&task=deletecss\" class=\"button\">Delete Css</a>";
        if(!GeneralProc::isValidated($this->cssid))
        echo "<br /><a href=\"admin.php?id=$this->cssid&sid=$this->session&task=showPendingValidation\" class=\"button\">Validate Css</a>";
      }
    }
    echo "<hr /><a href=\"index.php?sid=$this->session\" class=\"button\">Home</a>";
  }
};
class copy
{
  var $id,$link;
  function __construct($id)
  {
    $this->id=$id;
    if($this->fileExists())
    $this->link=mysql_result(mysql_query("SELECT link FROM ksg_cssdetail WHERE id=$this->id"),0);
    else
    return;
  }
  function fileExists()
  {
    $count=mysql_result(mysql_query("SELECT COUNT(*) FROM ksg_cssdetail WHERE id='".$this->id."' AND validated=1 AND deleted=0"),0);
    return $count;
  }
  function showTextArea()
  {
    if($this->fileExists())
    {
      $file=fopen("css/$this->link","r");
      echo "<textarea>";
      while(!feof($file))
      {
        echo fgets($file);
      }
      echo "</textarea>";
      fclose($file);
    }
    else
    {
      echo "<div class=\"warn\">Css does not exists.</div>";
    }    
  }
};
class EditCssDetail extends session 
{
   var $id,$name,$desc;
   function __construct($id)
   {
      parent::__construct($_GET['sid']);
      $this->id=$id;
      $this->name=mysql_result(mysql_query("SELECT name FROM ksg_cssdetail WHERE id=$id"),0);
      $this->desc=mysql_result(mysql_query("SELECT `desc` FROM ksg_cssdetail WHERE id=$id"),0);
   }
   function showForm()
   {
      echo "<form action=\"editdetail.php?sid=$this->session&id=$this->id\" method=\"post\" enctype=\"multipart/form-data\">";
      echo "<b>Css Name:</b><br/>
            <input type=\"text\" maxlength=\"20\" name=\"name\" value=\"$this->name\" />
            <br/>
            <b>Description:</b><br/>
            <input type=\"text\" maxlength=\"100\" name=\"desc\" value=\"$this->desc\" />
            <br/>";
      echo "<input type=\"submit\" value=\"Update\" />";
      echo "</form>";
   }
   function updateDetail()
   {
      $name=$_POST['name'];
      $desc=$_POST['desc'];
      $sql=mysql_query("UPDATE ksg_cssdetail SET name='".$_POST['name']."',`desc`='".$desc."' WHERE id=$this->id ");
      if($sql)
        echo "<div class=\"notify\">Details Updated Successfully.";
      else
      echo "<div class=\"error\">Something wrong</div>".mysql_error();
   }
};
?>