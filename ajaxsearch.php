<html>
<head>
<script type="text/javascript">
function showHint(str,us)
{
if (str.length==0)
  {
  document.getElementById("txtHint").innerHTML="";
  return;
  }
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","ajaxresult.php?q="+str+"&sb="+us,true);
xmlhttp.send();
}
</script>
</head>
<body>

<p><b>Start typing a name in the input field below:</b></p>
<form>
First name: <input type="text" onchange="showHint(this.value,sb.value)" size="20" />
<input type="checkbox" name="sb" value="user">
</form>
<p>Suggestions: <span id="txtHint"></span></p>

</body>
</html>