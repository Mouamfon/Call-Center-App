<?php
//ADMINISTRATIVE FUNCTIONS
//TECHNICIANS
include "header.php";


if($_SESSION['access_level']!=3)
{
  logout();
  ShowLogin(1);
}

if(isset($_GET['a']))
{
  switch($_GET['a'])
  {
    case 100: //add tech
      ShowAddTechForm();
      break;
    case 110: //edit tech list
      ShowTechList();
      break;
    case 120: //edit tech form
      ShowEditTechForm($_GET['id']);
      break;
  }
}
elseif(isset($_POST['a']))
{
  switch($_POST['a'])
  {
    case 101: //add tech submit
      AddTechToDB($_POST['techpin'],$_POST['tech_name'],$_POST['access_level']);
      break;
    case 121: //edit submit
      UpdateTechDB($_POST['id'],$_POST['pin'],$_POST['name'],$_POST['access_level']);
      break;
  }
}
 
function ShowAddTechForm()
{
  include 'misc.inc.php';
  SimpleForm("ADD TECH",250);
?>
  <form method=post action=a_techs.php name=frm>
  <input type=hidden name=a value=101>
  <table cellspacing=0 cellpadding=2 border=0 width=95%> 
   <tr> 
    <td width=80><font face=sans-serif size=2>Tech Pin</font></td> 
    <td width=*><input type=text name=techpin size=15 maxlength=10></td>
   </tr><tr> 
    <td width=80><font face=sans-serif size=2>Tech Name</font></td> 
    <td width=*><input type=text name=tech_name size=15 maxlength=20></td>
   </tr><tr> 
    <td><font face=sans-serif size=2>Access Level</font></td>
    <td><select name=access_level><option value=1>Technician</option><option value=2>Supervisor</option><option value=3>Admin</option></select></td>
   </tr><tr> 
    <td colspan=2 align=center><input type=submit value='Add'></td>
    </tr>
  </table> 
  </form>
<?php
  SimpleFormClose();
}

function AddTechToDB($pin,$tech_name,$access_level)
{
  require 'connect.php';
  include 'status.php';

  if(strlen($pin)>0 && strlen($tech_name)>0)
  {
    $res_tech=mysql_query("SELECT id FROM techs WHERE pin='$pin';") or die("Error in query");
    if(mysql_num_rows($res_tech)==0)
    {
      $res=mysql_query("INSERT INTO techs (pin,name,access_level) VALUES('$pin','$tech_name','$access_level');");
      DisplayStatus($tech_name,'tech_add',$res);
    }
    else
    {
      $row=mysql_fetch_array($result);
      ShowEditTechForm($row['id']);

    }
  }
  else 
  {
    echo "No Data Entered<br><br>";
    ShowAddTechForm();
  } 
}

function ShowTechList()
{
  require 'connect.php';
  include 'misc.inc.php';
  $result=mysql_query("SELECT * FROM techs ORDER BY pin ASC;"); 
  if(mysql_num_rows($result)==0) 
  { 
    echo "No pins in DB."; 
    exit; 
  } 
?>
  <script language=JavaScript>  
  function d_verify(p) 
  { 
    if(confirm('Are you sure you want to delete ?\n\nIt is not recommended that you delete a Tech.\nOtherwise simply edit the pin.'))
      document.location.href='a_techs.php?p=220&id='+ p; 
  } 
  </script>
<?php
  SimpleForm("TECHS",300);
?>
    <table cellspacing=0 cellpadding=2 width=95%>
     <tr> 
       <td colspan=4 height=4></td>
     </tr><tr>
      <td width=35%><font face=sans-serif size=2><b>Tech Name</b></font></td>
      <td width=35%><font face=sans-serif size=2><b>Access Level</b></font></td>
      <td width=30%><font face=sans-serif size=2><b>Action</b></font></td>
     </tr><tr>
<?php
  while($row=mysql_fetch_array($result)) 
  {
    echo '<tr><td><font face=sans-serif size=2>',$row['name'],'</font></td><td><font face=sans-serif size=2>';
    switch($row['access_level']) 
    { 
      case 1: 
        echo 'Tech'; 
        break; 
      case 2: 
        echo 'Supervisor'; 
        break; 
      case 3: 
        echo 'Admin'; 
        break; 
    } 
    echo '</font></td><td><font face=sans-serif size=2><a href=a_techs.php?a=120&id=',$row['id'],'>edit</a> / <a href=JavaScript:d_verify("',$row['id'],'")>delete</a></font></td></tr>';
  }  
?>
    </table>
<?php
  SimpleFormClose();
} 


function ShowEditTechForm($tech_id)
{
  include 'connect.php';
  include 'misc.inc.php';
  $result=mysql_query("SELECT * FROM techs WHERE id='".$tech_id."'") or die("Error in query.");

  if(mysql_num_rows($result)==0) 
  { 
    echo "<br>Error. No records selected."; 
    exit; 
  } 
  $row=mysql_fetch_array($result); 
?>
  <script language=JavaScript> 
  function verify() 
  { 
    if(length(document.tech.tech_name.value)==0) 
    { 
      alert('Must specify a tech name.'); 
      return false; 
    } 
    return true; 
  } 
  </script>
  <form method=post action='a_techs.php' name=tech onSubmit='return verify()'> 
  <input type=hidden name=a value='121'>
  <input type=hidden name=id value='<?php echo $tech_id;?>'> 
<?php
  SimpleForm("EDIT TECH",240);
?>
  <table cellspacing=0 cellpadding=2 width=95%>
   <tr> 
    <td><font face=sans-serif size=2>Pin</td> 
    <td><input type=text name=pin size=15 maxlength=10 value='<?php echo $row['pin'];?>'></td> 
   </tr><tr> 
    <td><font face=sans-serif size=2>Tech name</td> 
    <td><input type=text name=name size=15 value='<?php echo $row['name'];?>'></td> 
   </tr><tr> 
    <td><font face=sans-serif size=2>Access Level</font></td>
    <td><select name=access_level><option value='1'<?php if($row['access_level']==1) echo " SELECTED";?>>Technician</option><option value='2'<?php if($row['access_level']=='2') echo " SELECTED";?>>Supervisor</option><option value=3<?php if($row['access_level']=='3') echo " SELECTED";?>>Admin</option></select></td> 
   </tr><tr>
    <td colspan=2 align=center><input type=submit value=' Ok '><br>&nbsp;</td>
   </tr> 
  </table> 
  </form>
<?php
  SimpleFormClose();
}

function UpdateTechDB($id,$pin,$name,$access_level)
{
  include 'connect.php';
  include 'status.php';
  $query="UPDATE techs SET pin='".$pin."',name='".$name."',access_level='".$access_level."' WHERE id='".$id."'";
  $result=mysql_query($query) or die("Error in query");
  DisplayStatus($name,'tech_edit',$result);
}

function EditTechDelete()
{
 
    connect(); 
    $r1=mysql_query("DELETE FROM techs WHERE id='".$_GET['id']."';");
    if($r1>0) 
      echo "<br>Entry deleted."; 
    else 
    { 
      echo "<br>Entry not deleted."; 
    }    
  echo "</body></html>"; 
}

?>
