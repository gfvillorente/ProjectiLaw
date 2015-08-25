<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 
$tbl_name="bulb"; // Table name 

$clusterid;
if(isset($_GET['clusterid'])) {
	$clusterid = $_GET['clusterid'];
}
else {
	echo "Error: No clusterid<Br>";
	return;
}

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$sql="SELECT MAX(scheduleid) AS scheduleid FROM schedule";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$scheduleid = $row['scheduleid'];
if (is_null($scheduleid))
	$scheduleid = 0;
mysql_free_result($result);

//Current Power Consumption
$date = new DateTime();
$RealPowerReadingArray = array();
$interval = new DateInterval('P1D');

$dateString = date_format($date, 'Y-m-d');
$yearString = date_format($date, 'Y');
$monthString = date_format($date, 'm');
$dayString = date_format($date, 'd');	

$MonthlyAveragePower;
for ($i=0; $i<12; $i++) {
	$MonthlyAveragePower[$i] = 0;
}

$sql = "SELECT DISTINCT bulb.bulbid as bulbid
		FROM bulb
		INNER JOIN cluster_bulb
		ON cluster_bulb.bulbid = bulb.bulbid
		WHERE cluster_bulb.clusterid = ".$clusterid;

$result=mysql_query($sql);

$bulbCtr = 0;
$tempBulbCluster = null;
while($row = mysql_fetch_array($result)) {
	$tempBulbCluster[$bulbCtr]['bulbid'] = $row['bulbid'];
	$bulbCtr++;		
}	

//echo json_encode($tempBulbCluster);
if ($tempBulbCluster == null) {
	$temp['dayConsumption'] = 0;
	$temp['monthConsumption'] = 0;
	echo json_encode($temp);
	return;
}

foreach ($tempBulbCluster as $bulbCluster) {
	$sql="SELECT
			Year(timeinterval) as year, 
			Month(timeinterval) - 1 as month, 
			sum(abs(ave_va * ave_pf)) as total_watts
		FROM
			(
			select 
				avg(va) as ave_va, 
				avg(pf) as ave_pf, 
				convert((min(timestamp) div 6000)*6000, datetime) as timeinterval
			from poweranalyzer
			where bulbid = ".$bulbCluster['bulbid']." AND YEAR(timestamp) = ".$yearString."
			group by timestamp div 6000
			) as newdb
		GROUP BY
			Year(timeinterval),
			Month(timeinterval);";

	$result=mysql_query($sql);

	while($row = mysql_fetch_array($result)) {
		$tempMonth = $row['month'];

		if ($tempMonth != NULL) {
			$ctr = $row['month'];
			$MonthlyAveragePower[$ctr] += (float) $row['total_watts'];
		}
	}	

	//echo json_encode($MonthlyAveragePower);
}

$currentMonthConsumption = $MonthlyAveragePower[(int)(date("m")) - 1];

//echo "Current Power Consumption<Br>" . json_encode($currentMonthConsumption) . "<Br><Br>";

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

//echo "Bulbs per Cluster<Br>" . json_encode($bulbPerCluster) . "<Br><Br>";

$totalWattHours = 0;

//Find out number of days till next month
$curMonth = date('n');
$curYear  = date('Y');

if ($curMonth == 12) {
    $firstDayNextMonth = mktime(0, 0, 0, 0, 0, $curYear+1);
}
else {
    $firstDayNextMonth = mktime(0, 0, 0, $curMonth+1, 1);
}

$firstDayCurrentMonth = mktime(0, 0, 0, $curMonth, 1);
$totalDaysCurentMonth = ($firstDayNextMonth - $firstDayCurrentMonth) / (24 * 3600);
//echo "Total Days of the month: $totalDaysCurentMonth <Br><Br>";

$ctr=0;
$schedPerCluster;
//foreach ($clusterAll as $cluster) {
	$sql="SELECT 
			activate_time, brightness, day_of_week 
		  from 
		  	alarm_schedule 
		  where 
		  	clusterid = " . $clusterid .
		  " order by activate_time asc";

	$result=mysql_query($sql);

	$tempSched;
	$tempCtr=0;
	while($row = mysql_fetch_array($result)) {
		$tempSched[$tempCtr]['activate_time'] = $row['activate_time'];
		$tempSched[$tempCtr]['brightness'] = $row['brightness'];
		//$tempSched[$tempCtr]['day_of_week'] = $row['day_of_week'];
		$tempCtr++;
	}

	//echo "Cluster ID: ".$cluster['clusterid'].", Sched: ".json_encode($tempSched)."<Br>";

	$totalClusterWattHours = 0;
	for ($i=0; $i < $tempCtr; $i++) {
		if ($i == $tempCtr - 1) {
			$seconds = strtotime($tempSched[0]['activate_time']) - strtotime($tempSched[$i]['activate_time']);
		} else {
			$seconds = strtotime($tempSched[$i+1]['activate_time']) - strtotime($tempSched[$i]['activate_time']);
		}
		
		$days    = floor($seconds / 86400);
		$hours   = floor(($seconds - ($days * 86400)) / 3600);
		$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
		$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));

		//Temporary Bug Fix in PHP that adds 2 hours to get actual time
		//$hours = $hours + 2;
		if ($hours >= 24) {
			$hours = $hours - 24;
		}

		$timeOn = $hours + ($minutes / 60);
		$brightness = floatval($tempSched[$i]['brightness']);

		if ($brightness >= 10) {
			$wattHours = ($brightness + 15) * $timeOn;
		}
		else {
			$wattHours = 0;
		}
		
		$totalClusterWattHours += $wattHours;
		//echo "Total Time=$timeOn, Watt Hours=$wattHours <Br>";
	}
	//echo "Total Average Predicted Watt-Hours per day: $totalClusterWattHours <Br>";
	//echo "Total Average Predicted Watt-Hours per day for the Cluster: " . ($totalClusterWattHours * $bulbPerCluster[$ctr]['bulbCount']) . "<Br>";
	//echo "Total Average Predicted Watt-Hours for the Cluster for this Month: " . 
	//	($totalClusterWattHours * $bulbPerCluster[$ctr]['bulbCount'] * $daysTilNextMonth) . "<Br>";

	//$totalWattHours += $totalClusterWattHours * $bulbPerCluster[$ctr]['bulbCount'] * $daysTilNextMonth;
	$dailyConsumption = $totalClusterWattHours * $bulbPerCluster[$ctr]['bulbCount'];

	$ctr++;
//}

$totalWattHours += $dailyConsumption * $totalDaysCurentMonth;
//echo "<Br>Total Average Predicted Watt-Hours for the Whole System: $totalWattHours <Br>";
//echo $totalWattHours;
$finalResults['dayConsumption'] = $dailyConsumption;
$finalResults['monthConsumption'] = $totalWattHours;
echo json_encode($finalResults);

?>