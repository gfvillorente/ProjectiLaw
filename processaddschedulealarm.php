<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 


// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$clusterid = $_POST['clusterId'];
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

$sql="SELECT MAX(scheduleid) AS scheduleid FROM alarm_schedule";
$result=mysql_query($sql, $con);
$row = mysql_fetch_array($result);
$scheduleid = $row['scheduleid'];
if (is_null($scheduleid))
	$scheduleid = 1;
else
	$scheduleid++;
mysql_free_result($result);

//echo "$clusterid, $activate_time, $brightness, $day_of_week <Br>";

$sql="INSERT INTO alarm_schedule (scheduleid,clusterid,activate_time,brightness,day_of_week) VALUES ($scheduleid,$clusterid,'".$activate_time."',$brightness,$day_of_week)";
if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}
else {
	//echo "|| schedule added ";
	echo "$scheduleid";
}
	



/*
$scheduleid = $_POST['scheduleid'];
$clusterid = $_POST['clusterid'];
$start = $_POST['start'];
$brightness = $_POST['brightness'];
$start_time = substr($start, 16, 7);
$start_date = date('Y-m-d', strtotime(substr($start, 4, 11)));
$end_time = '00:00:00'; //make sure date is till the next day!
$end_date = date('Y-m-d', strtotime(substr($start, 4, 11).' +1 day'));
$user = strtolower($_SESSION['user']);


//$sql="INSERT INTO schedule VALUES ($scheduleid, '$start_time', '$end_time', $brightness, '$start_date', '$end_date')";
$sql="INSERT INTO alarm_schedule (clusterid,activate_time,brightness,day_of_week) 
	VALUES ($clusterid,$activate_time,$brightness,$day_of_week)";
if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}
else
	echo "|| schedule added ";


$sql="SELECT userid FROM user_ WHERE username='$user'";
$result=mysql_query($sql, $con);
$row = mysql_fetch_array($result);
$userid = $row['userid'];
mysql_free_result($result);

$sql="INSERT INTO sched_cluster VALUES ($scheduleid, $clusterid, $userid)";
if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}
else
	echo "|| sched_cluster added ";
*/
	
mysql_close($con);
?>