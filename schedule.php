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

$sql="SELECT bulbid, streetadd, latitude, longitude, state, name FROM bulb WHERE bulbid IN (SELECT bulbid FROM cluster_bulb WHERE clusterid=".$_GET['clusterid'].")";
$result=mysql_query($sql);
$count = 0;
$markersArray = array();
while($row=mysql_fetch_array($result))
{
	$markersArray[$count]['bulbid'] = $row['bulbid'];
	$markersArray[$count]['streetadd'] = $row['streetadd'];
	$markersArray[$count]['latitude'] = $row['latitude'];
	$markersArray[$count]['longitude'] = $row['longitude'];
	$markersArray[$count]['state'] = $row['state'];
	$markersArray[$count]['name'] = $row['name'];
	$count++;
}
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
		<link href="./css/home.css" rel="stylesheet">	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
	<script>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see a blank space instead of the map, this
// is probably because you have denied permission for location sharing.

var map;

function initialize() {
	var mapOptions = {
		zoom: 17,
		styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]}]
	};
	var map = new google.maps.Map(document.getElementById('map-canvas'),
	  mapOptions);
	var infoWindow = new google.maps.InfoWindow();
	var bounds = new google.maps.LatLngBounds();
	var markersArray = <?php echo json_encode($markersArray); ?>;
	for (var i = 0; i < <?php echo $count; ?>; i++){
		if (markersArray[i]["state"] == "on")
			var iconColor = 'http://maps.google.com/mapfiles/ms/icons/orange.png';
		else if (markersArray[i]["state"] == "off")
			var iconColor = 'http://maps.google.com/mapfiles/ms/icons/orange-dot.png';
		else
			var iconColor = 'http://maps.google.com/mapfiles/ms/icons/grey.png';
			
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(markersArray[i]["latitude"], markersArray[i]["longitude"]),
			map: map,
			icon: iconColor,
			title: markersArray[i]["streetadd"]
		});
		
		
		google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function(){
					infoWindow.setContent('<a href="' + './view.php?bulbid=' + markersArray[i]["bulbid"] + '">' + markersArray[i]["name"] + '</a>');
					infoWindow.open(map, marker);
				}
		})(marker, i));
		
		bounds.extend(marker.position);
		 
	}
	map.fitBounds(bounds);
}
google.maps.event.addDomListener(window, 'load', initialize);

    </script>
	
		<script src="//code.jquery.com/jquery-latest.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
		<script src="./js/jquery.ui.touch-punch.min.js"></script>
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
		<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
		<script type="text/javascript" src="./js/jquery.timepicker.js"></script>
  		<link rel="stylesheet" type="text/css" href="./css/jquery.timepicker.css" />
	<title>Schedule | iLaw</title>
	<style>
		html, body, #map-canvas {
        	height: 100%;
			margin: 0px;
			padding: 0px
		}
		#map-canvas {
			height: 100%;
			position: absolute; 
			top: 0; 
			bottom: -200px; 
			left: 0; 
			right: 0; 
			z-index: 0;
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
			color: white;
		}
		#slider-range-min {
			margin-top: 17px;
		}
		#slider-range-min .ui-slider-range { background: #FF9900; }
  		#slider-range-min .ui-slider-handle { border-color: #FF9900; }
    </style>
	</head>
	<body>
<?php
include './header.php';
?>
<div id="map-canvas"></div>
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
			<a href="#" class="list-group-item dropdown-toggle" data-toggle="dropdown">
			  <span class="glyphicon glyphicon-map-marker"></span>
			  Maps
			  <span class="badge pull-right"><?php echo $countClusters; ?></span>
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
			<a href="#" class="list-group-item list-group-item-warning dropdown-toggle" data-toggle="dropdown">
			  <span class="glyphicon glyphicon-calendar"></span>
			  Schedules
			  <span class="badge pull-right badge-warning"><?php echo $countSchedules; ?></span>
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
</div>
<div id="content">
	<div class="container-fluid">
		<form class="form-horizontal">
			<div class="form-group">
				<label for="nameCluster" class="control-label col-sm-offset-1 col-sm-2">Name</label>
				<div class="col-sm-9">
					<?php
						$sql="SELECT name FROM cluster WHERE clusterid=".$_GET['clusterid'];
						$result=mysql_query($sql);
						$row=mysql_fetch_array($result);

							$cluster['name'] = $row['name'];
						
						mysql_free_result($result);
					?>
					<input type="text" class="form-control" id="nameCluster" name="nameCluster" value ="<?php echo $cluster['name'];?>" disabled>
				</div>
			</div>
			<div class="form-group">
				<?php
					$sql="SELECT start_date, end_date, start_time, end_time, brightness FROM schedule WHERE scheduleid=".$_GET['scheduleid'];
					$result=mysql_query($sql);
					$row=mysql_fetch_array($result);

						$schedule['start_date'] = $row['start_date'];
						$schedule['end_date'] = $row['end_date'];
						$schedule['start_time'] = $row['start_time'];
						$schedule['end_time'] = $row['end_time'];
						$schedule['brightness'] = $row['brightness'];

					mysql_free_result($result);
				?>
				<label for="startDate" class="control-label col-sm-offset-1 col-sm-2">Start Date</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" id="startDate" name="startDate" value ="<?php echo $schedule['start_date'];?>" disabled>
				</div>
				<label for="endDate" class="control-label col-sm-offset-1 col-sm-2">End Date</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" id="endDate" name="endDate" value ="<?php echo $schedule['end_date'];?>" disabled>
				</div>
				<script>
					$(function(){
						$("#endDate").datepicker({ dateFormat: 'yy-mm-dd' });
						$("#startDate").datepicker({ dateFormat: 'yy-mm-dd' }).bind("change",function(){
							var minValue = $(this).val();
							minValue = $.datepicker.parseDate("yy-mm-dd", minValue);
							minValue.setDate(minValue.getDate()+1);
							$("#endDate").datepicker( "option", "minDate", minValue );
						})
					});
				</script>
			</div>
			<div class="form-group">
				<label for="startTime" class="control-label col-sm-offset-1 col-sm-2">Start Time</label>
				<div class="col-sm-3">
					<input id="startTime" name="startTime" type="text" class="time form-control" value ="<?php echo $schedule['start_time'];?>" disabled>
					<script>
						$(function(){
							$('#startTime').timepicker({ 'timeFormat': 'H:i:s' });
						});
					</script>
				</div>
				<label for="endTime" class="control-label col-sm-offset-1 col-sm-2">End Date</label>
				<div class="col-sm-3">
					<input id="endTime" name="endTime" type="text" class="time form-control" value ="<?php echo $schedule['end_time'];?>" disabled>
					<script>
						$(function(){
							$('#endTime').timepicker({ 'timeFormat': 'H:i:s' });
						});
					</script>
				</div>
			</div>
			<div class="form-group" id="brightnessSlider">
				<label for="brightness" class="col-sm-offset-1 col-sm-2 control-label">Brightness</label>
  				<div class="col-sm-2">
	  				<input type="text" class="form-control input-lg" id="brightness" disabled>
	  			</div>
				<div class="col-sm-7">
  					<div id="slider-range-min"></div>
				</div>
				<script>
					var level = <?php echo $schedule['brightness'];?>;
					$(function() {
						$( "#slider-range-min" ).slider({
							range: "min",
							value: level, //current brightness given a particular schedule
							min: 1,
							max: 100,
							slide: function( event, ui ) {
								$( "#brightness" ).val( ui.value );
							}
						});
						$( "#brightness" ).val( $( "#slider-range-min" ).slider( "value" ) );
					});
					$('#slider-range-min').slider({ disabled: true });
				</script>		
		
			</div>
		</form>
	</div>
</div>	

<?php
include './footer.php';
?>
</body>
</html>
