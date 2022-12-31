<?php
//PHP Call Center
//Started May 2002
//http://www.sourceforge.net/projects/phpcc

//search for calls

/*
    Pending 2003-02-28
	- still need to finish the way the results are displayed, but that's in functions_call.php
	- the main thing on search needs to be the ability to display calls for multiple customers
	  according to search criteria and not just for one customer matching the criteria. saying
	  this, it seems unnecessary to pull up a list of all the customers that match whatever
	  is entered into the customer related fields: the results can simply be displayed. i don't
	  exactly know how i'm going to do this with a join being involved. perhaps i'll perform a
	  customer search query first, make a list of user_id ORs to be added to the call query.
	  the call query will include the join statement, as well as the WHERE conditions pertaining
	  to the user_id, as well as whatever other conditions for the other fields specified. fun sql.
	- features for Supervisor+: allow search to be by any/all date items (year only, year and
	  month, etc); search by problem?, verified?, status; make sure phone can accept any number
	  of numbers (for area code searches); 

*/
 
require "header.php";
include 'misc.inc.php';

if(isset($_GET['a']))
{
  switch($_GET['a'])
  {
    case 100:
      ShowSearchForm();
      break;
   }
}
elseif(isset($_POST['a']))
{
  switch($_POST['a'])
  {
    case 100:
      ShowSearchResults();
      break;
  }
}

 
//alandrums.catalyticonline.net 
//alandrums@yahoo.com 

function ShowSearchForm()
{
?>
  <script language=JavaScript> 
  function verify() 
  { 
    if(document.frm.userid.value.length==0&&document.frm.l_name.value.length==0&&document.frm.phone.value.length==0) 
    { 
      alert("Must enter search data"); 
      return false; 
    } 
    return true; 
  }
  function date_adj(type,dir)
  {
    if(type=='day')
    {
      if(dir==1)
        document.frm.day.value++;
      else
        document.frm.day.value--;
    }
    if(type=="year")
    {
      if(dir==1)
        document.frm.year.value++;
      else
        document.frm.year.value--;
    }
  }
  </script> 
  <form method=post action=search.php name=frm> 
  <input type=hidden name=a value=100> 
  <table cellspacing=0 cellpadding=2 bgcolor=#234D76 width=250 style="border:1px solid #c0c0c0;"> 
   <tr> 
    <td align=center style="border-bottom:1px solid #c0c0c0;"><font face=sans-serif size=3 color=white><b>CALL SEARCH</b></font></td> 
   </tr><tr> 
    <td align=center> 
     <table cellspacing=0 cellpadding=2 width=90%> 
      <tr>
       <td colspan=2 height=4></td>
      </tr><tr>
       <td width=70><font face=sans-serif size=2>User ID</font></td> 
       <td><input type=text name=userid size=15 maxlength=8></td> 
      </tr><tr> 
       <td><font face=sans-serif size=2>Last Name</font></td> 
       <td><input type=text name=l_name size=15 maxlength=20></td> 
      </tr><tr> 
       <td><font face=sans-serif size=2>Phone</font></td> 
       <td><input type=text name=phone size=15 maxlength=15></td> 
      </tr>
<?php 
  if($_SESSION['access_level']==2 || $_SESSION['access_level']==3) 
  { 
    $date=getdate(); 
?>
      <tr>
       <td><font face=sans-serif size=2>Tech name</font></td>
       <td><input type=text name=tech_name size=15 maxlength=20></td>
      </tr><tr>
       <td><font face=sans-serif size=2>Month</font></td>
       <td><select name=month>
<?php
    for($i=1;$i<=12;$i++) 
    { 
    if($i==$date['mon']) 
      echo '<option value=',$i,' SELECTED>',$row; 
    else 
      echo '<option value=',$i,'>'; 
    echo get_month_abbrev($i); 
    echo '</option>';     
    }
?>
       </select></td>
      </tr><tr>
       <td><font face=sans-serif size=2>Day</font></td>
       <td><input type=text name=day size=7 maxlength=2 value="<?php echo str_pad($date['mday'],2,"0",STR_PAD_LEFT);?>"><font face=times size=3>&nbsp;<b><a href=# onClick="date_adj('day',-1);" style="text-decoration:none;">[dn]</a></b>&nbsp;/&nbsp;<b><a href=# onClick="date_adj('day',1)" style="text-decoration:none;">[up]</a></b></font></td>
      </tr><tr>
       <td><font face=sans-serif size=2>Year</font></td>
       <td><input type=text name=year size=7 maxlength=4 value="<?php echo $date['year'];?>"><font face=times size=3>&nbsp;<b><a href=# onClick="date_adj('year',-1);" style="text-decoration:none;">[dn]</a></b>&nbsp;/&nbsp;<b><a href=# onClick="date_adj('year',1)" style="text-decoration:none;">[up]</a></b></font></td>
      </tr> 
<?php
  }
?>

      <tr valign=top> 
       <td align=center colspan=2> 
        <table cellspacing=0 cellpadding=0 border=0 width=100%> 
         <tr valign=bottom> 
          <td align=right width=70><input type=radio name=type value=1 CHECKED></td> 
          <td width=*><font face=sans-serif size=2>Exact</font></td> 
         </tr><tr valign=bottom> 
          <td align=right><input type=radio name=type value=2></td> 
          <td><font face=sans-serif size=2>Starting with</font></td> 
         </tr><tr valign=bottom> 
          <td align=right><input type=radio name=type value=3></td> 
          <td><font face=sans-serif size=2>Containing</font></td> 
         </tr> 
        </table> 
       </td> 
      </tr><tr> 
       <td colspan=2 align=center><input type=submit value=" ok ">&nbsp;<input type=button value="cancel" onClick="history.back();"></td> 
      </tr><tr>
       <td colspan=2 height=4></td>
      </tr>
     </table>
    </td> 
   </tr>
  </table> 
  </form> 
  <script language=JavaScript>document.frm.userid.focus();document.frm.userid.select();</script>
<?php 
}

function ShowSearchResults()
{
  require "connect.php";
  include 'pin_lookup.php';

  $fields=0;
  if(strlen($_POST['userid'])>0)
    $fields++;
  if(strlen($_POST['l_name'])>0)
    $fields++;
  if(strlen($_POST['phone'])>0)
    $fields++;
  if(is_supervisor() && strlen($_POST['tech_name'])>0)
    $fields++;

  if($fields==0)
  {
    echo "<font face=sans-serif size=3>No fields entered.<br><br></font>";
    ShowSearchForm();
  }
  else
  {
    $query=""; //this string will contain the WHERE section of the search query
    $bPrev=false; //keeps track of whether or not another field has already been appended to the query string
    $s_tn="";

    switch($_POST['type'])
    {  
      case 1: //'EXACT' SEARCH
        if(strlen($_POST['userid'])>0)
	{
	  $bPrev=true;
          $query=$query." userid LIKE '".$_POST['userid']."'";
	}
        if(strlen($_POST['l_name'])>0)
	{
	  if($bPrev)
	    $query=$query." AND";
          $query=$query." l_name='".$_POST['l_name']."'";
	  $bPrev=true;
	}
        if(strlen($_POST['phone'])>0)
	{
	  if($bPrev)
	    $query=$query." AND";
          $query=$query." phone='".$_POST['phone']."'";
	  $bPrev=true;
	}
        if(is_supervisor() && strlen($_POST['tech_name'])>0)
        {
	  $res_tech=mysql_query("SELECT id,name FROM techs WHERE name='".$_POST['tech_name']."';");
	  if(!$res_tech || (mysql_num_rows($res_tech)==0))
	  {
	    echo "<font face=sans-serif size=3>Tech not found.<br><br></font>";
            ShowSearchForm();
	  }
	  else
	  {
	    $row_tech=mysql_fetch_array($res_tech);
	    if($bPrev)
	      $query=$query." AND";
	    $s_tn=$row_tech['name'];
            //$date=mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

            $date=$_POST['year'].str_pad($_POST['month'],2,"0",STR_PAD_LEFT).str_pad($_POST['day'],2,"0",STR_PAD_LEFT);
            $query=$query." tech_id='".$row_tech['id']."' AND dt_start LIKE '".$date."%'";
            echo $query;
            mysql_free_result($res_tech);
	  }
        }
        break; 
      case 2: //'STARTING WITH' SEARCH
        if(strlen($_POST['userid'])>0)
	{
	  $bPrev=true;
          $query=$query." userid LIKE '".$_POST['userid']."%'";
	}
        if(strlen($_POST['l_name'])>0)
	{
	  if($bPrev)
	    $query=$query." AND";
          $query=$query." l_name LIKE '".$_POST['l_name']."%'";
	  $bPrev=true;
	}
        if(strlen($_POST['phone'])>0)
	{
	  if($bPrev)
	    $query=$query." AND";
          $query=$query." phone LIKE '".$_POST['phone']."%'";
	  $bPrev=true;
	}
        if(is_supervisor() && strlen($_POST['tech_name'])>0)
        {
	  $res_tech=mysql_query("SELECT id,name FROM techs WHERE name LIKE '".$_POST['tech_name']."%';");
	  if(!$res_tech || (mysql_num_rows($res_tech)==0))
	  {
	    echo "<font face=sans-serif size=3>Tech not found.<br><br></font>";
            ShowSearchForm();
	  }
	  else
	  {
	    $row_tech=mysql_fetch_array($res_tech);
	    if($bPrev)
	      $query=$query." AND";
	    $s_tn=$row_tech['name'];
            //$date=mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

            $date=$_POST['year'].str_pad($_POST['month'],2,"0",STR_PAD_LEFT).str_pad($_POST['day'],2,"0",STR_PAD_LEFT);
            $query=$query." tech_id='".$row_tech['id']."' AND dt_start LIKE '".$date."%'";
            echo $query;
            mysql_free_result($res_tech);
	  }
        }
        break; 
      case 3: //'CONTAINING' SEARCH
        if(strlen($_POST['userid'])>0)
	{
	  $bPrev=true;
          $query=$query." userid LIKE '%".$_POST['userid']."%'";
	}
        if(strlen($_POST['l_name'])>0)
	{
	  if($bPrev)
	    $query=$query." AND";
          $query=$query." l_name LIKE '%".$_POST['l_name']."%'";
	  $bPrev=true;
	}
        if(strlen($_POST['phone'])>0)
	{
	  if($bPrev)
	    $query=$query." AND";
          $query=$query." phone LIKE '%".$_POST['phone']."%'";
	  $bPrev=true;
	}
        if(is_supervisor() && strlen($_POST['tech_name'])>0)
        {
	  $res_tech=mysql_query("SELECT id,name FROM techs WHERE name LIKE '%".$_POST['tech_name']."%';");
	  if(!$res_tech || (mysql_num_rows($res_tech)==0))
	  {
	    echo "<font face=sans-serif size=3>Tech not found.<br><br></font>";
            ShowSearchForm();
	  }
	  else
	  {
	    $row_tech=mysql_fetch_array($res_tech);
	    if($bPrev)
	      $query=$query." AND";
	    $s_tn=$row_tech['name'];
            //$date=mktime(0,0,0,$_POST['month'],$_POST['day'],$_POST['year']);

            $date=$_POST['year'].str_pad($_POST['month'],2,"0",STR_PAD_LEFT).str_pad($_POST['day'],2,"0",STR_PAD_LEFT);
            $query=$query." tech_id='".$row_tech['id']."' AND dt_start LIKE '".$date."%'";
            echo $query;
            mysql_free_result($res_tech);
	  }
        }
        break;
    }
    
    //if(strlen($query)>0)
      //$query=$query.";";

/*
    this is where the call to DisplayCalls needs to be with the appropriate query. get rid
    of display_output and display_output_tech
*/

    if(!$show_search)
    {
      if(strlen($_POST['tech_name'])>0)
      {
        $result=mysql_query("SELECT * FROM calls LEFT JOIN customers ON calls.user_id=customers.id WHERE".$query);
        display_output_tech($result,$s_tn);
      }
      else
      {
        $result=mysql_query("SELECT * FROM customers WHERE".$query." ORDER BY userid ASC");
        display_output($result);
      }
    }
  }
}
 
function display_output($result) 
{
  if(!$result || mysql_num_rows($result)==0)
  {
    echo "<br><font size=3>No records found.</font>";
  }
  else
  {
    echo "<table cellspacing=0 cellpadding=3 border=1 bordercolor=white bgcolor=#234D76><tr><td colspan=3 align=center><font face=sans-serif size=2>Search returned the following results.<br>Click on a userid to DO SOMETHING. FIX THIS. lower font size on results. reformat. possibly have quick access to prev, edit, new call, etc.</font></td></tr><tr><td width=100><font face=sans-serif size=3><b>User ID</b></font></td><td width=130><font face=sans-serif size=3><b>Customer Name</b></font></td><td width=130><font face=sans-serif size=3><b>Phone Number</b></font></td></tr>";
    while($row=mysql_fetch_array($result)) 
    { 
      if(strlen($row['phone'])==10) 
      { 
        $phone="(".substr($row['phone'],0,3).") ".substr($row['phone'],3,3)."-".substr($row['phone'],6,4); 
      } 
      else if(strlen($row['phone'])==7) 
      { 
        $phone=substr($row['phone'],0,3)."-".substr($row['phone'],3,4); 
      } 
      echo "<tr><td><font face=sans-serif size=3><a href=new_call.php?p=50&userid=".$row['userid'].">".$row['userid']."</a></font></td><td><font face=sans-serif size=3>".$row['l_name'].", ".$row['f_name']."</font></td><td><font face=sans-serif size=3>".$phone."</td></tr>"; 
    } 
    echo "</table>"; 
    mysql_free_result($result);
  } 
 
//alandrums.catalyticonline.net 
//alandrums@yahoo.com 
 
}
 
function display_output_tech($result,$s_tn)
{   
  include "prev_calls.php"; 
  if(!$result || mysql_num_rows($result)==0)
  {
    echo "<br><font size=3>No records found.</font>";   
  }
  else
  {
    disp_search_by_tech_header($s_tn);
    while($row=mysql_fetch_array($result)) 
    {     
      echo '<tr align=left valign=top><td><font face=sans-serif size=2>'.$row['userid'].'</font></td><td><font face=sans-serif size=2>'.FormatDate($row['dt_start']).'</font></td><td><font face=sans-serif size=2>'.parse_problem($row['problem']).'</td><td><font face=sans-serif size=2>'.parse_verified($row['verified']).'&nbsp;</font></td><td bgcolor=#234D76 width=150><font face=sans-serif size=2 color=white>&nbsp;&nbsp;<b>'.$row['notes'].'</b></font></td><td width=175><font face=sans-serif size=2>'.parse_status($row['status']).'&nbsp;</font></td></tr>'; 
    } 
    echo "</table><br><br>"; 
    mysql_free_result($result); 
  }   
} 
 
function disp_search_by_tech_header($s_tn)
{
  global $date;
  echo "<br><table cellspacing=0 cellpadding=0 bgcolor=#234D76 border=1 bordercolor=#c0c0c0> 
<tr valign=top align=center>
  <td colspan=6><font face=sans-serif size=2>Calls answered by ".$s_tn." on ".substr($date,0,2),"-".substr($date,2,2)."-".substr($date,4,4)."</td>
 </tr><tr>
<td width=80><font face=sans-serif size=2><b>User ID</b></font></td> 
<td width=150><font face=sans-serif size=2><b>Date/Time</b></font></td> 
<td width=80><font face=sans-serif size=2><b>Problem</b></font></td> 
<td width=120><font face=sans-serif size=2><b>Verified</b></font></td> 
<td width=200><font face=sans-serif size=2><b>Notes</b></font></td> 
<td width=50><font face=sans-serif size=2><b>Status</b></font></td></tr>"; 
}

?>
