<?php
// PHP Call Center
// started May 2002
// http://www.sourceforge.net/projects/phpcc

/*
    Pending 2003-02-28
	- in DisplayCalls i still need to work out a way to display the title of the table. one title will
	  be "## Calls taken by Tech on MM-DD-YYYY", "Search"
	- possibly added tech ids and name to an array so the whole tech row doesn't have to be joined with
	  calls simply to get the tech name
	- for DisplayCalls i'm planning on doing column sorts that are passed to DisplayCalls via 1 url
	  variable.

    Notes
	- prior to 2003-03-13 the dt_start field was a varchar(14) for storing the datetime of the call in
	  a string. i changed it to an int to save some space (10 bytes per call). it's not a big change,
	  but for some reason i'm convinced that it's worth it. these changed affects this file, call.php,

*/

//FUNCTIONS RELATED TO ADDING CALLS TO THE DB

function AddCalltoDB($user_id,$dt_start,$problem,$verified,$status,$notes)
{
  include_once 'connect.php';
  include 'status.php';

  $duration=intval(time()-$dt_start)/60;
  // OLD OP FOR GETTING DURATION OF CALL. IT'S MUCH SIMPLER WITH STRAIGHT INTEGERS. SEE ABOVE.
  //$duration=intval((time()-mktime(substr($dt_start,8,2),substr($dt_start,10,2),substr($dt_start,12,2),substr($dt_start,4,2),substr($dt_start,6,2),substr($dt_start,0,4)))/60);
  

  $notes_final=strip_tags($notes);
  if(strstr($notes_final,"\n"))
  {
    $notes_final=str_replace("\n","<br>",$notes_final);
    $notes_final=str_replace("\r","",$notes_final);
  }
  else
    $notes_final=str_replace("\r","<br>",$notes_final);

  $result = mysql_query("INSERT INTO calls (user_id,dt_start,duration,tech_id,problem,verified,status,notes) VALUES('".$user_id."','".$dt_start."','".$duration."','".$_SESSION['id']."','".$problem."','".$verified."','".$status."','".$notes_final."')");

  // call status.php function here
  if(!$result || (mysql_affected_rows()==0))
  {
    status_('','call_add',false);
  }
  else
  {  
    UpdateCallCount();
    DisplayStatus('','call_add',true);
  }
}


//ADDITIONAL FUNCTIONS FOR OPERATION
function UpdateCallCount()
{
  /* yeah, baby. this is my pride and joy. i loved making this. perhaps the best section of all. */

/*
    FLOWCHART
	- then check if the weeks are different. if not, increment the week field, else, set it to 1.
	- last check if the days are different, if so, increment the day field, else set it to 1.

*/

  include 'misc.inc.php';

  $result=mysql_query("SELECT * FROM techs WHERE id=".$_SESSION['id'].";");
  $row=mysql_fetch_array($result);

  for($i=0;$i<12;$i++) //makes an array of calls per month fields for easy updating.
    $calls_months[$i]=$row[get_month_field($i+1)];

  $prev_date=$row['date'];
  $prev_calls_year=$row['calls_year'];

  $prev_year=date("Y",$prev_date);
  $prev_month=date("m",$prev_date);
  $prev_day=date("d",$prev_date);
  $prev_year_i=$prev_year; //iterator for years. these is iterated in the loop. $prev_year is used as a static reference
  $prev_month_i=$prev_month; //iterator for months. this is iterated in the loop. $prev_month is used as a static reference.

  $ts=time();
  $cur_year=date("Y",$ts);
  $cur_month=date("m",$ts);
  $cur_day=date("d",$ts);
  $calls_year_int=0;
  $calls_year_string="";
  $calls_month=0;
  $calls_week=0;

  //NEED TO PULL THIS FROM DB AND DO A DAY COMPARE BELOW
  $calls_today=1;

//new code as of 2003-03-06

  while($prev_year_i<$cur_year)
  {
    $calls_year_int=0;
    for($i=0;$i<12;$i++)
    {
      $calls_year_int+=$calls_months[$i];
    }
    if($prev_year_i<$cur_year-1)
      for($i=0;$i<12;$i++)
        $calls_months[$i]=0;

    $calls_year_string.=$calls_year_int.',';

    $prev_year_i++;
  }
  $prev_year_i=$prev_year;

  $months_offset=0;

  while($prev_year_i<=$cur_year && $prev_month_i!=$cur_month)
  {
    $months_offset++;

    if($prev_year_i<$cur_year)
    {
      if($prev_month_i==12)
      {
        $prev_year_i++;
        $prev_month_i=1;
      }
      else
        $prev_month_i++;
    }
    else
    {
        $prev_month_i++;
    }
  }

  $prev_year_i=$prev_year;
  $prev_month_i=$prev_month;

  for($i=0;$i<$months_offset;$i++)
  {
    if($prev_year_i<$cur_year-1)
    {
      if($prev_month_i==12)
      {
        $prev_year_i++;
        $prev_month_i=1;
      }
      else
        $prev_month_i++;
    }
    elseif($prev_year_i==$cur_year-1)
    {
      if($prev_month_i==12)
      {
        $prev_year_i++;
        $prev_month_i=1;
      }
      else
        $prev_month_i++;    
      $calls_months[$prev_month_i-1]=0;
    }
    else
    {
      $prev_month_i++;
      $calls_months[$prev_month_i-1]=0;
    }
  }

  
  //still need operations for week checking
  $calls_week=0;

  //make query
  $query="";
  if($prev_year_i!=$cur_year && $prev_month_i!=$cur_month)
  {
    for($i=0;$i<sizeof($calls_months);$i++)
    {
      $query.=get_month_field($i+1).'='.$calls_months[$i].',';
    }
    $query.='calls_year='.$calls_year_string.',';
  }

  $q='UPDATE techs SET '.$query.'calls_week='.$calls_week.',calls_today='.$calls_today.',date='.time().' WHERE id='.$_SESSION['id'];
  mysql_query($q) or die("Error updating call counts.");

//begin old code. prior to 2003-03-06

/*  echo $prev_month_i,'-',$prev_year,' ',$cur_month,'-',$cur_year,'<br>';


  mysql_free_result($result);
  while(1)
  {
    if($prev_year_i<$cur_year)
    {
      if($prev_month_i==12)
      {
        YearAppend();
        if($prev_year_i<$cur_year)
          MonthsClear($cur_month,$prev_year_i);
        $prev_month_i=1;
        $prev_year_i++;
      }
      else
      {
        $prev_month_i++;
      }
    }
    else //years are equal
    {
      $cur_month_field=get_month_field($cur_month);
      $prev_ts=mktime(0,0,0,$prev_month_i,$prev_day,$prev_year_i);
      $cur_ts=mktime(0,0,0,$cur_month,$cur_day,$cur_year);

      if($prev_month_i<$cur_month-1)
      {
        $prev_month_i++;
      }
      else
      {
        $result=mysql_query("SELECT ".$cur_month_field.",calls_week,calls_today FROM techs WHERE id=".$_SESSION['id']) or die("aye");
        $row=mysql_fetch_array($result);
        $calls_week=$row['calls_week'];

        if($cur_ts-$prev_ts<=7*60*60*24) //check if seven days apart
        {
          if(date("w",$cur_ts)-date("w",$prev_ts)>=0) //same week
          {
            $calls_week++;
          }
          else
          {
            $calls_week=1;
          }
        }
        else
          $calls_week=1;


        if($prev_month_i<$cur_month)
        {
          $prev_month_i++;
        }
        else
        {
          echo 'here';
          if($prev_month==$cur_month)
            $calls_month=$row[$cur_month_field]+1;
          else
            $calls_month=1;

          if($prev_year==$cur_year && $prev_month==$cur_month && $prev_day==$cur_day)
            $calls_today=$row['calls_today']+1;
          else
            $calls_today=1;
          echo $calls_today;
          mysql_query("UPDATE techs SET ".$cur_month_field."=".$calls_month.",calls_week=".$calls_week.",calls_today=".$calls_today.",date=".time()." WHERE id=".$_SESSION['id']) or die("aye");
          mysql_free_result($result);
          break;
        }
        mysql_free_result($result);
      }
    }

    if($prev_year_i==$cur_year && $prev_month_i>$cur_month)
      break;
  }*/
}

function YearAppend()
{
  $calls_year_number=0;
  $calls_year='';
  $result=mysql_query("SELECT calls_jan,calls_feb,calls_mar,calls_apr,calls_may,calls_jun,calls_jul,calls_aug,calls_sept,calls_oct,calls_nov,calls_dec,calls_year FROM techs WHERE id='".$_SESSION['id']."'") or die("Failed on YearAppend() Select");
  $row=mysql_fetch_array($result);
  $calls_year_number=$row['calls_jan']+$row['calls_feb']+$row['calls_mar']+$row['calls_apr']+$row['calls_may']+$row['calls_jun']+$row['calls_jul']+$row['calls_aug']+$row['calls_sept']+$row['calls_oct']+$row['calls_nov']+$row['calls_dece'];
  $calls_year=$row['calls_year'].$calls_year_number.',';
  mysql_free_result($result);
  
  mysql_query("UPDATE techs SET calls_year='".$calls_year."' WHERE id=".$_SESSION['id']) or die("Failed on YearAppend() Update");
}

function MonthsClear($month,$year)
{
  $query='';

  if(date("Y")>$year)
  {
    $m=1;
  }
  else
    $m=$month;
  for($i=$m;$i<13;$i++)
  {
    $query.=get_month_field($i).'=0';
    if($i<12)
      $query.=',';
  }
  echo $query,'<br>';
  mysql_query("UPDATE techs SET ".$query." WHERE id=".$_SESSION['id']) or die("Failed on MonthsClear()");
}




















//FUNCTIONS RELATED TO VIEWING CALLS IN THE DB

function DisplayCalls($where,$fields,$fields_headers,$skip,$number,$sorting,$display_type)
{
  //display_type signifies whether it is a Previous Calls list for a Customer (0) or a Call Recap for a Technician (1)


  /* pending as of 2003-02-28: previous/next links (need to pass to calls.php?a=400) */

  include_once "connect.php";
  $i=0;

  if($skip==0)
    $limit=$number;
  else
    $limit=$skip.','.$number;

  $result_test=mysql_query("SELECT count(*) FROM calls WHERE ".$where) or die('Error in query.');

  $result=mysql_query("SELECT * FROM calls LEFT JOIN customers ON calls.user_id=customers.id LEFT JOIN techs ON calls.tech_id=techs.id WHERE ".$where." ORDER BY calls.dt_start DESC LIMIT ".$limit) or die('Error in query.');
  $result_problem=mysql_query("select * from config where type=1 order by value desc");
  $result_verified=mysql_query("select * from config where type=2 order by value desc");
  $result_status=mysql_query("select * from config where type=3 order by value");

  //BUILD VERIFY ARRAY
  $count=0;
  while($row_verified=mysql_fetch_array($result_verified))
  {
    $verified_array[$count][0]=$row_verified['name'];
    $verified_array[$count][1]=$row_verified['value'];
    $count++;
  }
  mysql_free_result($result_verified);

  //BUILD PROBLEM ARRAY
  $count=0;
  while($row_problem=mysql_fetch_array($result_problem))
  {
    $problem_array[$count][0]=$row_problem['name'];
    $problem_array[$count][1]=$row_problem['value'];
    $count++;
  }
  mysql_free_result($result_problem);
  //BUILD STATUS ARRAY
  $count=0;
  while($row_status=mysql_fetch_array($result_status))
  {
    $status_array[$count][0]=$row_status['name'];
    $status_array[$count][1]=$row_status['value'];
    $count++;
  }
  mysql_free_result($result_status);





/*
  if(mysql_num_rows($result)) //needs multiple pages
  {
    if($start>=0 && $num>=1)
    {
      echo "<table cellspacing=0 cellpadding=0 border=0 bordercolor=#c0c0c0 width=90%><tr><td width=33%><font face=arial size=2>";
      if($start>0)
      {
        $start_l=$start-10;
         echo "<a href=display_calls.php?user=".$user."&start=".$start_l."&num=".$num."&o=".$o.">Prev</a>";
      }
      echo "</td><td wdith=34% align=center><font face=sans-serif size=2>&nbsp;";
      
      $start_d=$start+1;
      if($start+10>$numrows)
        $end=$numrows;
      else
        $end=$start+$num;

      echo "$start_d - $end of $numrows</font></td><td width=33% align=right><font face=sans-serif size=2>";

      if($start+$num<$numrows)
      {  
        $start_l=$start+10;
        echo "<a href=display_calls.php?user=".$user."&start=".$start_l."&num=".$num."&o=".$o.">Next</a>";        
      }
      echo "</font></td></tr></table>\n";
      if($o==0)
        $result=mysql_query("SELECT * FROM calls LEFT JOIN customers ON calls.user_id=customers.id WHERE user_id='$user' ORDER BY dt_start DESC LIMIT $start,$num;") or die("Query error.");
      else
        $result=mysql_query("SELECT * FROM calls LEFT JOIN customers ON calls.user_id=customers.id WHERE user_id='$user' ORDER BY dt_start ASC LIMIT $start,$num;") or die("Query error.");
    }
  }
  else //not more than 10, doesn't need multiple pages
  {
    if($numrows>0) //more than 0
    {
      if($o==0)
        $result=mysql_query("SELECT * FROM calls LEFT JOIN customers ON calls.user_id=customers.id WHERE user_id='$user' ORDER BY dt_start DESC LIMIT $start,$num;") or die("Query error.");
      else
        $result=mysql_query("SELECT * FROM calls LEFT JOIN customers ON calls.user_id=customers.id WHERE user_id='$user' ORDER BY dt_start ASC LIMIT $start,$num;") or die("Query error.");
    }
    else //0 found
    {
      $result=mysql_query("SELECT * FROM customers WHERE userid='$user';");
      $row=mysql_fetch_array($result);
      echo "<font face=sans-serif size=2>No previous records found for ".$row['f_name']." ".$row['l_name'].".";
      exit;
    }
  }*/

  if($result)
    $numrows=mysql_num_rows($result);
  else
    $numrows=0;
  if($numrows>0)
  {
    $columns=5+sizeof($fields);
    echo '<table cellspacing=0 cellpadding=2 bgcolor=#234D76 border=1 bordercolor=#c0c0c0>
 <tr valign=top align=center>
  <td colspan=',$columns,' align=center valign=top width=100%><font face=sans-serif size=3>';
    if($display_type==0)
      echo 'Previous Calls';
    else
    {
      $count=mysql_num_rows($result);
      echo $count,' call';
      if($count>1)
        echo 's';
      echo ' taken by '.$_SESSION['name'].' on '.date('Y-m-d');
    }
    /*$result_c=mysql_query("SELECT l_name,f_name FROM customers WHERE id=$user;");
    if(mysql_num_rows($result_c)==1)
    {
      $row_c=mysql_fetch_array($result_c);
      echo $row_c['f_name']." ".$row_c['l_name'];
    }
    else
    {
      echo $user;
    }*/

echo "</font></td>
 </tr><tr valign=top align=center>
<td width=240><font face=sans-serif size=2><b><!--<a href=display_calls.php?user=".$user."&start=".$start."&num=".$num."&o=";
if($o==0)
  echo '1';
else
  echo '0';
echo ">-->Date, Time, Duration<!--</a>--></b></font></td>";

for($j=0;$j<sizeof($fields_headers);$j++)
  echo '<td><font face=sans-serif size=2><b>'.$fields_headers[$j].'</b></font></td>';


echo "<td width=100><font face=sans-serif size=2><b>Problem</b></font></td>\n
<td width=130><font face=sans-serif size=2><b>Verified</b></font></td>\n
<td width=*><font face=sans-serif size=2><b>Notes</b></font></td>\n
<td width=80><font face=sans-serif size=2><b>Status</b></font></td>\n</tr>";
    while($row=mysql_fetch_array($result))
    {
      //BUILD VERIFIED ARRAY
      $verified=$row['verified'];
      $count=0;
      $verified_display=array();
      for($i=0;$i<sizeof($verified_array);$i++)
      {
        if($verified>=$verified_array[$i][1])
        {
          $verified_display[$count]=$verified_array[$i][0];
          $verified-=$verified_array[$i][1];
          $count++;
        }
      }

      //BUILD PROBLEM ARRAY
      $problem=$row['problem'];
      $count=0;
      $problem_display=array();
      for($i=0;$i<sizeof($problem_array);$i++)
      {
        if($problem>=$problem_array[$i][1])
        {
          $problem_display[$count]=$problem_array[$i][0];
          $problem-=$problem_array[$i][1];
          $count++;
        }
      }


      echo '<tr align=left valign=top><td><font face=sans-serif size=2>'.FormatDT($row['dt_start']).' ('.$row['duration'].')</font></td>';


      for($j=0;$j<sizeof($fields);$j++)
        echo '<td><font face=sans-serif size=2>'.$row[$fields[$j]].'</font></td>';

      echo '<td><font face=sans-serif size=2>';
      for($j=sizeof($problem_display)-1;$j>=0;$j--) //display the array in reverse because more than likely it is the way the items appear on the new call form
        echo $problem_display[$j],'<br>';
      echo '</td>

      <td><font face=sans-serif size=2>';

      for($j=sizeof($verified_display)-1;$j>=0;$j--) //display the array in reverse because more than likely it is the way the items appear on the new call form
        echo $verified_display[$j],'<br>';
      echo '&nbsp;</font></td>

      <td><font face=sans-serif size=2 color=white>&nbsp;&nbsp;<b>'.$row['notes'].'</b></font></td>
      <td><font face=sans-serif size=2>';

      for($i=0;$i<sizeof($status_array);$i++)
      {
        if($row['status']==$status_array[$i][1])
        {
          echo $status_array[$i][0];
          break;
        }
      }
      echo '&nbsp;</font></td></tr>';
    }
    echo "</table><br><br>";
  }
  if($result)
    mysql_free_result($result);
}

function FormatDT($datetime)
{
  return date('Y',$datetime).'-'.date('m',$datetime).'-'.date('d',$datetime).'&nbsp;&nbsp;'.date('h',$datetime).':'.date('i',$datetime).':'.date('s',$datetime);

  //OLD OP THAT DEALT WITH DATES AS A STRING OF 14 CHARS (YYYYMMDDHHMMSS)
  //return substr($datetime,0,4).'-'.substr($datetime,4,2).'-'.substr($datetime,6,2).'&nbsp;&nbsp;'.substr($datetime,8,2).':'.substr($datetime,10,2).':'.substr($datetime,12,2);

}
?>
