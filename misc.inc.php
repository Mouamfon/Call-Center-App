<?php
// PHP Call Center
// started May 2002
// http://www.sourceforge.net/projects/phpcc

function get_month($month)
{
 switch($month)
  {
    case 1:
      return 'January';
      break;  
    case 2:
      return 'February';
      break;
    case 3:  
      return 'March';
      break;  
    case 4:  
      return 'April';
      break;  
    case 5:  
      return 'May';
      break;  
    case 6:  
      return 'June';
      break;  
    case 7:
      return 'July';
      break;  
    case 8:
      return 'August';
      break;
    case 9:
      return 'September';
      break;
    case 10:  
      return 'October';
      break;  
    case 11:
      return 'November';
      break;
    case 12:
      return 'December';
      break;
  }
}

function get_month_abbrev($month)
{
  switch($month)
  {
    case '1':
      return 'Jan.';
      break;
    case '2':
      return 'Feb.';
      break;
    case '3':
      return 'Mar.';
      break;
    case '4':
      return 'Apr.';
      break;
    case '5':
      return 'May.';
      break;
    case '6':
      return 'Jun.';
      break;
    case '7':
      return 'Jul.';
      break;
    case '8':
      return 'Aug.';
      break;
    case '9':
      return 'Sept.';
      break;
    case '10':
      return 'Oct.';
      break;
    case '11':
      return 'Nov.';
      break;
    case '12':
      return 'Dec.';
      break;
  }
  return $month_text;
}

function get_month_field($month)
{
 switch($month)
  {
    case 1:
      return 'calls_jan';
      break;  
    case 2:
      return 'calls_feb';
      break;
    case 3:  
      return 'calls_mar';
      break;  
    case 4:  
      return 'calls_apr';
      break;  
    case 5:  
      return 'calls_may';
      break;  
    case 6:  
      return 'calls_jun';
      break;  
    case 7:
      return 'calls_jul';
      break;  
    case 8:
      return 'calls_aug';
      break;
    case 9:
      return 'calls_sept';
      break;
    case 10:  
      return 'calls_oct';
      break;  
    case 11:
      return 'calls_nov';
      break;
    case 12:
      return 'calls_dec';
      break;
  }
}



function SimpleForm($title,$width)
{
?>
  <table cellspacing=0 cellpadding=0 style="border:1px solid #c0c0c0;"<?php if($width>0) echo " width=$width";?> bgcolor=#234D76> 
   <tr> 
    <td align=center style="border-bottom:1px solid #c0c0c0;"><font face=sans-serif size=3 color=white><b><?php echo $title;?></b></font></td> 
   </tr><tr>
    <td height=4></td>
   </tr><tr>
    <td align=center>
<?php
}

function SimpleFormClose()
{
?>
    </td>
   </tr><tr>
    <td height=4></td>
   </tr>
  </table>
<?php
}
?>
