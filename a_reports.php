<?php
//  ADMINISTRATIVE FUNCTIONS  
//  REPORTS

if(isset($_GET['a']))
{
  switch($_GET['a'])
  {
    case 100: //Call Reports
      require "a_header.php";
      show();
      break;
    case 101: //show report
      report();
      break;
  }
}
elseif(isset($_POST['a']))
{

}
else
{
  require "a_header.php";
  echo "<table cellspacing=0 cellpadding=0 width=470 style='border:1px solid #c0c0c0'>\n  
 <tr>\n  
  <td>\n  
   <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=100%>\n  
    <tr>\n  
     <td colspan=2 align=center><font face=sans-serif size=3 color=white><b>REPORTS MENU</b></font><br><br></td>\n  
    </tr><tr align=center valign=top>\n  
     <td width=50%><font face=arial size=3 color=white><a href=a_reports.php?a=100>Call Volume Report</a><br><font size=2>(Call volume reports.)</font></td>\n  
     <td width=50%><font face=arial size=3 color=white><a href=a_calls.php?a=200></a><br><font size=2></font></td>\n  
    </tr><tr>\n
     <td>&nbsp;</td>
     <td>&nbsp;</td>
    </tr>\n
   </table>\n  
  </td>\n
 </tr>\n  
</table>";  
}  


function show()
{
  include '../misc.inc.php';
  $date=getdate();
  $mon=date('m');
  $result=mysql_query("SELECT id,name FROM techs;");

      echo "<script language=javascript>
      function select_techs()
      {
        for(i=0;i<document.frm.techs.options.length;i++)
        {
          document.frm.techs.options[i].selected=true;
        }
        sel_tech();
      }
      function sel_tech()
      {
        var sel=\"\";
        for(i=0;i<document.frm.techs.options.length;i++)
        {
          if(document.frm.techs.options[i].selected==true)
            sel+=document.frm.techs.options[i].value + \",\";
        }
        document.frm.tech_list.value=sel;
      }
      function month()
      {
        clr();";
        for($i=1;$i<13;$i++)
          if($i<=$mon)
            echo 'document.frm.other.options[document.frm.other.options.length]=new Option("'.get_month($i).'","'.$i.'",false,false);';

      echo "\n      }
      function clr()
      {
        var count=document.frm.other.options.length;
        for(i=0;i<count;i++)
        {
          document.frm.other.options[0]=null;
        }
      }
      function check()
      {


      }

      function report()
      {
        var time_period;
        for(i=0;i<document.frm.time_period.length;i++)
          if(document.frm.time_period[i].checked)
          {
            time_period=document.frm.time_period[i].value;
          }
        
        window.open(\"a_reports.php?a=101&time_period=\"+time_period+\"&type=\"+document.frm.type.value+\"&tech_list=\"+document.frm.tech_list.value+\"&other=\"+document.frm.other.value,\"report\",\"screenX=0,screenY=0,width=800,height=500,toolbar=yes\");
      }
      </script>";
      echo "<form name=frm>\n
      <input type=hidden name=tech_list>
<table cellspacing=0 cellpadding=0 border=1 bordercolor=white bgcolor=#234D76 width=280>\n
 <tr>\n
  <td>\n
   <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=100%>\n
    <tr>\n
     <td align=center bgcolor=white width=100%><font face=sans-serif size=3 color=#234D76><b>CALL VOLUME REPORT</b></font></td>\n
    </tr><tr>
     <td align=center width=100%>
      <table cellspacing=0 cellpadding=2 border=0 width=90%>
       <tr>
        <td colspan=2><font size=2 face=sans-serif>&nbsp;</font></td>
       </tr><tr>
        <td colspan=2 align=center>
         <table cellspacing=0 cellpadding=2 border=0 width=100%>
          <tr>
           <td width=15><input type=radio name=time_period value=0 onClick=clr()></td>
           <td><font face=sans-serif size=2>Months : YTD</font></td>\n
           <td width=15></td>
           <td width=15><input type=radio name=time_period value=1 onClick=month()></td>
           <td><font face=sans-serif size=2>Specific Month</font></td>\n
          </tr><tr>\n
           <td width=15><input type=radio name=time_period value=2 onClick=clr() checked></td>
           <td><font face=sans-serif size=2>Current Week</font></td>\n
           <td width=15></td>
           <td width=15><input type=radio name=time_period value=3 onClick=clr()></td>
           <td><font face=sans-serif size=2>Today</font></td>\n
	  </tr><tr>
           <td width=15><input type=radio name=time_period value=4 onClick=clr()></td>
           <td><font face=sans-serif size=2>All Years</font></td>
           <td width=15></td>
           <td width=15></td>
           <td><font face=sans-serif size=2></font></td>
	  </tr>
	 </table>
        </td>
       </tr><tr>\n
        <td colspan=2><font size=2 face=sans-serif>&nbsp;</font></td>
       </tr><tr valign=top>\n
        <td><font face=sans-serif size=2>Select Tech(s)<br>&nbsp;&nbsp;&nbsp;<a href='javascript:select_techs()'>all</a></font></td>
        <td align=right width=100><select name='techs' size=3 multiple onClick=sel_tech()>";
         if(!$result || mysql_num_rows($result)==0)
         {
         }
         else
         {
           while($row=mysql_fetch_array($result))
           {
             echo "<option value='".$row['id']."'>".$row['name']."</option>";
           }
         }
         echo "</select>
        </td>
       </tr><tr>\n
        <td><font size=2 face=sans-serif>Month</font></td>
        <td align=right><select name=other></select></td>
       </tr><tr>
        <td colspan=2><font face=sans-serif size=1>&nbsp;</font></td>
       </tr><tr valign=top>\n
        <td><font face=sans-serif size=2>Type</font></td>
        <td>
         <table cellspacing=0 cellpadding=2 border=0 width=100%>\n
          <tr>\n
           <td width=15><input type=radio name=type value=0 checked></td>
           <td><font face=sans-serif size=2>Number Chart</font></td>\n
          </tr><tr>\n
           <td width=15><input type=radio name=type value=1></td>
           <td><font face=sans-serif size=2>Graph</font></td>\n
	  </tr>
	 </table>
        </td>
       </tr><tr>
        <td colspan=2 align=center><input type=button value=' ok ' onClick='report();'></td>
       </tr><tr>
        <td colspan=2><font face=sans-serif size=1>&nbsp;</font></td>
       </tr>
      </table>
     </td>
    </tr><tr>
     <td bgcolor=white align=center><font face=arial size=2 color=#234D76>Choose report information.</font></td>\n
    </tr>
   </table>\n
  </td>\n
 </tr>\n\n
</table>\n
</form>";
}

function report()
{
  /* for reports, need to only display call stats if the tech has had a new call within that time frame,
  otherwise the stat info will be wrong. perhaps that's why i initially started printing separate tables
  each tech. this is where the $where variable will come in. i'll have to check for correct timestamp
  ranges and specify that only those techs that have had activity within that time period will show up */


  include 'connect.php';
  include '../misc.inc.php';
  connect();
  $where="";
  $tech="";
  $temp=0;

  $tech_list=$_GET['tech_list'];
  switch($_GET['time_period'])
  {
    case 0: //Past Year
      $select="id,name,status,calls_jan,calls_feb,calls_mar,calls_apr,calls_may,calls_jun,calls_jul,calls_aug,calls_sept,calls_oct,calls_nov,calls_dec";
      $width=760;
      $where="date>'".mktime(23,59,59,12,31,date('Y')-1)."' AND (";
      break;
    case 1: //Certain Month
      $temp=mktime(0,0,0,$_GET['other'],1,date('Y'))-1;
      $select="id,name,status,".get_month_field($_GET['other']);
      $where="date>'".$temp."' AND (";
      $width=400;
      break;
    case 2: //Past Week
      $i=0;
      $temp=time();
      while(date('w',$temp)>0)
      {
        $i++;
        $temp-=60*60*24;
      }
      $temp-=60*60*24;
      $where="date>'".mktime(23,59,59,date('m',$temp),date('d',$temp),date('Y',$temp))."' AND (";
      $select="id,name,status,calls_week";
      $width=400;
      break;
    case 3: //Today
      $temp=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
      $select="id,name,status,calls_today";
      $where="date>'".$temp."' AND (";
      $width=400;
      break;
  }


  $len=strlen($tech_list);
  for($i=0;$i<$len;$i++)
  {
    $c=substr($tech_list,$i,1);
    if($c == ',')
    {
      $where.=" id=".$tech."";
      if($i<$len-1)
        $where.=" OR";
      $tech="";
    }
    else
      $tech=$c;

  }

  $result=mysql_query("SELECT ".$select." FROM techs WHERE ".$where.")");
  
  echo "<html>
  <head>
  <title>Call Volume Report</title>
  <style type=text/css>
  td.dat {border-right: 1px solid black;border-bottom:1px solid black;}
  </style></head>
  <body><center><table cellspacing=0 cellpadding=0 style='border-left:1px solid black;border-top:1px solid black;border-right:1px solid black;' width=".$width.">
   <tr>
    <td style='border-bottom:1px solid black;border-right:1px solid black;'>
     <table cellspacing=0 cellpadding=3 width=100%>
      <tr>
       <td align=center><font face=sans-serif size=3>Call Volume Report : ".get_report_time_period($_GET['time_period'],$_GET['other'])."</font></td>
      </tr>
     </table>
    </td>
   </tr><tr>
    <td style='border-bottom:1px solid black;'>
     <table cellspacing=0 cellpadding=2 width=100%>
      <tr align=center valign=top>";

  $tech=0;
  $yr=date('Y');
  $mon=date('m');

      switch($_GET['time_period'])
      {
        case 0:
          echo "<td class=dat><font face=sans-serif size=2>Techs</font></td>";
          for($i=1;$i<13;$i++)
          {
            if($mon<$i)
              break;
            else
              echo "<td class=dat><font face=sans-serif size=2>".get_month_abbrev($i)."</font></td>";
          }
          break;
        case 1: //month
          echo "<td class=dat><font face=sans-serif size=2>Techs</font></td>
           <td class=dat><font face=sans-serif size=2>Calls</font></td>
          </tr>";
          break;
        case 2: //week
          echo "<td class=dat><font face=sans-serif size=2>Techs</font></td>
           <td class=dat><font face=sans-serif size=2>Calls</font></td>
          </tr>";
          break;
        case 3: //today
          break;
      }

  //echo "</table></td></tr><tr><td><table cellspacing=0 cellpadding=2 width=100% >";

  while($row=mysql_fetch_array($result))
  {
    switch($_GET['time_period'])
    {
      case 0:
        echo "<tr align=right>
          <td align=left class=dat><font face=sans-serif size=2>".$row['name']."</font></td>";
          for($i=1;$i<13;$i++)
          {
            if($mon<$i)
              break;
            else
              echo "<td class=dat><font face=sans-serif size=2>".$row[1+$i]."</font></td>";
          }
        break;
      case 1: //month
        echo "<tr align=right>
         <td align=left class=dat><font face=sans-serif size=2>".$row['name']."</font></td>
         <td class=dat><font face=sans-serif size=2>".$row[3]."</font></td>
        </tr>";
        break;
      case 2: //week
        echo "<tr>
         <td align=left class=dat><font face=sans-serif size=2>".$row['name']."</font></td>
         <td align=right class=dat><font face=sans-serif size=2>".$row['calls_week']."</font></td>
        </tr>";
        break;
      case 3:
        echo "<tr align=right>
         <td class=dat><font face=sans-serif size=2>".$row['calls_today']."</font></td>
        </tr>";
        break;
    }
  }
  echo "</table></td></tr></table></body>\n</html>";
}

function get_report_time_period($tp,$other)
{
  switch($tp)
  {
    case 0:
      return 'Months, Year to Date';
      break;
    case 1: //month
      return get_month($other)." ".date('Y');
      break;
    case 2:
      return 'Current Week';
      break;
    case 3:
      return 'Today';
      break;
    case 4:
      return 'Years';
      break;
  }
}
?>
