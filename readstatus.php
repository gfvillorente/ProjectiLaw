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

$sql="SELECT MAX(scheduleid) AS scheduleid FROM schedule";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$scheduleid = $row['scheduleid'];
if (is_null($scheduleid))
	$scheduleid = 0;
mysql_free_result($result);
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
    	
    	<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		
		<!-- nav-bar with footer-->
		<link href="./css/schedule.css" rel="stylesheet">
	<title>Readings | iLaw</title>
	<style>
		html, body {
        	height: 100%;
			margin: 0px;
			padding: 0px
		}
		#float {
			z-index: 100;
			float: right;
			padding-top: 70px;
			padding-right: 20px;
			width: 25%;
		}
		#content {
			z-index: 100;
			float: left;
			padding-top: 70px;
			padding-left: 10px;
			width: 73%;
		}
		
    </style>
	<script src="//code.jquery.com/jquery-latest.js"></script>
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<script src="./js/jquery.ui.touch-punch.min.js"></script>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="http://code.highcharts.com/modules/exporting.js"></script>
	<script>
		$(function () {
				var showPopover = function () {
					$(this).popover('show');
				}
				, hidePopover = function () {
					$(this).popover('hide');
				};
		$('#messages').popover({
					html: 'true',
					title: '<a href="#">See all messages</a>',
					content: '<table class="table table-hover table-condensed"><tr class="warning"><td><span class="glyphicon glyphicon-signal"></span></td><td><small>Mar 2014 Consumption Report</small></td></tr><tr class="warning"><td><span class="glyphicon glyphicon-wrench"></span></td><td><small>Mar 2014 Maintenance Report</small></td></tr><tr><td><span class="glyphicon glyphicon-stats"></span></td><td><small>Feb 28, 2014 Power Reading Stats</small></td></tr><tr><td><span class="glyphicon glyphicon-sort-by-attributes-alt"></span></td><td><small>Feb 2014 Savings Report</small></td></tr></table>',
					trigger: 'click',
					placement: 'auto'
				})
		$('#notifications').popover({
					html: 'true',
					title: '<a href="#">See all notifications</a>',
					content: '<table class="table table-hover table-condensed"><tr class="warning"><td><span class="glyphicon glyphicon-certificate"></span></td><td><small>New light added</small></td></tr><tr><td><span class="glyphicon glyphicon-warning-sign"></span></td><td><small>Pilferage detected</small></td></tr><tr><td><span class="glyphicon glyphicon-wrench"></span></td><td><small>Repair needed</small></td></tr><tr><td><span class="glyphicon glyphicon-flash"></span></td><td><small>Power surge detected</small></td></tr><tr><td><span class="glyphicon glyphicon-remove"></span></td><td><small>Light can not be reached</small></td></tr><tr><td><span class="glyphicon glyphicon-time"></span></td><td><small>Light not responding</small></td></tr></table>',
					trigger: 'click',
					placement: 'auto'
				})
		$('#messages').on('show.bs.popover', function () {
				  $('#notifications').popover('hide')
				})
		$('#notifications').on('show.bs.popover', function () {
				  $('#messages').popover('hide')
				})
		$('#settings').on('show.bs.dropdown', function () {
				  $('#notifications').popover('hide');
				  $('#messages').popover('hide');
				})
		$('#account').on('show.bs.dropdown', function () {
				  $('#notifications').popover('hide');
				  $('#messages').popover('hide');
				})
		});
	</script>
	<style>
	
		#wrap {
			width: 100%;
			height: 100%;
			margin: 0 auto;
			}
	
	</style>


	</head>
	<body>
<?php
include './header.php';
?>
<div id="float">
		<ul class="list-group list-unstyled">
			<li id="maps" class="dropdown">
			<?php
				$sql="SELECT clusterid, name FROM cluster";
				$result=mysql_query($sql);
				$clustersArray = array();
				$countClusters = 0;
				while($row=mysql_fetch_array($result))
				{
					$clustersArray[$countClusters]['clusterid'] = $row['clusterid'];
					$clustersArray[$countClusters]['name'] = $row['name'];
					$countClusters++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item list-group-item-warning dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-map-marker"></span>
				  Maps
				  <span class="badge pull-right badge-warning"><?php echo $countClusters; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Map Clusters</li>
					<?php
						for($i = 0; $i < $countClusters; $i++) {
							echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./cluster.php?clusterid=".$clustersArray[$i]['clusterid']."\">".$clustersArray[$i]['name']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="./addcluster.php">Add a Cluster</a></li>
			  </ul>
			</li>
			<li id="lights" class="dropdown">
			<?php
				$sql="SELECT bulbid, name FROM bulb";
				$result=mysql_query($sql);
				$bulbsArray = array();
				$countBulbs = 0;
				while($row=mysql_fetch_array($result))
				{
					$bulbsArray[$countBulbs]['bulbid'] = $row['bulbid'];
					$bulbsArray[$countBulbs]['name'] = $row['name'];
					$countBulbs++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-adjust"></span>
				  Lights
				  <span class="badge pull-right"><?php echo $countBulbs; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Light Bulbs</li>
					<?php
						for($i = 0; $i < $countBulbs; $i++) {
							echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./view.php?bulbid=".$bulbsArray[$i]['bulbid']."\">".$bulbsArray[$i]['name']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="./addlight.php">Add a Light</a></li>
			  </ul>
			</li>
			<li id="readings" class="dropdown">
			<?php
				$sql="SELECT bulbid, name FROM bulb WHERE bulbid IN (SELECT DISTINCT bulbid FROM poweranalyzer ORDER BY bulbid)";
				$result=mysql_query($sql);
				$readingsArray = array();
				$countReadings = 0;
				while($row=mysql_fetch_array($result))
				{
					$readingsArray[$countReadings]['bulbid'] = $row['bulbid'];
					$readingsArray[$countReadings]['name'] = $row['name'];
					$countReadings++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-signal"></span>
				  Reports
				  <span class="badge pull-right"><?php echo $countReadings; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Consumption Reports</li>
					<?php
						for($i = 0; $i < $countReadings; $i++) {
							echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./readings.php?bulbid=".$readingsArray[$i]['bulbid']."\">".$readingsArray[$i]['name']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="#">Customize a Report</a></li>
			  </ul>
			</li>
			<li id="schedules" class="dropdown">
			<?php
				$sql="SELECT scheduleid, start_date, end_date, start_time, end_time FROM schedule";
				$result=mysql_query($sql);
				$schedulesArray = array();
				$countSchedules = 0;
				while($row=mysql_fetch_array($result))
				{
					$schedulesArray[$countSchedules]['scheduleid'] = $row['scheduleid'];
					$schedulesArray[$countSchedules]['start_date'] = $row['start_date'];
					$schedulesArray[$countSchedules]['start_time'] = $row['start_time'];
					$schedulesArray[$countSchedules]['end_date'] = $row['end_date'];
					$schedulesArray[$countSchedules]['end_time'] = $row['end_time'];
					$countSchedules++;
				}
				mysql_free_result($result);
			?>
				<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
				  <span class="glyphicon glyphicon-calendar"></span>
				  Schedules
				  <span class="badge pull-right"><?php echo $countSchedules; ?></span>
				</a>
				<ul class="dropdown-menu">
					<li role="presentation" class="dropdown-header">Events</li>
					<?php
						$dateNow = date("Y-m-d");
						for($i = 0; $i < $countSchedules; $i++) {
							if ($schedulesArray[$i]['end_date'] > $dateNow)
								echo "<li role=\"presentation\"><a role=\"menuitem\" tabindex=\"-1\" href=\"./viewschedule.php?scheduleid=".$schedulesArray[$i]['scheduleid']."\">On ".$schedulesArray[$i]['start_date']." to ".$schedulesArray[$i]['end_date']." from ".$schedulesArray[$i]['start_time']." to ".$schedulesArray[$i]['end_time']."</a></li>";
						}
					?>
					<li role="presentation" class="divider"></li>
					<li role="presentation"><a role="menuitem" tabindex="-1" href="./addschedule.php">Schedule an Event</a></li>
			  </ul>
			</li>		
		</ul>
		<div class="container-fluid">
			<p>&nbsp;</p>
			<div class="row">
				<div class="panel panel-warning">
					<div class="panel-heading"><small><strong>Power Readings</strong></small></div>
					<div class="list-group">
						<a href="./readstatus.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item list-group-item-warning">Status</a>
						<a href="./readings.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item">Real RMS Power</a>
						<a href="./readapparentpower.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item">Apparent Power</a>
						<a href="./readreactivepower.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item">Reactive Power</a>
						<a href="./readpowerfactor.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item">Power Factor</a>
						<a href="./readvoltage.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item">RMS Voltage</a>
						<a href="./readcurrent.php?bulbid=<?php echo $_GET['bulbid'];?>" class="list-group-item">RMS Current</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	
<div id="wrapContent">
	<div id="content">
		<div class="container-fluid">
			<div id="wrap">
				<div class="btn-group btn-group-xs pull-right">
					<a class="btn btn-default active" href="./readstatus.php?bulbid=<?php echo $_GET['bulbid'];?>">Real-Time</a>
					<a class="btn btn-default" href="./readstatushour.php?bulbid=<?php echo $_GET['bulbid'];?>">Hour</a>
					<a class="btn btn-default" href="./readstatusday.php?bulbid=<?php echo $_GET['bulbid'];?>">Day</a>
					<a class="btn btn-default" href="./readstatusweek.php?bulbid=<?php echo $_GET['bulbid'];?>">Week</a>
					<a class="btn btn-default" href="./readstatusmonth.php?bulbid=<?php echo $_GET['bulbid'];?>">Month</a>
				</div>
				<div id="chart"></div>
			</div>
		</div>
		<div id="push" class="container"><h1>&nbsp;</h1></div>
	</div>
</div>

<div id="footer" class="footer navbar-fixed-bottom">
      <div class="container-fluid">
        <p class="text-muted">&copy; 2014 Solatronics <small class="pull-right"><a href="#">about</a> &#8226; <a href="#">contact</a> &#8226; <a href="#">help</a></small></p>
      </div>
</div>
<?php 
	$sql="SELECT * FROM (SELECT stat, timestamp FROM poweranalyzer WHERE bulbid=".$_GET['bulbid']." ORDER BY timestamp DESC LIMIT 20)sub ORDER BY timestamp ASC";
	$result=mysql_query($sql);
	$StatReadingArray = array();
	$countStatPowerReadings = 0;
	while($row=mysql_fetch_array($result))
	{
		$StatReadingArray[$countStatPowerReadings]['stat'] = $row['stat'];
		$StatReadingArray[$countStatPowerReadings]['timestamp'] = $row['timestamp'];
		$countStatPowerReadings++;
	}
	mysql_free_result($result);		
?>
<script>
		var dataString = '<?php echo json_encode($StatReadingArray);?>';
		var objStat = eval ("("+dataString+")");
		var bulbid = '<?php echo $_GET['bulbid'];?>';
		
		$(function () {
			$(document).ready(function() {
				Highcharts.setOptions({
					global: {
						useUTC: false
					}
				});
	
				var chart;
				$('#chart').highcharts({
					chart: {
						type: 'spline',
						animation: Highcharts.svg, // don't animate in old IE
						marginRight: 10,
						events: {
							load: function() {
								var series = this.series[0];
								setInterval(function() {
									var time = (new Date()).getTime();
									var value = 0.0;
									var x = time;
									var y = value;
									var feed = $.ajax({
										type: 'GET',
										url: './statusdata.php',
										data: 'bulbid='+bulbid,
										success: function(data){
											var dataString = data.split(' ');
											time = (new Date(dataString[1]+'T'+dataString[2]+'+08:00')).getTime();
											if (dataString[0].localeCompare('InRange') == 0)
												value = 1.0;
											else
												value = 2.0;
								
											x = time;
											y = value;
											series.addPoint([x, y], true, true);
										},
										dataType: 'html'	
									});
								}, 10000);
							}
						}
					},
					title: {
						text: "Load Status for "+"<strong><?php echo $bulbsArray[$_GET['bulbid'] - 1]['name'];?></strong>",
						style: {
							color: '#000000',
							font: '16px "Helvetica Neue", Helvetica, Arial, sans-serif'
						}
					},
					xAxis: {
						type: 'datetime',
						tickPixelInterval: 150
					},
					yAxis: {
						title: {
							text: 'Load within Range',
							style: {
								color: '#000000',
								font: 'bold 14px "Helvetica Neue", Helvetica, Arial, sans-serif'
							}
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
					tooltip: {
						formatter: function() {
								return '<b>'+ this.series.name +'</b><br/>'+
								Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
								Highcharts.numberFormat(this.y, 2);
						}
					},
					legend: {
						enabled: false
					},
					exporting: {
						enabled: true
					},
					series: [{
						name: 'Load Status',
						data:
						(function() {
							var data = [],
							time = (new Date()).getTime(), 
							value = 0.0;
							i=0;
	
							for (i = 0; i < 20; i++) {
								time = (new Date(objStat[i].timestamp.replace(' ', 'T') + '+08:00')).getTime();
								if (objStat[i].stat.localeCompare('InRange') == 0)
									value = 1.0;
								else
									value = 2.0;
								data.push({
									x: time,
									y: value
								});
							}
							return data;
						})(),
						color: '#FF9900'
					}]
				});
			});
	
		});
	</script>
	
</body>
</html>