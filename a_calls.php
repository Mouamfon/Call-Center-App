<?php
//  ADMINISTRATIVE FUNCTIONS  
//  MANAGE CALLS 

include "header.php";

if(isset($_GET['a']))
{
  switch($_GET['a'])
  {

  }
}
elseif(isset($_POST['a']))
{
  switch($_POST['a'])
  {

  }
}
else
{
  ShowCallMenu();
}


  
function ShowCallMenu()
{
  include 'misc.inc.php';
  SimpleForm("CALL MANAGEMENT MENU",470);
?>

  <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=95%>
   <tr>
    <td colspan=2 height=6></td>
   </tr><tr align=center valign=top>
    <td width=50%><font face=arial size=3 color=white><a href=a_calls.php?a=100>Export Calls</a><br><font size=2>(Export calls by date.)</font></td>
    <td width=50%><font face=arial size=3 color=white><a href=a_calls.php?a=200>Manage Archives</a><br><font size=2>(View, rename, delete archives.)</font></td>
   </tr><tr>
    <td colspan=2 height=6></td>
   </tr>
  </table>
<?php
  SimpleFormClose();
}


function ShowExportForm()
{
      $date=getdate();  
      echo "<table cellspacing=0 cellpadding=0 border=1 bordercolor=white bgcolor=#234D76>\n
 <tr>\n
  <td>\n
   <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=100%>\n
    <tr>\n
     <td align=center bgcolor=white width=100%><font face=sans-serif size=3 color=#234D76><b>EXPORT CALLS</b></font></td>\n
    </tr><tr>\n
     <td align=center width=100%>\n
      <form method=post action=a_calls.php name=tech>\n
      <input type=hidden name=a value=101>\n
      <br><table cellspacing=0 cellpadding=3 border=0 width=100%>\n
       <tr>\n
        <td><font face=arial size=2><b>Start Date</b></font></td>\n
        <td>&nbsp;&nbsp;<select name=s_mon>";    
        for($i=1;$i<=12;$i++)  
        {  
        if($i==$date['mon'])  
          echo "<option value=".$i." SELECTED>".$row;  
        else  
          echo "<option value=".$i.">";  
        echo get_month($i);  
        echo "</option>";      
        }  
        echo "</select>&nbsp;<input type=text name=s_day size=2 maxlength=2 value='".str_pad($date['mday'],2,"0",STR_PAD_LEFT)."' onClick=this.select()>&nbsp;<input type=text name=s_year size=4 maxlength=4 value='".$date['year']."' onClick=this.select()></td>\n
       </tr><tr>\n
        <td><font face=arial size=2><b>End Date</b></font></td>\n
        <td>&nbsp;&nbsp;<select name=e_mon>";    
        for($i=1;$i<=12;$i++)  
        {  
        if($i==$date['mon'])  
          echo "<option value=".$i." SELECTED>".$row;  
        else  
          echo "<option value=".$i.">";  
        echo get_month($i);  
        echo "</option>\n";
        }  
        echo "</select>&nbsp;<input type=text name=e_day size=2 maxlength=2 value='".str_pad($date['mday'],2,"0",STR_PAD_LEFT)."' onClick=this.select()>&nbsp;<input type=text name=e_year size=4 maxlength=4 value='".$date['year']."' onClick=this.select()></td>\n
       </tr><tr>\n
        <td align=center colspan=2><br>&nbsp;<input type=submit value='Export'><br>&nbsp;</td>\n
       </tr>\n
      </table>\n  
      </form>
     </td>\n
    </tr><tr>\n
     <td colspan=2 bgcolor=white align=center><font face=arial size=2 color=#234D76>Enter tech information to add.</font></td>\n
    </tr>\n
   </table\n
  </td>\n
 </tr>\n
</table>\n";

}

function DoExport()
{
      echo "<br><table cellspacing=0 cellpadding=3 style='border:1px solid #c0c0c0'>\n";       
      echo "<tr>\n<td width=150><font face=arial size=2>Connect to target DB:</font></td>\n";  
      t_select();  
      echo "<td align=right width=100><font face=arial size=2>success.</td>\n</tr>";  
      if(isset($_POST['s_year'])&&isset($_POST['s_mon'])&&isset($_POST['s_day'])) //SPECIFIYING START DATE, when exporting if no record has this date, prompt to rename
      {  
        $iExported=0;
        $bTableDup=false;  
        $s_mon=str_pad($_POST['s_mon'],2,"0",STR_PAD_LEFT);
        $s_day=str_pad($_POST['s_day'],2,"0",STR_PAD_LEFT);
        $e_mon=str_pad($_POST['e_mon'],2,"0",STR_PAD_LEFT);
        $e_day=str_pad($_POST['e_day'],2,"0",STR_PAD_LEFT);
        $result=mysql_list_tables("export_calls");  
        $num_rows=mysql_num_rows($result);  
        if($num_rows>0)  
        {          
          $s_table_name="T".$s_mon.$s_day.$s_year."_".$e_mon.$e_day.$e_year;  
          
          for($i=0;$i<$num_rows;$i++)  
          {  
            $table_name=mysql_tablename($result,$i);  
            if(strcmp($table_name,$s_table_name)==0)  
            {  
              $bTableDup=true;  
              break;  
            }  
          }  
          if($bTableDup)  
          {  
            echo "<tr>\n<td colspan=2><font face=arial size=2>Error. Duplicate table found. <a href='javascript:history.back()'>go back.</a></td>\n</tr>\n</table>\n";  
            exit;  
          }  
        }  
  
        $e_date=$e_mon.$e_day.$_POST['e_year'];
  
        $fDate=$e_date;  
        $lDate=$s_mon.$s_day.$_POST['s_year'];
        $bMadeTable=false;  
        $i=0;
        
        $s=mktime(0,0,0,$s_mon,$s_day,$_POST['s_year']);
        $f=mktime(0,0,0,$e_mon,$e_day,$_POST['e_year']);
        
        while($s<=$f)
        {  
          //echo "Finding calls within date range: ";  
          $s_date=date(mdY,$s);
          s_select();
          $rSource=mysql_query("SELECT * FROM calls LEFT JOIN techs ON techs.id=calls.tech_id WHERE date LIKE '$s_date%';");
          $num_rows=mysql_num_rows($rSource);
          if($num_rows>0)  
          {  
            if(!$bMadeTable)  
            {  
              echo "<tr>\n<td><font face=arial size=2>Creating target export table:</font></td>\n";  
              t_select();
              mysql_query("CREATE TABLE temp (userid char(20),date char(14),tech char(20),problem int,verified int,notes text,status int);");
              /*if(!$rTarget) 
              { 
                echo "<td align=right><font face=arial size=2>failed</font></td>\n</tr>"; 
                echo mysql_error(); 
                exit; 
              } */ 
              $bMadeTable=true;  
              echo "<td align=right><font face=arial size=2>success</font></td>\n</tr>"; 
            }   
            //echo "success.<br>Exporting ".$num_rows." records .";               
            
            while($row=mysql_fetch_array($rSource)) 
            {          
              $temp_date=substr($row['date'],0,8); 
              if(strcmp($fDate,$temp_date)>0)  
                $fDate=$temp_date;  
              if(strcmp($lDate,$temp_date)<0)  
                $lDate=$temp_date;  
              t_select();
              if(!strstr($row['notes'],"\'"))
                $notes=str_replace("'","\'",$row['notes']);
              mysql_query("INSERT INTO temp (userid,date,tech,problem,verified,notes,status) VALUES('".$row['userid']."','".$row['date']."','".$row['tech_name']."','".$row['problem']."','".$row['verified']."','".$notes."','".$row['status']."');");
              $num_rows=mysql_affected_rows(); 
              if($num_rows>=1)  
              {
                $iExported++;
              }
              else
              {
                echo "<tr>\n<td colspan=2><font face=arial size=2>Failed to insert record. Quitting.</td>\n</tr>\n</table>"; 
                echo "<br>".mysql_error(); 
                exit;  
              }
                
              //if($i%2==0)  
                //echo ".";  
              //$i++;   
            }            
          }  
          else  
          { 
            echo "no records";
          }
          $s+=86400; 
        }  
         
        t_select();
        echo "<tr>\n<td><font face=arial size=2>Number of records exported</font></td>\n<td align=right><font face=arial size=2>".$iExported."</font></td>\n</tr>";
        $t_name="T".substr($fDate,4,4).substr($fDate,0,2).substr($fDate,2,2)."_".substr($lDate,4,4).substr($lDate,0,2).substr($lDate,2,2); 
        $result=mysql_query("RENAME TABLE temp TO ".$t_name.";"); 
        if($result) 
          echo "<tr>\n<td>&nbsp;</td>\n<td>&nbsp;</td>\n</tr><tr>\n<td colspan=2><font face=arial size=2>New table is called: ".$t_name."</font></td>\n</tr></table>";
        else 
        { 
          echo "<tr>\n<td colspan=2><font face=arial size=2>Table not renamed.</font></td>\n</tr></table>";
          t_select();  
          mysql_query("DROP TABLE IF EXISTS temp;");  
        } 
        echo "<br>To delete exported records <a href='a_calls.php?p=300&s_date=$s_date&e_date=$e_date'>click here</a>.";
  
      }  
}

function ShowExports()
{
  include 'connect.php';
  $sql_alt=AltConnect();
  $result=mysql_list_tables("export_calls",$sql_alt);
       
  if(mysql_num_rows($result)==0) 
  { 
    echo "<br>No archives."; 
    exit; 
  } 
  echo "<script language=JavaScript>function d_verify(table_name)\n{\nif(confirm('Are you sure you want to the delete the table?'))\ndocument.location.href='a_calls.php?p=231&tn='+ table_name;\n}\n </script>";
  echo "<br><table cellspacing=0 cellpadding=3 bgcolor=#234D76 style='border: 1px solid #c0c0c0'>\n<tr><td width=175><font face=arial size=3><b>Archive Name</b></font></td>\n<td><font face=arial size=3><b>Action</b></font></td>\n</tr>";
  for($i=0;$i<mysql_num_rows($result);$i++)  
  {        
    echo "<tr>\n<td><font face=arial size=2>".mysql_tablename($result,$i)."</font></td>\n<td><font face=arial size=2><a href=view_archive.php?tn=".mysql_tablename($result,$i).">View</a> / <a href=a_calls.php?p=240&tn=".mysql_tablename($result,$i).">Ren</a> / <a href=javascript:d_verify('".mysql_tablename($result,$i)."')>Del</a></font></td>\n</tr>\n";
  } 
  echo "</table>"; 
}
  
//alandrums@yahoo.com  

/*
    case '210': //SEARCH ARCHIVES        
      break;
*/

function DoSearch()
{
      t_select(); 
      $result=mysql_query("SELECT * FROM $tn;"); 
      if(!$result) 
      { 
        echo "No exported archives."; 
        exit; 
      } 
      echo "<br>";
      $num_rows=mysql_num_rows($result); 
      if($num_rows>0) 
      { 
        while($row=mysql_fetch_array($result)) 
        { 
          echo $row['userid']." ".$row['date']."<br>"; 
        } 
      } 
      else 
        echo "<br>No records."; 
      break;
}

function DropExport($tn)
{
  include 'connect.php';
  $sql_alt=AltConnect();
 
  echo "<br>Dropping ".$tn.": ";
  $result=mysql_query("DROP TABLE IF EXISTS ".$tn.";");
  if($result)  
    echo "success.";  
  else  
    echo "failed.";  
}  

function ShowRenameTableForm()
{
  include 'connect.php';
  $sql_alt=AltConnect();

      $result=mysql_list_tables("export_calls");  
      if(mysql_num_rows($result)==0) 
      { 
        echo "<br>No archives."; 
        exit; 
      } 
      echo "<br><form method=post action=a_calls.php name=tech>
     <input type=hidden name=p value=241>
     <input type=hidden name=orig value='".$_GET['tn']."'>
<table cellspacing=0 cellpadding=0 border=1 bordercolor=white bgcolor=#234D76>
 <tr>
  <td>
   <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=100%>
    <tr>
     <td align=center bgcolor=white width=100%><font face=sans-serif size=3 color=#234D76><b>RENAME ARCHIVE</b></font></td>
    </tr><tr>
     <td align=center width=100%><br>  
      <table cellspacing=0 cellpadding=3 border=0 width=100%>
       <tr>
        <td><font face=arial size=2>Change:</font></td><td><input type=text value='".$_GET['tn']."' size=20 disabled></font></td></tr>";
      echo "<tr><td><font face=arial size=2>To:</td><td><input type=text name=new size=20 maxlength=30></td></tr><tr><td colspan=2 align=center><input type=submit value=' Ok '><br>&nbsp;</td></tr></table></td></tr>}</table>";
}


function RenameTable()
{
  include 'connect.php';
  $sql_alt=AltConnect();
      if(isset($_POST['orig'])&&isset($_POST['new']))
      {  
        t_select();  
        $result=mysql_query("RENAME TABLE ".$_POST['orig']." TO ".$_POST['new'].";");
        if($result)
          echo "<br>Renamed ".$_POST['orig']." to ".$_POST['new'].".";
        else
        {
          echo "<br>Rename failed.<br><br>";  
          echo mysql_error();
        }
      }
}
 
  
?>
