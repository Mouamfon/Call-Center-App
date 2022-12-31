<?php
// PHP Call Center

//displays header

/*
    Pending 2003-03-01
	- add message when session expired
	- need to rework this. only call ShowHeader from external files. Logout() function.
	  including header.php starts the session only. need index.php to handle login submissions,
	  logout clicks. header.php needs to ShowLogin() and exit if session variables aren't set,
	  otherwise proceed, and the including page should call ShowHeader();

    Notes
	- keep form submission and logout in index.php, along with the ShowMenu function that
	  only needs to be displayed when at index.php. only make these changes if problems
	  persist.

*/

session_start();

//echo 'start';

if(isset($_POST['login']))
{
  include 'connect.php';

  $result=mysql_query(
    "SELECT id,name,access_level 
    FROM techs 
    WHERE pin='".$_POST['login']."'") or die("Died on pin lookup.");

  if(mysql_num_rows($result)==1)
  {
    $row=mysql_fetch_array($result);
    $_SESSION['id']=$row['id'];
    $_SESSION['name']=$row['name'];
    $_SESSION['access_level']=$row['access_level'];

    setcookie("sessionData",session_encode(),time()+60*60*7);
  }
  else
  {
    ShowLogin(1); //sending 1 because it failed
    exit;
  }
}

if(isset($_GET['a']) && $_GET['a']=='logout')
{
  logout();
}


if(!isset($_SESSION['id']))
{
  if(isset($_COOKIE['sessionData']))
  {
    if(isset($_GET['a']) && $_GET['a']=='logout')
      ShowLogin(1);
    else
      session_decode($_COOKIE['sessionData']);
  }
  else
  {
    ShowLogin(1);
    exit;
  }
 
}
else
{
  ShowHeader();
  if(isset($_POST['login']))
    ShowMenu();
}

function logout()
{
  if(isset($_COOKIE['sessionData']))
    setcookie("sessionData",$_COOKIE['sessionData'],time()-60*60);
  $_SESSION=array();
}

function ShowHeader()
{
  //alandrums.catalyticonline.net
?>
<html>
<head>
<title>Call Center App<?php echo $_SESSION['name']; ?></title>
</head>
<body bgcolor=#324C69 text=white link=#c0c0c0 alink=#c0c0c0 vlink=#c0c0c0 topmargin=0>
<center>
<table cellspacing=0 cellpadding=0 style="border: 1px solid #c0c0c0">
 <tr>
  <td>
   <table cellspacing=0 cellpadding=3 border=0>
    <tr align=center valign=top>
     <td width=90><font face=sans-serif size=2><a href=call.php?a=100 target=_top>New Call</a></font></td>
     <td width=90><font face=sans-serif size=2><a href=# target=_top>Search Calls</a></font></td>
     <td width=90><font face=sans-serif size=2><a href=call.php?a=400 target=_top>Call Recap</a></font></td>
     <td width=90><font face=sans-serif size=2><a href=customer.php?a=100 target=_top>Add Customer</a></font></td>
     <td width=90><font face=sans-serif size=2><a href=customer.php?a=200 target=_top>Edit Customer</a></font></td>
     <td width=90><font face=sans-serif size=2><a href=index.php?a=logout target=_top>Logout</a></font></td>
    </tr>
<?php
  if($_SESSION['access_level']==3)
  {
?>
    <tr>
     <td colspan=7>
      <table cellspacing=0 cellpadding=0 width=100%>
       <tr align=center>
        <td><font face=sans-serif size=2><a href=a_techs.php?a=100>Add Tech</a></font></td>
        <td><font face=sans-serif size=2><a href=a_techs.php?a=110>Edit Tech</a></font></td>
        <td><font face=sans-serif size=2><a href=a_calls.php>Manage Call DB</a></font></td>
        <td><font face=sans-serif size=2><a href=a_reports.php>Print Reports</a></font></td>
       </tr>
      </table>
     </td>
    </tr>
<?php
  }
?>
<tr>
  <td colspan=6 align=center><font face=monospace size=2 color=#c0c0c0><a href=http://sourceforge.net/projects/phpcc target=_blank>phpCC</a> created by Abdel Karim MOUAMFON</font></td></tr>
</table>
  </td>
 </tr>
</table><br><font face=sans-serif size=2>

<?php
}

function ShowLogin($val)
{
?>
<html>
  <head>
  <title>Call Center App</title>
  <script language=JavaScript>
  function verify()
  {
    if(tech.login.value.length!=6 && tech.login.value.length!=10)
    {
      tech.login.focus();
      tech.login.select();
      alert("Enter a valid pin.");
      return false;
    }
    return true;
  }
  </script>
  </head>
  <body bgcolor=#324C69 text=white link=#c0c0c0 alink=#c0c0c0 vlink=#c0c0c0>
  <font face=sans-serif size=2><center>

<?php
 if(!$val)
 echo "Login failed<br>";
?>
 
  <form method=post action=index.php name=frm onSubmit="return verify()"> 
  <table cellspacing=0 cellpadding=0 border=1 bordercolor=white bgcolor=#234D76 width=200> 
   <tr> 
    <td> 
     <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=100%> 
      <tr> 
       <td align=center bgcolor=white><font face=sans-serif size=3 color=#234D76><b>CC LOG IN</b></font></td> 
      </tr><tr> 
       <td align=center><br> 
        <table cellspacing=0 cellpadding=3 border=0> 
         <tr> 
          <td width=80><font face=sans-serif size=2>Tech Pin</font></td>
          <td width=*><input type=password name=login size=10 maxlength=10></td>
         </tr><tr> 
          <td colspan=2 align=center><br>&nbsp;<input type=submit value="log in"><br>&nbsp;</td> 
         </tr> 
        </table> 
       </td> 
      </tr>
     </table> 
    </td> 
   </tr> 
  </table> 
  </form>  
  <br> 
  <font face=sans-serif size=2 color=#234D76>Tech Support Database</font> 
  <script language=JavaScript> 
  document.frm.login.focus(); 
  </script> 
  </body> 
  </html>
<?php
}
?>
