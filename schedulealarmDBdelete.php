<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

$scheduleid = $_POST['scheduleId'];

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$sql="DELETE FROM alarm_schedule WHERE scheduleid=$scheduleid";
echo "Deleted $scheduleid Initiated!";

if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}	

mysql_close($con);
?>