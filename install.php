<?php
//Call Center App
//Install Script

include 'connect.php';

echo "<font face=arial size=2><font size=4>Installing to Database: </font><font size=3>".$db."</font><br>";
 
echo "<br><font size=3>Tables</font><br>";
$result=mysql_query(
  "CREATE TABLE customers (
    id bigint(20) unsigned auto_increment,
    userid char(20),
    l_name char(20),
    f_name char(20),
    phone char(10),
    address char(40),
    city char(30),
    state char(3),
    zip char(9),
    system_speed tinyint(3) unsigned not null default '0',
    system_memory tinyint(3) unsigned not null default '0',
    system_os tinyint(3) unsigned not null default'0',
    system_connection tinyint(3) unsigned not null default '0',
    primary key (id)
  )"
);
echo "<b>customers:</b> "; 
if(!$result) 
{ 
  echo "creation failed.<br>"; 
  return; 
} 
else 
{ 
  echo "created successfully.<br>"; 
} 
 
$result = mysql_query(
  "CREATE TABLE techs (
    id smallint(5) not null auto_increment,
    pin char(10),
    name char(20),
    access_level tinyint(1) not null default '0',
    date int(11) not null default '0',
    calls_today tinyint(3) not null default '0',
    calls_week tinyint(3) not null default '0',
    calls_jan tinyint(3) not null default '0',
    calls_feb tinyint(3) not null default '0',
    calls_mar tinyint(3) not null default '0',
    calls_apr tinyint(3) not null default '0',
    calls_may tinyint(3) not null default '0',
    calls_jun tinyint(3) not null default '0',
    calls_jul tinyint(3) not null default '0',
    calls_aug tinyint(3) not null default '0',
    calls_sept tinyint(3) not null default '0',
    calls_oct tinyint(3) not null default '0',
    calls_nov tinyint(3) not null default '0',
    calls_dec tinyint(3) not null default '0',
    primary key (id)
  )"
);
echo "<b>techs:</b> ";
if(!$result) 
{ 
  echo "creation failed.<br>"; 
  return; 
} 
else 
{ 
  echo "created successfully.<br>"; 
} 

//alandrums.catalyticonline.net
//alandrums@yahoo.com

$result = mysql_query(
  "CREATE TABLE calls (
    user_id int(11),
    dt_start int(11) not null default '0',
    duration smallint(5) not null default '0',
    tech_id smallint(5) not null default '0',
    problem int(11) not null default '0',
    verified int(11) not null default '0',
    status tinyint(3) not null default'0',
    notes text
  )"
);
echo "<b>calls:</b> ";
if(!$result)
{
  echo "creation failed.<br>";
  return;
}
else
{
  echo "created successfully.<br>";
}

$result = mysql_query(
  "CREATE TABLE config (
    type tinyint(3),
    name char(40),
    value int(11),
    display_order tinyint(3),
    other tinyint(3)
  )"
);
echo "<b>config:</b> ";
if(!$result)
{
  echo "creation failed.<br>";
  return;
}
else
{
  echo "created successfully.<br>";
}


echo "<br><font size=3>Other Necessary Options</font><br>";

mysql_query("insert into techs (pin,name,access_level) values('tech','Default','3')");

mysql_query("insert into config values (1,'Connection',1,1,0)");
mysql_query("insert into config values (1,'Login',2,2,0)");
mysql_query("insert into config values (1,'Dialup',4,3,0)");
mysql_query("insert into config values (1,'Email',8,4,0)");
mysql_query("insert into config values (1,'Information',16,5,0)");
mysql_query("insert into config values (1,'Miscellaneous',32,6,0)");

mysql_query("insert into config values('2','Dialup','1','1','0')");
mysql_query("insert into config values('2','Internet','2','2','0')");
mysql_query("insert into config values('2','Network','4','3','0')");
mysql_query("insert into config values('2','Modem','8','4','0')");
mysql_query("insert into config values('2','Username/Password','16','5','0')");
mysql_query("insert into config values('2','Email','32','6','0')");
mysql_query("insert into config values('2','Misc','64','7','0')");
mysql_query("insert into config values('2','Modem has sync','128','1','3')");
mysql_query("insert into config values('2','Physical connections','256','2','3')");
mysql_query("insert into config values('2','No static on phones','512','3','3')");
mysql_query("insert into config values('2','Unplugged other devices','1024','4','3')");
mysql_query("insert into config values('2','TCP/IP installation','2048','5','3')");
mysql_query("insert into config values('2','Ping to localhost','4096','6','3')");
mysql_query("insert into config values('2','No sync at demarc','8192','7','3')");

mysql_query("insert into config values('3','Referred to Supervisor','1','1','1')");
mysql_query("insert into config values('3','Referred to Technician','2','2','1')");
mysql_query("insert into config values('3','Referred to Tech Shop','3','3','1')");
mysql_query("insert into config values('3','Referred to Billing','4','4','1')");
mysql_query("insert into config values('3','Solved','5','5','1')");
mysql_query("insert into config values('3','Not solved','6','6','1')");
mysql_query("insert into config values('3','Cust. will call back if not solved','7','7','1')");
mysql_query("insert into config values('3','Changed userid','10','8','0')");

mysql_query("insert into config (type,name,value,display_order,other) values('4','- 400 Mhz','1','1','0')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','400 - 800','2','2','0')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','800 +','3','3','0')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','32','1','1','1')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','64','2','2','1')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','128','3','3','1')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','256','4','4','1')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Win 95','1','1','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Win 98','2','2','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Win NT','3','3','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Win 2000','4','4','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Win ME','5','5','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Win XP','6','6','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Mac OSX','7','7','2')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','Dial-up','1','1','3')");
mysql_query("insert into config (type,name,value,display_order,other) values('4','ISDN','2','2','3')");
$result=mysql_query("insert into config (type,name,value,display_order,other) values('4','DSL','3','3','3')");


if(!$result)
{
  echo "Failed.<br>".mysql_error();
  return;
}
else
{
  echo "Added successfully.<br><br>---------------------------------<br><br>
  All operations should be finished now.<br><br>
  Go to <a href=index.php>index.php</a> and log in with 'tech' to begin.";
}
?>

