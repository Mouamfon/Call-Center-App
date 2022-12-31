<?php
/*
Call Center App v0.2

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/


include 'header.php';

if(!isset($_POST['login']) && isset($_SESSION['id']))
{
  ShowMenu();
}

function ShowMenu()
{ 
echo '<br><table cellspacing=0 cellpadding=0 width=470 style=\'border:1px solid #c0c0c0\'> 
 <tr> 
  <td> 
   <table cellspacing=0 cellpadding=0 border=0 bgcolor=#234D76 width=100%> 
    <tr> 
     <td colspan=2 align=center><font face=sans-serif size=3 color=white><b>MENU</b></font><br><br></td> 
    </tr><tr align=center valign=top> 
     <td width=50%><font face=sans-serif size=3 color=white><a href=call.php?a=100>New Call (Existing Customers)</a><br><font size=2>Enter information for a new tech call.</font></td> 
     <td width=50%><font face=sans-serif size=3 color=white><a href=search.php?a=100>Search Calls</a><br><font size=2>Search previous tech calls.</font></td> 
    </tr><tr> 
     <td colspan=2>&nbsp;</td> 
    </tr><tr align=center valign=top> 
     <td><font face=sans-serif size=3 color=white><a href=customer.php?a=100>Add Customer</a><br><font size=2>Add a customer in order to<br>keep a tech call log for them.</font></td> 
     <td><font face=sans-serif size=3 color=white><a href=customer.php?a=200>Edit Customer</a><br><font size=2>Edit customer info.</font></td> 
    </tr><tr> 
     <td colspan=2><br></td> 
    </tr><tr> 
     <td colspan=2><table cellspacing=0 cellpadding=2 border=0 width=100%><tr><td width=50%><font face=sans-serif size=2 color=white>Logged in as ',$_SESSION['name'],'</font></td> 
     <td align=right><font face=sans-serif size=2 color=white><a href=index.php?logout=true>logout</a></font></td></tr></table></td> 
    </tr> 
   </table> 
  </td> 
 </tr> 
</table>';
echo '</body></html>';

}
?>
