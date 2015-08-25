<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 
$tbl_name="bulb"; // Table name 

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

if(isset($_GET['change'])) {
	$percentChange = $_GET['change'];

	//Find number of bulb in a cluster
	$sql = "SELECT clusterid FROM cluster;";

	$result=mysql_query($sql);

	$clusterCount = 0;
	$clusterAll;
	while($row = mysql_fetch_array($result)) {
		$clusterAll[$clusterCount]['clusterid'] = $row['clusterid'];
		$clusterCount++;
	}	

	$ctr=0;
	$bulbPerCluster;
	foreach ($clusterAll as $cluster) {
		$sql="SELECT 
				COUNT(distinct bulbid) AS bulbCount 
			  FROM 
			  	cluster_bulb 
			  WHERE 
			  	clusterid=" . $cluster['clusterid'];

		$result=mysql_query($sql);

		while($row = mysql_fetch_array($result)) {
			$temp = $row['bulbCount'];
			$bulbPerCluster[$ctr]['clusterid'] = $cluster['clusterid'];
			$bulbPerCluster[$ctr]['bulbCount'] = (int) $temp;
			$ctr++;
		}	
	}

	foreach ($bulbPerCluster as $bulbCluster) {
		if ($bulbCluster['bulbCount'] > 0) {
			$sql = "UPDATE 
						alarm_schedule
					SET 
						brightness=brightness*$percentChange
					WHERE 
						clusterid=".$bulbCluster['clusterid']."
					and
						(brightness*$percentChange) < 100 
					and 
						(brightness*$percentChange) > 10;";

			$result=mysql_query($sql);
			//mysql_free_result($result);
		}
	}
}
else {
	echo "ERROR: No percentChange input<Br/>";
}


?>