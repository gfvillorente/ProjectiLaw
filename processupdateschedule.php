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

$scheduleid = $_POST['scheduleid'];
$start = $_POST['start'];
$end = $_POST['end'];
$allDay = $_POST['allDay'];
if(isset($allDay)){
	if(is_null($end) && ($allDay=='true')){
		$start_time = substr($start, 16, 7);
		$start_date = date('Y-m-d', strtotime(substr($start, 4, 11)));
		$end_time = '00:00:00'; //make sure date is till the next day!
		$end_date = date('Y-m-d', strtotime(substr($start, 4, 11).' +1 day'));

		$sql="UPDATE schedule SET start_time='$start_time', end_time='$end_time', start_date='$start_date', end_date='$end_date' WHERE scheduleid=$scheduleid";
		if (!mysql_query($sql, $con)){
			echo mysql_error($con);
		}
		else
			echo "|| schedule edited ";
	}
	else if(is_null($end) && ($allDay=='false')){
		$start_time = substr($start, 16, 7);
		$start_date = date('Y-m-d', strtotime(substr($start, 4, 11)));
		$end_time = '23:59:59'; //make sure date is just right before the next day!
		$end_date = date('Y-m-d', strtotime(substr($start, 4, 11)));

		$sql="UPDATE schedule SET start_time='$start_time', end_time='$end_time', start_date='$start_date', end_date='$end_date' WHERE scheduleid=$scheduleid";
		if (!mysql_query($sql, $con)){
			echo mysql_error($con);
		}
		else
			echo "|| schedule edited ";
	}
	if(!is_null($end) && ($allDay=='true')){
		$start_time = substr($start, 16, 7);
		$start_date = date('Y-m-d', strtotime(substr($start, 4, 11)));
		$end_time = '00:00:00'; //make sure date is till the next day!
		$end_date = date('Y-m-d', strtotime(substr($end, 4, 11).' +1 day'));

		$sql="UPDATE schedule SET start_time='$start_time', end_time='$end_time', start_date='$start_date', end_date='$end_date' WHERE scheduleid=$scheduleid";
		if (!mysql_query($sql, $con)){
			echo mysql_error($con);
		}
		else
			echo "|| schedule edited ";
	}
	else if(!is_null($end) && ($allDay=='false')){
		$start_time = substr($start, 16, 7);
		$start_date = date('Y-m-d', strtotime(substr($start, 4, 11)));
		$end_time = substr($end, 16, 7);
		$end_date = date('Y-m-d', strtotime(substr($end, 4, 11)));

		$sql="UPDATE schedule SET start_time='$start_time', end_time='$end_time', start_date='$start_date', end_date='$end_date' WHERE scheduleid=$scheduleid";
		if (!mysql_query($sql, $con)){
			echo mysql_error($con);
		}
		else
			echo "|| schedule edited ";
	}


}
else{ //update start and end all!
	$start_time = substr($start, 16, 7);
	$start_date = date('Y-m-d', strtotime(substr($start, 4, 11)));
	$end_time = substr($end, 16, 7);
	$end_date = date('Y-m-d', strtotime(substr($end, 4, 11)));

	$sql="UPDATE schedule SET start_time='$start_time', end_time='$end_time', start_date='$start_date', end_date='$end_date' WHERE scheduleid=$scheduleid";
	if (!mysql_query($sql, $con)){
		echo mysql_error($con);
	}
	else
		echo "|| schedule edited ";

}

mysql_close($con);
?>