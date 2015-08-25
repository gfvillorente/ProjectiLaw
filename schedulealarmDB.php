<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

$scheduleid = $_POST['scheduleId'];
$activate_time = $_POST['activateTime'];
$brightness = $_POST['brightness'];
$dowstring = $_POST['dayOfWeek'];

$daysofweek = array("ALL","MON","TUE","WED","THU","FRI","SAT","SUN");

$dayctr = 0;
$day_of_week = 100;
foreach ($daysofweek as $day) {
	if ($day == $dowstring) {
		$day_of_week = $dayctr;
		break;
	}

	$dayctr++;
}

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

if(isset($_POST['update'])) {
	$sql="UPDATE alarm_schedule SET activate_time='$activate_time', brightness=$brightness, day_of_week=$day_of_week WHERE scheduleid=$scheduleid";

	if (!mysql_query($sql, $con)){
		echo mysql_error($con);
	}
}
if(isset($_POST['delete'])) {
	$sql="DELETE FROM alarm_schedule WHERE scheduleid=$scheduleid";
	echo "Deleted $scheduleid Initiated!";

	if (!mysql_query($sql, $con)){
		echo mysql_error($con);
	}	
}

mysql_close($con);
?>