<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$bulbid = $_GET['bulbid'];
$state = $_GET['state'];
$level = $_GET['level'];
$mode = $_GET['mode'];

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

//update bulb set currbrightness=0 where bulbid=17;

$sql="UPDATE bulb SET state='$state', currbrightness=$level, mode='$mode' WHERE bulbid=$bulbid";

if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}
mysql_close($con);
?>