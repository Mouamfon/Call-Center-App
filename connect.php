<?php
// Call Center aPP

$host="localhost";
$user="root";
$pass="";
$db="ccdb";

try {
  $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
  $dbc = new PDO('mysql:host=localhost;dbname=ccdb', $user, $pass, $pdo_options);
} catch (Exception $ex) {
  die("Erreur : ".$ex->GETMESSAGE());
}
/*
if(!mysql_select_db($db))
{
  echo "<font face=arial size=2>Failed to connect to DB.</font>";
  exit;
}

function AltConnect()
{
  $sql_alt=mysql_connect("localhost","root","");
  mysql_select_db("export",$sql_alt);
  return $sql_alt;
}
*/
?>
