<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$scheduleid = $_GET['scheduleid'];
$sdate = $_GET['sdate'];
$edate = $_GET['edate'];
$stime = $_GET['stime'];
$etime = $_GET['etime'];
$level = $_GET['level'];

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

//update bulb set currbrightness=0 where bulbid=17;

//$sql="UPDATE bulb SET state='$state', currbrightness=$level, mode='$mode' WHERE bulbid=$bulbid";
$sql="UPDATE schedule SET start_time='$stime', end_time='$etime', brightness=$level, start_date='$sdate', end_date='$edate' WHERE scheduleid=$scheduleid";

if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}
mysql_close($con);
?>