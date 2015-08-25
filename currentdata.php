<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$bulbid = $_GET['bulbid'];

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

//update bulb set currbrightness=0 where bulbid=17;

$sql="SELECT ampere, timestamp FROM poweranalyzer WHERE bulbid=$bulbid ORDER BY timestamp DESC LIMIT 1";

$result=mysql_query($sql, $con);
$row=mysql_fetch_array($result);
	$latestCurrent['ampere'] = $row['ampere'];
	$latestCurrent['timestamp'] = $row['timestamp'];
mysql_free_result($result);
mysql_close($con);
echo $latestCurrent['ampere'].' '.$latestCurrent['timestamp'];
?>