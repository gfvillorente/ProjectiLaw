<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 
$tbl_name="bulb"; // Table name 

//Right Navigation Menu Highlights
$groupWarningMaps = "list-group-item-warning";
$badgeWarningMaps = "badge-warning";
$groupWarningLights = "";
$badgeWarningLights = "";
$groupWarningReportsIndividual = "";
$badgeWarningReportsIndividual = "";
$groupWarningReportsCluster = "";
$badgeWarningReportsCluster = "";
$groupWarningSchedules = "";
$badgeWarningSchedules = "";

// Connect to server and select databse.
$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

$sql="SELECT bulbid, streetadd, latitude, longitude, state, name FROM $tbl_name";
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
include './head.php';
?>
	<title>Add Cluster | iLaw</title>
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
		.alert {
			padding-top: 2px;
			padding-bottom: 2px;
			padding-right: 2px;
			margin-right: 20px;
			padding-left: 2px;
			background-color: #FF9900;
		}
		
    </style>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
	<script>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see a blank space instead of the map, this
// is probably because you have denied permission for location sharing.

var map;
function moveToList(name, number) { 
	    $('#content ul').append('<li class="alert alert-warning alert-dismissable" id="'+number+'"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+name+'</li>'); 
}
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
					infoWindow.setContent('<button type="button" class="button btw-warning btn-xs" value="' + markersArray[i]["name"] + '" id="'+markersArray[i]["bulbid"]+'" onclick="moveToList(this.value, this.id);">Add to Cluster</button>');
					infoWindow.open(map, marker);
				}
		})(marker, i));
		bounds.extend(marker.position);
	}
	map.fitBounds(bounds);  
}
google.maps.event.addDomListener(window, 'load', initialize);

    </script>
	</head>
	<body>
<?php
include './header.php';
?>
<div id="map-canvas"></div>
<?php
include './rightnavigationbar.php';
?>
<div id="content">
	<div class="container-fluid">
		<!-- form class="form-horizontal" id="addDetailsForm" name="addDetailsForm" role="form" onsubmit="prepareList();" action="./processaddcluster.php" method="post" -->
		<form class="form-horizontal" id="addDetailsForm" name="addDetailsForm" role="form" onsubmit="prepareList();" action="./processaddclusteralarm.php" method="post">	
		  <div class="form-group">
			<label for="nameCluster" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
			  <input type="text" class="form-control" id="nameCluster" name="nameCluster" placeholder="cluster name" required autofocus>
			</div>
		  </div>
		  <div class="form-group">
			<label for="clusterList" class="col-sm-2 control-label">List</label>
			<div class="col-sm-10 ">
				<ul id="clusterList" class="list-inline">
				</ul>
				<input type="hidden" id="listCluster" name="listCluster" value="">
			</div>
		  </div>
		  <div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
			  <button type="submit" class="btn btn-warning btn-lg btn-block">Register Lights to this Cluster</button>
			</div>
		  </div>
		</form>
	</div>
</div>
<?php
include './footer.php';
?>
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
		<script>
			function array_unique(arr) {
				var result = [];
				for (var i = 0; i < arr.length; i++) {
					if (result.indexOf(arr[i]) == -1) {
						result.push(arr[i]);
					}
				}
				return result;
			}
			function prepareList () {
				var list = document.getElementById("clusterList").getElementsByTagName("li");
				var rawArray = new Array();
				var uniqueList = new Array();
				var clusterName = document.getElementById("nameCluster").value;
				var objClusterString = '{"'+clusterName+'":[{"bulbid":"';
				
				if(list.length == 0) {
					alert("Cluster List is empty!");
					return false;
				}
				else {
					for(var i = 0 ; i < list.length; i++){
						rawArray[i] = list[i].id;							
					}
					uniqueList = array_unique(rawArray);
					for (var i = 0; i < uniqueList.length; i++){
						if(i == (uniqueList.length-1))
							objClusterString = objClusterString + uniqueList[i] +'"}]}';
						else
							objClusterString = objClusterString + uniqueList[i] +'"},{"bulbid":"';
					}
					document.getElementById("listCluster").value = objClusterString;
				}
			}
		</script>
	</body>
</html>