<?php
// PHP Call Center
// started May 2002
// http://www.sourceforge.net/projects/phpcc

/*
    Pending 2003-04-12
	- no begin form tag on customer add

    Pending 2003-02-28
	- customer technical setting options (system speeds, ram sizes, etc.) are not being pulled from the DB.
	  they're still being pulled from config.php
	- frank gave the idea to make the edit page as a layer style. that way all the customer will be read
	  upon visiting the edit page, the General div will be visible, whatever changes are made will remain
	  when switching between tabs, and the submit will submit all changes at once. much easier. just the
	  center form data will be within the divs. the styles on the tabs will change on click, moving the
	  borders.

*/


require "header.php";

if(isset($_GET['a']))
{
  switch($_GET['a'])
  {
    case 100: //Add Customer form
      AddForm('');
      break;

    case 200: //Edit Customer Search
      EditFind();
      break;
    case 220: //Edit Customer Form
      EditForm($_GET['userid'],$_GET['type']);
      break;
    case 240: //Edit delete customer
      EditDelete();
      break;

    case 400:
     include 'status.php';
     DisplayStatus($_GET['userid'],0,1);
     break;
 }
}
elseif(isset($_POST['a']))
{
  switch($_POST['a'])
  {
    case 110: //Add Customer to DB
    case 111:
      AddToDB($_POST['a']);
      break;

    case 210: //Find Customer in DB
      EditFindResults();
      break;
    case 230: //Edit. Make changes
      EditUpdateDB();

  }
}
else
{

}

function AddForm($user)
{
  include_once 'misc.inc.php';
  echo "<script language=JavaScript>
   function verify()
   {
    if(document.frm.userid.value.length<2 || document.frm.userid.value.length>8)
    {
     alert('Invalid userid length (2-8 chars).');
     document.frm.userid.focus();
     document.frm.userid.select();
     return false;
    }
    if(document.frm.l_name.value.length==0)
    {
     alert('Must enter a last name.');
     document.frm.l_name.focus();
     document.frm.l_name.select();
     return false;
    }
    if(document.frm.f_name.value.length==0)
    {
     alert('Must enter a first name.');
     document.frm.f_name.focus();
     document.frm.f_name.select();
     return false;
    }
    phone= new String;
    phone=document.frm.phone.value;
    var phonefinal='';
    for(var i=0;i<phone.length;i++)
    {
     if(phone.charAt(i)=='0'||phone.charAt(i)=='1'||phone.charAt(i)=='2'||phone.charAt(i)=='3'||phone.charAt(i)=='4'||phone.charAt(i)=='5'||phone.charAt(i)=='6'||phone.charAt(i)=='7'||phone.charAt(i)=='8'||phone.charAt(i)=='9')
     {
      phonefinal=phonefinal+phone.charAt(i);
     }    
    }
    document.frm.phone.value=phonefinal;
    if(document.frm.phone.value.length<10)
    {
     alert('Invalid Phone Number. 10 digits required.');
     document.frm.phone.focus();
     return false;
    }

    return true;
   }
   </script>";
  if(isset($_GET['not_found']))
  {
   echo "<font size=3>User not found.</font><br><br>";
   $a=111;
  }
  else
   $a=110;

  SimpleForm("ADD CUSTOMER",250);
/*
  echo '<form method=post action=customer.php name=frm onSubmit=\'return verify()\'>
<input type=hidden name=a value=',$a,'>
<table cellspacing=0 cellpadding=0 style=\'border:1px solid #c0c0c0\' bgcolor=#234D76 width=250>
 <tr>
  <td align=center style="border-bottom:1px solid #c0c0c0;"><font face=sans-serif size=3><b>ADD CUSTOMER</b></font></td>
 </tr><tr>
  <td align=center>*/
?>
  <form method=post action=customer.php name=frm onSubmit='return verify()'>
  <input type=hidden name=a value='<?php echo $a;?>'>
  <table cellspacing=0 cellpadding=3 border=0 width=90%>
   <tr>
    <td width=70><font face=sans-serif size=2>User ID</td>
    <td><input type=text name=userid size=15 maxlength=8 value='<?php echo $user;?>'></td>
   </tr><tr>
    <td><font face=sans-serif size=2>Last name</td>
    <td><input type=text name=l_name size=15 maxlength=20></td>
   </tr><tr>
    <td><font face=sans-serif size=2>First name</td>
    <td><input type=text name=f_name size=15 maxlength=20></td>
   </tr><tr>
    <td><font face=sans-serif size=2>Phone</td>
    <td><input type=text name=phone size=15 maxlength=15></td>
   </tr><tr>
    <td><font face=sans-serif size=2>City</font></td>
    <td><input type=text name=city size=15 maxlength=15></td>
    </td>
   </tr><tr>
    <td><font face=sans-serif size=2>State</font></td>
    <td><select name=state></select></td>
   </tr><tr>
    <td><font face=sans-serif size=2>Connection</td>
    <td><select name=conn_type><option value=0>Dial-up</option><option value=1>ISDN</option><option value=2>DSL</option></select></td>
   </tr><tr>
    <td colspan=2 align=center><input type=submit value=' ok '>&nbsp;<input type=button value='cancel' onClick='history.back()'></td>
   </tr>
  </table>
  </form>
<?php
  SimpleFormClose();
?>
<script language=JavaScript>
if(document.frm.userid.value!="")
  document.frm.l_name.focus();
else
  document.frm.userid.focus();
</script>
<?php
//echo "<a href=customer.php?a=400&userid=john>go</a>";
}

function AddToDB($action)
{
  $userid=ret_alpha_numer($_POST['userid']);
  $userid=strtolower($userid);
  $l_name=strtoupper(substr($_POST['l_name'],0,1)).substr($_POST['l_name'],1,strlen($_POST['l_name']));
  $f_name=strtoupper(substr($_POST['f_name'],0,1)).substr($_POST['f_name'],1,strlen($_POST['f_name']));
  //$userid=trim($userid);
  //$userid=trim($userid,'%');
  if(strlen($userid)<2||strlen($userid)>8)
  {
    echo "<font size=3><br>Invalid username.</font><br>";
    exit;
  }
      
  if(strlen($_POST['phone'])<10)
  {
    echo "<font size=3><br>Invalid phone.</font><br>";
    exit;
  }
  include_once 'connect.php';      
  $result=mysql_query("SELECT * FROM customers WHERE userid='".$userid."';");
  if((!$result) || (mysql_num_rows($result)==0))
  {
    $result = mysql_query("INSERT INTO customers (userid,l_name,f_name,phone,system_connection) VALUES('".$userid."','".$l_name."','".$f_name."','".$_POST['phone']."','".$_POST['conn_type']."');");
    if(!$result)
    {
      echo "<font size=3><br>Adding failed.</font>";
      echo mysql_error();
      exit;
    }
    else
    {
      if($action=='111')
        echo "<script language=JavaScript>parent.document.location.href='new_call.php?p=50&userid=$userid';</script>";
      else
      {
        /*
         * here is where we will want to include status.php and display the status of our last attempted
         * operation. with one click we can then go to the NewCallForm
         */
        include 'status.php';
        DisplayStatus($userid,'cust_add',1);
      }
    }
  }
  else
  {
    require "header.php";
    echo "<br><font size=3>Account already exists.</font>";
  }        
}


function EditFind()
{
  echo '<form method=post action=customer.php name=tech onSubmit="return verify();">
  <input type=hidden name=a value=210> 
  <table cellspacing=0 cellpadding=0 style="border:1px solid #c0c0c0;" width=240 bgcolor=#234D76> 
   <tr> 
    <td align=center style="border-bottom:1px solid #c0c0c0;"><font face=sans-serif size=3 color=white><b>FIND CUSTOMER</b></font></td> 
   </tr><tr> 
    <td align=center> 
     <table cellspacing=0 cellpadding=3 border=0 width=90%> 
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
      </tr><tr valign=top>
       <td align=center colspan=2><table cellspacing=0 cellpadding=0 border=0 width=100%><tr valign=bottom> 
       <td align=right width=70><input type=radio name=t value=1 CHECKED></td> 
       <td width=*><font face=sans-serif size=2>Exact</font></td> 
      </tr><tr valign=bottom> 
       <td align=right><input type=radio name=t value=2></td> 
       <td><font face=sans-serif size=2>Starting with</font></td> 
      </tr><tr valign=bottom> 
       <td align=right><input type=radio name=t value=3></td> 
       <td><font face=sans-serif size=2>Containing</font></td></tr></table></td> 
      </tr><tr> 
       <td colspan=2 align=center><input type=submit value=" ok ">&nbsp;<input type=button value="cancel" onClick="history.back()"></td> 
      </tr><tr>
       <td colspan=2 height=4></td>
      </tr>
     </table> 
    </td> 
   </tr><tr> 
    <td align=center bgcolor=white><font face=sans-serif size=2 color=#234D76>Find a customer to edit.</font></td> 
   </tr> 
  </table> 
  </form>
  <script language=JavaScript>
  document.tech.userid.focus();
  function verify()
  {
    if(document.tech.userid.value.length==0 && document.tech.l_name.value.length==0 && document.tech.phone.value.length==0)
    {
     alert("Must enter search information.");
      return false;
    }
    return true; 
  }
  </script>';
}
function EditFindResults()
{
  include_once 'connect.php';
  switch($_POST['t']) 
  { 
    case 1: //exact 
      if(strlen($_POST['userid'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE userid='".$_POST['userid']."' ORDER BY userid ASC;"); 
      else{if(strlen($_POST['l_name'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE l_name='".$_POST['l_name']."' ORDER BY l_name ASC;"); 
      else{if(strlen($_POST['phone'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE phone='".$_POST['phone']."' ORDER BY phone ASC;"); 
      }} 
      break; 
    case 2: //starting with 
      if(strlen($_POST['userid'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE userid LIKE '".$_POST['userid']."%' order by userid asc;"); 
      else{if(strlen($_POST['l_name'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE l_name LIKE '".$_POST['l_name']."%' order by l_name asc;"); 
      else{if(strlen($_POST['phone'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE phone LIKE '".$_POST['phone']."%' order by phone asc;"); 
      }} 
      break; 
    case 3: //containing 
      if(strlen($_POST['userid'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE userid LIKE '%".$_POST['userid']."%' order by userid asc;"); 
      else{if(strlen($_POST['l_name'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE l_name LIKE '%".$_POST['l_name']."%' order by l_name asc;"); 
      else{if(strlen($_POST['phone'])>0) 
        $result=mysql_query("SELECT * FROM customers WHERE phone LIKE '%".$_POST['phone']."%' order by phone asc;"); 
      }} 
      break; 
  }
  if(mysql_num_rows($result)==1) 
  {
    $row=mysql_fetch_array($result);
    EditForm($row['userid'],0);
  }
  elseif(mysql_num_rows($result)>1)
  {
    echo "<table cellspacing=0 cellpadding=3 border=1 bordercolor=white bgcolor=#234D76>
    <tr><td colspan=4 align=center><font face=sans-serif size=3><b>Find Results</b></font></td>
    </tr><tr>
    <td width=100><font face=sans-serif size=2><b>User ID</b></font></td><td width=120><font face=sans-serif size=2><b>Name</b></font></td><td width=100><font face=sans-serif size=2><b>Phone</b></font></td><td width=50><font face=sans-serif size=3><b>Action</b></font></td></tr>"; 
    while($row=mysql_fetch_array($result)) 
    {
      if(strlen($row['phone'])==10)
        $phone="(".substr($row['phone'],0,3).") ".substr($row['phone'],3,3)."-".substr($row['phone'],6,4);
      else
        $phone="&nbsp;";
      echo "<tr>
       <td><font face=sans-serif size=2>".$row['userid']."</a></font></td>
       <td><font face=sans-serif size=2>".$row['l_name'].", ".$row['f_name']."</font></td>
       <td><font face=sans-serif size=2>".$phone."</font></td>
       <td><font face=sans-serif size=2><a href=customer.php?a=220&userid=".$row['userid'].">Edit";
      if($_SESSION['status']==2 || $_SESSION['status']==3)
        echo "</a> / <a href=customer.php?a=240&userid=".$row['userid'].">Delete";
      echo "</a></font></td></tr>"; 
    } 
    echo "</table>"; 
  } 
  else 
  { 
    echo "<font size=3>User not found.<br><br></font>";
    EditFind();
  } 
}


function EditForm($userid,$edit_type)
{
  include_once "connect.php";


/*leave recessed tabs as transparent. only have borders around current tab and form.
going to need hidden field for edit type: 0 for general, 1 for technical
test for $_GET['type']. */

  if($edit_type==0)
  {
    $query="SELECT id,userid,l_name,f_name,phone,address,city,state,zip FROM customers WHERE userid='".$userid."'";
  }
  else
    $query="SELECT id,userid,system_connection,system_speed,system_os,system_memory FROM customers WHERE userid='".$userid."'";

  $result=mysql_query($query) or die('Error in query'); 
  if(mysql_num_rows($result)==1) 
  { 
    $row=mysql_fetch_array($result); 
  } 
  else 
  { 
    //echo "<br><font size=3>User should exist but wasn't found.<br>Maybe someone just deleted it?</font>"; 
    //exit; 
  }

if($edit_type==0) //left tab
{
  $tab_left="border-left:1px solid #c0c0c0;border-top:1px solid #c0c0c0;";
  $tab_left_bgcolor="#234d76";
  $tab_right="border-bottom:1px solid #c0c0c0;";
  $tab_right_bgcolor="";
}
else
{
  $tab_left="border-bottom:1px solid #c0c0c0;";
  $tab_left_bgcolor="";
  $tab_right="border-top:1px solid #c0c0c0;border-right:1px solid #c0c0c0;";
  $tab_right_bgcolor="#234d76";
}
echo "<form method=post action=customer.php name=tech>\n<input type=hidden name=a value=230>\n<input type=hidden name=id value=",$row['id'],">\n";
if($edit_type==0)
  echo '<input type=hidden name=type value=0><input type=hidden name=cur_userid value=',$row['userid'],'>';
else
{
?>
 <input type=hidden name=type value=1><input type=hidden name=cur_userid value='<?php echo $row['userid'];?>'>
<?php
}
?>
 <table cellspacing=0 cellpadding=0 width=260 style='border:1px solid #c0c0c0;' bgcolor=#384854> <!-- 234d76>-->
  <tr> 
   <td bgcolor=#384854>
    <table cellspacing=0 cellpadding=3 width=100%> 
     <tr> 
      <td align=center colspan=2><font face=sans-serif size=3><b>EDIT CUSTOMER</b></font></td>
     </tr>
    </table>
    <!-- Possibly include current customer userid up here instead of in bottom half,
    such as on Technical Info. -->
   </td>
  </tr><tr>
   <td align=center valign=top height=10><img src=line.gif width=95% height=1></td>
  </tr><tr>
   <td align=center>
    <table cellspacing=0 cellpadding=2 width=90%>
     <tr align=center>
      <td width=50% style='<?php echo $tab_left;?>' bgcolor='<?php echo $tab_left_bgcolor;?>'><table cellspacing=0 cellpadding=0 width=100%><tr><td align=center><font face=sans-serif size=2><b><a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?a=220&userid=<?php echo $row['userid'];?>&type=0" style='color:white'>General</a></b></font></td></tr><tr><td height=5></table></td>
      <td width=50% style='border-left:1px solid #c0c0c0;<?php echo $tab_right;?>;' bgcolor="<?php echo $tab_right_bgcolor;?>"><table cellspacing=0 cellpadding=0 width=100%><tr><td align=center><font face=sans-serif size=2><b><a href="<?php echo $_SERVER['SCRIPT_NAME'];?>?a=220&userid=<?php echo $row['userid'];?>&type=1" style='color:white'>Technical</a></b></font><tr><td height=5></table></td>
     </tr><tr>
      <td colspan=2 align=center style='border-left:1px solid #c0c0c0;border-bottom:1px solid #c0c0c0;border-right:1px solid #c0c0c0;' bgcolor=#234d76>
<?php


if($edit_type==0)
{
  echo "\n      <table cellspacing=0 cellpadding=2 width=90%>
       <tr>
        <td colspan=2 height=4></td>
       </tr><tr>
        <td><font face=sans-serif size=2>User ID</td> 
        <td><input type=text name=userid size=15 maxlength=8 value='".$row['userid']."' onChange='bUseridChanged()'></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>Last name</td> 
        <td><input type=text name=l_name size=15 maxlength=20 value='".$row['l_name']."'></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>First name</td> 
        <td><input type=text name=f_name size=15 maxlength=20 value='".$row['f_name']."'></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>Phone</td> 
        <td><input type=text name=phone size=15 maxlength=15 value='".$row['phone']."'></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>Address</td> 
        <td><input type=text name=address size=15 maxlength=40 value='".$row['address']."'></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>City</td> 
        <td><input type=text name=city size=15 maxlength=15 value='".$row['city']."'></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>State</td> 
        <td><input type=text name=state size=15 maxlength=15 value='".$row['state']."'></td> 
       </tr><tr>
        <td colspan=2 align=center><input type=submit value=' ok '>&nbsp;<input type=button value='cancel' onClick='history.back()'></td> 
       </tr><tr>
        <td colspan=2 height=4></td>
       </tr>
      </table>

      <script language=JavaScript>\n
      var bUserid_Changed=false;\n
      function verify()\n
      {\n
        if(bUserid_Changed==true)\n
        {\n
          if(confirm('Userid has been changed.\\n\\nPressing ok will update userid\\nas well as any other field changes.\\n\\nContinue?')==false)\n
          {\n
            return false; 
          } 
        }

        phone= new String; 
        phone=tech.phone.value; 
        var phonefinal=''; 
        for(var i=0;i<phone.length;i++) 
        { 
          if(phone.charCodeAt(i)>=48 && phone.charCodeAt(i)<-57) 
          { 
            phonefinal=phonefinal+phone.charAt(i); 
          }     
        } 
        tech.phone.value=phonefinal; 
       
        if(tech.phone.value.length<10) 
        { 
          alert('Must include area code.'); 
          return false; 
        } 
        if(tech.l_name.value.length==0) 
        { 
          alert('Must enter a last name.'); 
          tech.l_name.focus(); 
          tech.l_name.select(); 
          return false; 
        } 
        if(tech.f_name.value.length==0) 
        { 
          alert('Must enter a first name.'); 
          tech.f_name.focus(); 
          tech.f_name.select(); 
          return false; 
        } 
        return true; 
      }
      function bUseridChanged() 
      { 
        bUserid_Changed=true; 
        return; 
      } 
      </script>";
}
else
{
  $res_config=mysql_query("select * from config where type=4 order by other,display_order");
  $i=0;
  $prev=-1;
  while($row_config=mysql_fetch_array($res_config))
  {
    if($prev!=$row_config['other'])
      $i=0;
    else
      $i++;
    switch($row_config['other'])
    {
      case 0: //speed
        $speed_array[$i][0]=$row_config['value'];
        $speed_array[$i][1]=$row_config['name'];
        break;
      case 1: //mem
        $mem_array[$i][0]=$row_config['value'];
        $mem_array[$i][1]=$row_config['name'];
        break;
      case 2: //os
        $os_array[$i][0]=$row_config['value'];
        $os_array[$i][1]=$row_config['name'];
        break;
      case 3: //connection
        $connection_array[$i][0]=$row_config['value'];
        $connection_array[$i][1]=$row_config['name'];
        break;
    }
    $prev=$row_config['other'];
  }

  echo "      <table cellspacing=0 cellpadding=2 width=90%>
       <tr>
        <td colspan=2 height=4></td>
       </tr><tr>
        <td><font face=sans-serif size=2>User ID</td> 
        <td><font face=sans-serif size=2>".$row['userid']."</font></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>System Speed</td> 
        <td><select name=speed><option value='0'></option>";
        GetTechSpec($speed_array,$row['system_speed']);
   echo "</select></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>System RAM</td> 
        <td><select name=memory><option value='0'></option>";
        GetTechSpec($mem_array,$row['system_memory']);
   echo "</select></td> 
       </tr><tr> 
        <td><font face=sans-serif size=2>System OS</td> 
        <td><select name=os><option value='0'></option>";
        GetTechSpec($os_array,$row['system_os']);
   echo "</select></td> 
       </tr><tr>
        <td><font face=sans-serif size=2>Connection</td>
        <td><select name=connection><option value=0></option>";
        GetTechSpec($connection_array,$row['system_connection']);

   echo "</select></td>
       </tr><tr>
        <td colspan=2 align=center><input type=submit value=' ok '>&nbsp;<input type=button value='cancel' onClick='history.back()'></td> 
       </tr><tr>
        <td colspan=2 height=4></td>
       </tr>
      </table>";
}
echo "     </td>
    </tr>
   </table>
  </td> 
 </tr><tr>
  <td height=10></td>
 </tr>
</table> 
</form>";
}


function EditUpdateDB()
{
  include_once 'connect.php';
  include 'status.php';
  if($_POST['type']==0) //General
  {
    //need error checking

    if(strcmp($_POST['cur_userid'],$_POST['userid'])==0) //Userid not changed, update all else
    {
     $query="UPDATE customers SET l_name='".$_POST['l_name']."',f_name='".$_POST['f_name']."',phone='".$_POST['phone']."',address='".$_POST['address']."',city='".$_POST['city']."',state='".$_POST['state']."' WHERE id='".$_POST['id']."'";
     $result=mysql_query($query);
     DisplayStatus($_POST['cur_userid'],'cust_edit',$result);
    } 
    else //userid changed 
    { 
      //$userid=ret_alpha_numer($_POST['userid']);
      //$userid=strtolower($userid);
      echo "<br><table cellspacing=0 cellpadding=0 style='border: 1px solid #c0c0c0'><tr><td width=100% align=center valign=top><font face=sans-serif size=4><b>STATUS</b></font></td></tr><tr align=left valign=middle><td width=100%><table cellspacing=0 cellpadding=2 border=0><tr>"; 
    echo "<td width=205 align=left><font face=sans-serif size=3>Duplicate user id:</font></td>"; 
    $result=mysql_query("SELECT * FROM customers WHERE userid='$userid';"); 
    if(mysql_num_rows($result)>0) 
    { 
      echo "<td width=95 align=right><font face=sans-serif size=3>exists.</font></td></tr><tr><td width=100% colspan=2 align=left><font face=sans-serif size=3>Nothing changed.</font></td>"; 
      exit; 
    } 
    else 
    { 
      echo "<td width=95 align=right><font face=sans-serif size=3>not found.</td></tr><tr><td><font face=sans-serif size=3>Changing user id:</font></td>"; 
      $result=mysql_query("UPDATE customers SET userid='$userid',l_name='".$_POST['l_name']."',f_name='".$_POST['f_name']."',phone='".$_POST['phone']."' WHERE userid='".$_POST['cur_userid']."';"); 
      if($result) 
      { 
        echo "<td align=right><font face=sans-serif size=3>success.</font></td></tr><tr><td><font face=sans-serif size=3>Changing tech call information:</font></td>"; 
        $result=mysql_query("UPDATE calls SET userid='$userid' WHERE userid='".$_POST['cur_userid']."';"); 
        if($result) 
        { 
          echo "<td align=right><font face=sans-serif size=3>success.</font></td></tr>"; 
          $date=getdate(); 
          $month = str_pad($date['mon'],2,"0",STR_PAD_LEFT); 
          $day = str_pad($date['mday'],2,"0",STR_PAD_LEFT); 
          $hours = str_pad($date['hours'],2,"0",STR_PAD_LEFT); 
          $minutes = str_pad($date['minutes'],2,"0",STR_PAD_LEFT); 
          $seconds = str_pad($date['seconds'],2,"0",STR_PAD_LEFT); 
          $dt=$date['year'].$month.$day.$hours.$minutes.$seconds; 
          echo "<tr><td><font face=sans-serif size=3>Adding change to call log:</font></td>"; 
          $result=mysql_query("INSERT INTO calls (userid,dt_start,tech_id,problem,verified,notes,status) VALUES('".$userid."','".$dt."','".$_SESSION['id']."','32','0','Changed userid from ".$_POST['cur_userid']."','10')");
          if($result) 
            echo "<td align=right><font face=sans-serif size=3>success.</font></td>";
          else 
            echo "<td align=right><font face=sans-serif size=3>failed.</font></td>";   
        } 
        else 
          echo "<td align=right><font face=sans-serif size=3>failed.</font></td>"; 
      } 
      else 
      { 
        echo "<td align=right><font face=sans-serif size=3>failed.</font></td></tr><tr><td colspan=2><font face=sans-serif size=3><b>Not</b> updating tech call information.</font></td>"; 
      }       
    } 
    echo "</tr></table></td></tr></table>"; 
  }
  }
  else //Technical Information Updated
  {
    $query="UPDATE customers SET system_speed=".$_POST['speed'].",system_memory=".$_POST['memory'].",system_os=".$_POST['os'].",system_connection=".$_POST['connection']." WHERE id=".$_POST['id'];
    $result=mysql_query($query);
    DisplayStatus($_POST['cur_userid'],'cust_edit',$result);
  }
}

function EditDelete()
{
  if($_SESSION['status']==2 || $_SESSION['status']==3)
  {
    require "connect.php";
    $result=mysql_query("DELETE FROM customers WHERE userid='".$_POST['userid']."';");

    /* need to do a real check on the return from the delete sql statement */

    if($result)
    {
      echo "<br><font size=3>User deleted from customers DB.</font>";
    }
    else
      echo "<br><font size=3>not deleted from customers DB.</font>";
  } 
  else 
  { 
    echo "<br><font size=3>Access denied. :)"; 
    exit; 
  } 
}


//OTHER FUNCTIONS
function ret_alpha_numer($string)
{
  $final='';
  for($i=0;$i<strlen($string);$i++)
  {
    if(('a'<=substr($string,$i,1)&&substr($string,$i,1)<='z') || ('A'<=substr($string,$i,1)&&substr($string,$i,1)<='Z') || ('0'<=substr($string,$i,1)&&substr($string,$i,1)<='9'))
      $final.=substr($string,$i,1);
  }
  return $final;
}

function GetTechSpec($tech_array,$data)
{
  for($i=0;$i<sizeof($tech_array);$i++)
  {
    echo '<option value=',$tech_array[$i][0];
    if($tech_array[$i][0]==$data)
      echo ' selected';
    echo '>',$tech_array[$i][1],'</option>';
  }
}
?>
