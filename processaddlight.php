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

$name = trim($_POST['nameBulb']);
$name = stripslashes($name);
$name = mysql_real_escape_string($name);

$sql="SELECT MAX(bulbid) AS bulbid, name FROM bulb";

$result=mysql_query($sql, $con);
$row = mysql_fetch_array($result);
if ($name == $row['name'])
{
	mysql_free_result($result);
	mysql_close($con);
	header("location:./addlightfailed.php");
}
$bulbid = $row['bulbid'];
if (is_null($bulbid))
	$bulbid = 1;
else
	$bulbid++;

mysql_free_result($result);

$ipaddress = $_POST['ipAddressBulb'];
$streetadd = trim($_POST['addressBulb']);
$streetadd = stripslashes($streetadd);
$streetadd = mysql_real_escape_string($streetadd);
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$state = "cnbr";
$mode = "control";

$sql="INSERT INTO bulb VALUES ($bulbid,'$ipaddress','$streetadd','$latitude','$longitude','$state',0,'$mode','$name')";

if (!mysql_query($sql, $con)){
	echo mysql_error($con);
}
//else
	//echo "|| bulb inserted ";

if ($_POST['optionsRadios'] == "existing") {
	$clusterid = $_POST['existingClusters'];
	
	$sql="INSERT INTO cluster_bulb VALUES ($bulbid,$clusterid)";
	if (!mysql_query($sql, $con)){
		echo mysql_error($con);
	}
	//else
		//echo "|| existing cluster used ";
}

else if ($_POST['optionsRadios'] == "new") {
	$name = trim($_POST['newCluster']);
	$name = stripslashes($name);
	$name = mysql_real_escape_string($name);
	
	$sql="SELECT MAX(clusterid) AS clusterid, name FROM cluster";
	$result=mysql_query($sql, $con);
	$row = mysql_fetch_array($result);
	if ($name == $row['name'])
	{	
		mysql_free_result($result);
		mysql_close($con);
		header("location:./addlightfailed.php");
	}
	$clusterid = $row['clusterid'];
	if (is_null($clusterid))
		$clusterid = 1;
	else
		$clusterid++;
	mysql_free_result($result);

	$sql="INSERT INTO cluster VALUES ($clusterid,'$name')";
	if (!mysql_query($sql, $con)){
		echo mysql_error($con);
	}
	//else
		//echo "|| cluster created ";

	$sql="INSERT INTO cluster_bulb VALUES ($bulbid,$clusterid)";
	if (!mysql_query($sql, $con)){
		echo mysql_error($con);
	}
	//else
		//echo "|| new cluster used ";

}
mysql_close($con);
$location = "location:./view.php?bulbid=".$bulbid;
//echo $location;
header($location);
?>