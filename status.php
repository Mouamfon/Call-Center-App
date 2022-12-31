<?php
//PHP Call Center
//Started May 2002
//http://www.sourceforge.net/projects/phpcc

//status.php
/* used to display the status of the previous action performed, be it a Customer Add, Edit, New Call */

/*
    Pending 2003-02-28
	- there's still some left to do here: call added status (success/failed), as well as changes
	  to customer add and edit

*/

function DisplayStatus($userid,$type,$ret)
{
?>
  <table cellspacing=0 cellpadding=2 border=0 style='border:1px solid #c0c0c0;' bgcolor=#234d76 width=250>
   <tr>
    <td align=center style='border-bottom:1px solid #c0c0c0;'><font face=sans-serif size=3><b>STATUS</b></font></td>
   </tr><tr>
    <td align=center>
     <table cellspacing=0 cellpadding=2 width=90%>
      <tr>
       <td width=100><font face=sans-serif size=2><b>Last Action:</b></font></td>
       <td><font face=sans-serif size=2><?php echo GetStatusActionText($type);?></font></td>
      </tr><tr>
       <td><font face=sans-serif size=2><b>Status:</b></font></td>
       <td><font face=sans-serif size=2><?php echo GetStatusResultText($type,$ret);?></font></td>
      </tr><tr>
       <td colspan=2 height=10 style='border-bottom:1px solid #c0c0c0;'>&nbsp;</td>
      </tr><tr>
       <td colspan=2><font face=sans-serif size=2><?php
  if($type=='cust_add' || $type=='cust_edit' || $type=='call_add')
  {
    echo "<a href=call.php?a=110&userid=$userid>New Call</a>&nbsp;&nbsp;<a href=customer.php?a=220&type=0&userid=$userid>Edit</a>";
  }
  else
  {
    echo "<a href=a_techs.php?a=100>New Tech</a>&nbsp;&nbsp;<a href=a_techs.php?a=110>Edit Techs</a>";
  }
?></font></td>
      </tr><tr>
       <td colspan=2 height=4></td>
      </tr>
     </table>
    </td>
   </tr>
  </table>
<?php
}

function GetStatusActionText($type)
{
  switch($type)
  {
    case 'cust_add': //Customer Added
      return "Customer Add";
      break;
    case 'cust_edit': //Customer Edit
      return "Customer Edit";
      break;
    case 'call_add': //New Call
      return "New Call Add";
      break;

    case 'tech_add':
      return "Add Tech";
      break;
    case 'tech_edit':
      return "Edit Tech Options";
      break;
  }
}
function GetStatusResultText($type,$ret)
{
  switch($type)
  {
    case 'cust_add': //Customer Added
      if($ret)
        return "Succeeded";
      break;
    case 'cust_edit': //Customer Edit
      if($ret)
        return "Succeeded";
      else
        return "Failed";
      break;
    case 'call_add': //New Call
      if($ret)
        return "Succeeded";
      else
        return "Failed";
      break;

    case 'tech_add': //Add Tech
      if($ret)
        return "Succeeded";
      else
        return "Failed";
      break;
    case 'tech_edit': //Edit Tech
      if($ret)
        return "Succeeded";
      else
        return "Failed";
      break;
  }
}
?>
