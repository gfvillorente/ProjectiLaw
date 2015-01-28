<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");
	
$marker = array();
$marker["latitude"] = $_GET['latitude'];
$marker["longitude"] = $_GET['longitude'];

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

include './head.php';
?>
	<title>Add Light | iLaw</title>
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
			padding-left: 20px;
			width: 70%;
			color: white;
		}
		#addressBulb::-webkit-input-placeholder::after {
			display:block;
			content:"Unit Number, House/Building/Street Number + Street Name\A Barangay Name, City/Municipality\A Postal Code + Province\A Country";
		}
	</style>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
	<script>

		var map;
		
		function initialize() {
		  var mapOptions = {
			zoom: 17,
			styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]}]

		  };
		    var map = new google.maps.Map(document.getElementById('map-canvas'),
			    mapOptions);
			
			var markerArray = <?php echo json_encode($marker); ?>;
			
			var options = {
				map: map,
				position: new google.maps.LatLng(markerArray["latitude"], markerArray["longitude"]),
			  };
			
			var infoWindow = new google.maps.InfoWindow(options);
			
			map.setCenter(options.position);
			
			var iconColor = 'http://maps.google.com/mapfiles/ms/icons/grey.png';
	
			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(markerArray["latitude"], markerArray["longitude"]),
				map: map,
				icon: iconColor
			});
			
			var offsetx = 1- (screen.width * 0.375);
			var offsety = 1 - (screen.height * 0.10);
			map.panBy(offsetx, offsety);
		
		}	
		
		google.maps.event.addDomListener(window, 'load', initialize);
		

    	function ValidateIPaddress(inputText)
		 {
			 var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
			 if (inputText == "0.0.0.0")
			 {
				 alert("You have entered an invalid IP address!");
				 document.addDetailsForm.ipAddressBulb.focus();
				 return false;
			 }
			 else if (inputText == "255.255.255.255")
			 {
				 alert("You have entered an invalid IP address!");
				 document.addDetailsForm.ipAddressBulb.focus();
				 return false;
			 }
			 else if(inputText.match(ipformat))
				 return true;
			 else
			 {
				 alert("You have entered an invalid IP address!");
				 document.addDetailsForm.ipAddressBulb.focus();
				 return false;
			 }
		 }
    </script>
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
			<a href="#" class="list-group-item list-group-item-warning dropdown-toggle" data-toggle="dropdown">
			  <span class="glyphicon glyphicon-adjust"></span>
			  Lights
			  <span class="badge pull-right badge-warning"><?php echo $countBulbs; ?></span>
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
</div>
<div id="content">
	<div class="container-fluid">
		<form class="form-horizontal" id="addDetailsForm" name="addDetailsForm" role="form" action="./processaddlight.php" onsubmit="return ValidateIPaddress(document.getElementById('ipAddressBulb').value);" method="post">
		  <div class="form-group">
			<label for="nameBulb" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="nameBulb" name="nameBulb" placeholder="light bulb name" required autofocus>
			</div>
		  </div>
		  <div class="form-group">
			<label for="ipAddressBulb" class="col-sm-2 control-label">IP Address</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="ipAddressBulb" name="ipAddressBulb" placeholder="192.168.1.101" required>
			</div>
		  </div>
		  <div class="form-group">
			<label for="addressBulb" class="col-sm-2 control-label">Address</label>
			<div class="col-sm-10">
			  <textarea class="form-control" rows="5" id="addressBulb" name="addressBulb" placeholder="the nearest..." required></textarea>
			</div>
		  </div>
		  <div class="form-group">
			<label for="bulbCluster" class="col-sm-2 control-label">Cluster</label>
			<div class="col-sm-10">
				<input type="radio" name="optionsRadios" id="addNewCluster" value="new" onclick="document.getElementById('newCluster').disabled = false; document.getElementById('existingClusters').disabled = true;">
				<label>Add this bulb to a new cluster.			  
				<input type="text" class="form-control" name="newCluster" id="newCluster" placeholder="new cluster name" disabled="disabled" required></label>
				<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>--OR--</strong><br>
				<input type="radio" name="optionsRadios" id="selectCluster" value="existing" checked onclick="document.getElementById('newCluster').disabled = true; document.getElementById('existingClusters').disabled = false;">
				<label>Include this bulb to an existing cluster.
					<select class="form-control" id="existingClusters" name="existingClusters">
						<?php
							for($i = 0; $i < $countClusters; $i++) {
								echo "<option value=\"".$clustersArray[$i]['clusterid']."\">".$clustersArray[$i]['name']."</option>";
							}
						?>
					</select>
				</label>
			</div>
		  </div>
		  <?php
		  	echo "<input type=\"hidden\" id=\"latitude\" name=\"latitude\" value=\"".$marker["latitude"]."\">";
		  	echo "<input type=\"hidden\" id=\"longitude\" name=\"longitude\" value=\"".$marker["longitude"]."\">";
		  ?>
		  <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
			  <button type="submit" class="btn btn-warning btn-lg btn-block">Add Light Bulb</button>
			</div>
		  </div>
		</form>
	</div>
</div>
<?php
include './footer.php';
include './foot.php';
?>
