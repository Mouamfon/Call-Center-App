<?php
// PHP Call Center
// started May 2002
// http://www.sourceforge.net/projects/phpcc

/*
    Pending 2003-02-28
	- need to finish integration with status.php when new call added.
	- need to finish DisplayCalls() in calls.inc.php
	- 
*/


/*
100 - ShowSearchForm(0)
101 - ShowSearchForm(1)

400 - recap


110 - PerformSearch
      If type is 0 and found an exact match, do FrameCheck and pass userid
      If type is 1 and found an exact match, bring up calls accordingly
      Else ShowSearchResults

NewCallFrameCheck

120 - AddCalltoDB


grrr. the search for new call and the search for calls are different.
one searched the customers table, the other the calls table
*/



if(isset($_GET['a']))
{
  switch($_GET['a'])
  {
    case 100: //New Call Search Form
      NewCallSearchForm(0);
      break;
    case 101: //Search Search Form
      require_once "header.php";
      NewCallSearchForm(1);
      break;

    case 110:
      NewCallFrameCheck($_GET['userid']);
      break;
    case 111: //New Call Form in frames
      
      NewCallForm($_GET['userid']);
      break;

    case 400: //Call Recap
      require_once "header.php"; 
      include_once 'calls.inc.php';
      DisplayCalls("tech_id=".$_SESSION['id']." AND dt_start>'".mktime(0,0,0,date('n'),date('d'),date('Y'))."'",array('userid'),array('User ID'),0,10,1,1);
      break;
  }

}
elseif(isset($_POST['a']))
{
  $frames=false;
  switch($_POST['a'])
  {
    case 110: //New Call userid Form submitted. Do frames if specified.
      NewCallSearch();
      break;

    case 120: //New Call Form submitted
      include_once 'header.php';
      include_once 'calls.inc.php';
      include_once 'connect.php';

      $problem=0;
      $result=mysql_query("select * from config where type=1 order by value");
      while($row=mysql_fetch_array($result))
      {
        $field="problem_".$row['value'];
        if(isset($_POST[$field]))
          $problem+=$row['value'];
      }     


      AddCalltoDB($_POST['id'],$_POST['dt_start'],$problem,$_POST['verified'],$_POST['status'],$_POST['notes']);
      /* need to still keep prev_calls file for inclusion in searches, etc. eliminate need for display calls though? put those ops in call.php */     
      break;
  }
}
else
{

}

function NewCallSearchForm()
{
  require_once "header.php";
?>
  <script language=JavaScript>
  function verify()
  {
    if(document.frm.userid.value=='')
    {
      document.frm.userid.focus();
      document.frm.userid.select();
      alert('Enter a user name.');
      return false;
    }
    return true;
  } 
  </script>
  <form method=post action=call.php name=frm onSubmit='return verify()'>
  <input type=hidden name=a value=110>
<table cellspacing=0 cellpadding=2 style='border: 1px solid #c0c0c0' width=200 bgcolor=#234D76>
 <tr>
  <td align=center style='border-bottom:1px solid #c0c0c0;'><font face=sans-serif size=3 color=white><b>NEW CALL</b></font></td>
 </tr><tr>
  <td align=center width=100%>
   <table cellspacing=0 cellpadding=3 border=0>
    <tr>
     <td colspan=2 height=4></td>
    </tr><tr>
     <td width=70><font face=sans-serif size=2>User ID</font></td>
     <td width=100><input type=text name=userid size=15 maxlength=8></td>
    </tr><tr>
     <td colspan=2 align=center><input type=submit value=' ok '>&nbsp;<input type=button value='cancel' onClick='history.back()'></td>
    </tr><tr>
     <td colspan=2 height=4></td>
    </tr>
   </table>
  </td>
 </tr>
</table>
</form>
<script language=JavaScript>
document.frm.userid.focus();
</script>
<?php
}

function NewCallSearch()
{
  include_once 'connect.php';
  $result=mysql_query("SELECT userid FROM customers where userid='".$_POST['userid']."'") or die('bla');

  if(mysql_num_rows($result)==1)
  {
      $row=mysql_fetch_array($result);
      NewCallFrameCheck($_POST['userid']);
  }
  else
  {
    NewCallSearchForm();
  }
} 



function NewCallFrameCheck($user)
{
   /* 2003-02-28: need to pull frame settings from DB. */

   if($frames)
   {
     echo "<html>
<frameset cols=40%,60% border=1>
 <frame src=me.php name=left>
 <frame src='call.php?a=111&userid=".$user."' name=right>
</frameset>
</html>";
   }
   else
     NewCallForm($user);
}

function NewCallForm($userid)
{
  include_once 'connect.php';
  include_once 'calls.inc.php';

  $result_problem=mysql_query("select * from config where type=1 order by display_order");
  $result_verified=mysql_query("select * from config where type=2 order by other,display_order");
  $result_status=mysql_query("select * from config where type=3 order by display_order");

  $bCustomerStatus=false;
  if($bCustomerStatus)
  {
  }
  else
  {
    require_once "header.php";
  }
  if(strstr($userid,'%'))
  {
    echo '<br>Invalid character.';
    exit;
  }

  $result=mysql_query("SELECT id,userid,l_name,f_name,phone,system_connection FROM customers WHERE userid='".$userid."'");
      
  if(!$result || mysql_num_rows($result)==0)
  {
    echo "<script language=JavaScript>document.location.href='customer.php?a=100&not_found=".$userid."';</script>";
  }
  else //USER FOUND
  {
    $dt=time();

    $date_time=getdate(); //used to create date_time_display
    $month = str_pad($date_time['mon'],2,"0",STR_PAD_LEFT);
    $day = str_pad($date_time['mday'],2,"0",STR_PAD_LEFT);
    $hours = str_pad($date_time['hours'],2,"0",STR_PAD_LEFT);
    $minutes = str_pad($date_time['minutes'],2,"0",STR_PAD_LEFT);
    $seconds = str_pad($date_time['seconds'],2,"0",STR_PAD_LEFT);
    $date_time_display=$date_time['month'].", ".$date_time['mday']." ".$date_time['year']." ".$date_time['hours'].":".$minutes.":".$seconds;

    $row=mysql_fetch_array($result);
    if(strlen($row['phone'])==10)
    {
      $phone.="(".substr($row['phone'],0,3).") ".substr($row['phone'],3,3)."-".substr($row['phone'],6,4);
    }
    elseif(strlen($row['phone'])==7)
    {
      $phone.=substr($row['phone'],0,3)."-".substr($row['phone'],3,4);
    }
    echo "<script language=JavaScript>
    var datetime='$dt';
    var notes_clicked=false;
    var element;
    var verified=false;

    function verify()
    {
      var bStatus=false;

      element=eval('document.frm.ver1');
      for(i=0;i<element.length;i++)
      {
        if(element.options[i].selected==true)
        {
          verified=true;
        }
      }
      element=eval('document.frm.ver2');
      for(i=0;i<element.length;i++)
      {
        if(element.options[i].selected==true)
        {
          verified=true;
        }
      }
      
      if(!verified)
      {
        alert('Must verify something.');
        return false;
      }
      for(i=0;i<document.frm.status.length;i++)
      {
        if(document.frm.status[i].checked==true)
        {
          bStatus=true;
          break;
        }
      }
      if(!bStatus)
      {
        alert('Must select a call status.');
        return false;
      }
      if(!notes_clicked)
      {
        alert('Must enter notes.');
        document.frm.notes.value='';
        document.frm.notes.focus();
        notes_clicked=true;
        return false;
      }  
      if(document.frm.notes.value=='')
      {
        alert('Must enter notes.');
        document.frm.notes.focus();
        return false;
      }
      return true;
    }

    
    function notes_click()
    {
      if(!notes_clicked)
      {
        document.frm.notes.value='';
        notes_clicked=true;
      }
    }

    function sel()
    {
      var selected=0;
      element=eval('document.frm.ver1');
      for(i=0;i<element.length;i++)
      {
        if(element.options[i].selected==true)
        {
          selected+=1*element.options[i].value;
        }
      }

      if(document.frm.ver2!=null)
      {
        element=eval('document.frm.ver2');
        for(i=0;i<element.length;i++)
        {
          if(element.options[i].selected==true)
          {
            selected+=1*element.options[i].value;
          }
        }
      }
      document.frm.verified.value=selected;
    }
    </script>";
echo '<!-- StratoNet Tech Support
<br>This is ',$tn,'
<br>Can I get your StratoNet username or email address?
<br>Can I get your last name and first name?
<br>Can I get your telephone number?
<br>Thank you.
<br>How can I assist you? -->

    <form method=post action=call.php name=frm onSubmit="return verify()">
    <input type=hidden name=a value=120>
    <input type=hidden name=id value=',$row['id'],'>
    <input type=hidden name=dt_start value=',$dt,'>
    <input type=hidden name=verified>
    <table cellspacing=0 cellpadding=0 width=500 bgcolor=#234D76 style="border: 1px solid #c0c0c0">
    <tr>
     <td>
     <table cellpadding=0 cellspacing=3 border=0 width=100%>
     <tr>
     <td colspan=3 align=center width=100%><font face=sans-serif size=3>Support Call</font></td>
      </tr><tr valign=top>
       <td width=40%><font face=sans-serif size=2><b>User ID:</b> ',$userid,'</font></td>
       <td width=40%><font face=sans-serif size=2><b>Phone:</b> ',$phone,'</font></td>
       <td width=20% rowspan=3><font face=sans-serif size=2><a href=customer.php?a=220&userid='.$userid.' target=_top>Edit</a></font></td>
      </tr><tr>
       <td><font face=sans-serif size=2><b>Name:</b> ',$row['f_name'],' ',$row['l_name'].'</font></td>
       <td><font face=sans-serif size=2><b>Connection:</b> ';
        if($row['system_connection']==1) echo 'Dial-up';
        elseif($row['system_connection']==2) echo 'ISDN';
        elseif($row['system_connection']==3) echo 'DSL';
        echo '</font></td>
      </tr>
     </table>
    </td>
   </tr><tr>
    <td align=center><img src=line.gif height=1 width=450></td>
   </tr><tr>
    <td width=100%>
     <table cellspacing=0 cellpadding=3 border=0 width=100%>
      <tr>
       <td width=50><font face=sans-serif size=2><b>Date</b></td>
       <td colspan=2><input type=text size=25 value="',$date_time_display,'" readonly></td>
      </tr><tr valign=top align=left>
       <td width=50><font face=sans-serif size=2><b>Problem</b></font></td> 
       <td><font face=sans-serif size=2>';

       $i=1;
       while($row_problem=mysql_fetch_array($result_problem))
       {
         echo '<input type=checkbox name=problem_',$row_problem['value'],'> ',$row_problem['name'];
         if($i==intval((mysql_num_rows($result_status)-1)/2))
             echo '</font></td><td><font face=sans-serif size=2>';
         else
           echo '<br>';
         $i++;         
       }
       echo "</font></td>";


      echo '</tr><tr valign=top>
       <td><font face=sans-serif size=2><b>Verified</b></font></td> 
       <td><font face=sans-serif size=2><select name=ver1 size=6 multiple onChange=sel()>';


       /* need to finish making the lists for the connection types. always populate the first list for
       general verifications. only populate the second by connection type */

       $second_added=false;
       $prev=0;
       while($row_verified=mysql_fetch_array($result_verified))
       {
         if($prev!=$row_verified['other'] && $row['conn_type']==$row_verified['other'])
           echo '</select></td><td><select name=ver2 size=6 multiple onChange=sel()>';
         switch($row_verified['other'])
         {
           case 0: //for all connection types
             echo '<option value=',$row_verified['value'],'>',$row_verified['name'],'</option>';
             break;
           case 1: //just for dialup
             break;
           case 2: //isdn
             break;
           case 3: //just for DSL
             if($row['system_connection']==3)
             {
               if(!$second_added)
               {
                 echo '</select></td><td><select name=ver2 size=6 multiple onChange=sel()>';
                 $second_added=true;
               }
               echo '<option value=',$row_verified['value'],'>',$row_verified['name'],'</option>';
             }
             break;
               
         }         
         $prev=$row_verified['other'];

       }
       if(!$second_added)
         echo '</td><td>';

echo '</td>
      </tr><tr valign=top>
       <td><font face=sans-serif size=2><b>Status</b></font></td>
       <td><font face=sans-serif size=2>';

       $i=0;
       while($row_status=mysql_fetch_array($result_status))
       {
         if($row_status['other']==1)
         {
           echo '<input type=radio name=status value=',$row_status['value'],'>',$row_status['name'];
           if($i==intval((mysql_num_rows($result_status)-1)/2))
             echo '</font></td><td><font face=sans-serif size=2>';
           else
             echo '<br>';
           $i++;
         }        
       }
echo '</td>
      </tr><tr>
       <td><font face=sans-serif size=2><b>Notes</b></font></td>
       <td colspan=2><textarea name=notes cols=60 rows=4 maxlength=300 style=\'font-size:12;font-family:sans-serif;\' onClick=\'notes_click()\'>[Please give a short description of what the customer\'s problem was and what you did to resolve the issue.]</textarea></td> 
      </tr><tr>
       <td colspan=3 align=center><input type=submit value=add>&nbsp;<input type=button value=cancel onClick="history.back()"></td> 
      </tr>
     </table> 
    </td>
   </tr>
  </table>
  </form>
  <br>';
  
  DisplayCalls("user_id='".$row['id']."'",array("name"),array("Tech Name"),0,10,"",0);
  }
}

?>
