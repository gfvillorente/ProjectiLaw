<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

//if ((!isset($_POST['cluster'])) || (!isset($_POST[''])))
//	header("location:./");
//if ((!isset($_POST['listCluster'])) || (!isset($_POST['nameCluster'])))
//	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 


// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

//$jsonCluster = $_POST['cluster'];
//$name = $_POST['name'];
$jsonCluster = $_POST['listCluster'];
//echo $jsonCluster;
$name = $_POST['nameCluster'];
$cluster = json_decode($jsonCluster, true);

$sql="SELECT MAX(clusterid) AS clusterid, name FROM cluster";
$result=mysql_query($sql, $con);
$row = mysql_fetch_array($result);
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
	
foreach($cluster[$name] as $p){
	$bulbid = $p["bulbid"];
	$sql="INSERT INTO cluster_bulb VALUES ($bulbid,$clusterid)";
	if (!mysql_query($sql, $con))
		echo mysql_error($con);

	//else
		//echo "|| new cluster used ";

}

mysql_close($con);
$location = "location:./cluster.php?clusterid=".$clusterid;
//echo $location;
header($location);
?>