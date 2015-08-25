<?php
session_start();
if (!isset($_SESSION['loggedin'])||($_SESSION['loggedin']==false))
	header("location:./");

$host="localhost"; // Host name 
$username="pi"; // Mysql username 
$password="raspberry"; // Mysql password 
$db_name="ilaw"; // Database name 

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

// Get the cluster name
$sql="SELECT * FROM cluster WHERE clusterid=".$_GET['clusterid'];
$result=mysql_query($sql);
$currcluster = array();
$row=mysql_fetch_array($result);

	$currcluster['clusterid'] = $row['clusterid'];
	$currcluster['name'] = $row['name'];

// Get the bulbs associated to the cluster
$sql="SELECT bulbid, ipaddress, streetadd, latitude, longitude, state, name FROM bulb WHERE bulbid IN (SELECT bulbid FROM cluster_bulb WHERE clusterid=".$_GET['clusterid'].")";
$result=mysql_query($sql);
$count = 0;
$markersArray = array();
while($row=mysql_fetch_array($result))
{
	$markersArray[$count]['bulbid'] = $row['bulbid'];
	$markersArray[$count]['ipaddress'] = $row['ipaddress'];
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
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<title>Cluster Control | iLaw</title>
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
  		.liveChart {
			width: 100%;
			margin: 0px auto;
		}

		.embed-container {
			height: 0;
			width: 100%;
			padding-bottom: 56.25%;
			overflow: hidden;
			position: relative;
		}
			
		.embed-container iframe {
			width: 100%;
			height: 100%;
			position: absolute;
			top: 0;
			left: 0;
		}
    </style>
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=true"></script>
	<script>
// Note: This example requires that you consent to location sharing when
// prompted by your browser. If you see a blank space instead of the map, this
// is probably because you have denied permission for location sharing.

var map;

function initialize() {
	var mapOptions = {
		zoom: 17,
		styles: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":17}]},
				{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},
				{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":17}]},
				{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":29},{"weight":0.2}]},
				{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},
				{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},
				{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":21}]},
				{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#000000"},{"lightness":16}]},
				{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40}]},
				{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},
				{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},
				{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},
				{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]}]
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
		<form class="form-horizontal">
			<div class="form-group">
				<label for="nameCluster" class="control-label col-sm-offset-1 col-sm-2">Cluster Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="nameCluster" name="nameCluster" value ="<?php echo $currcluster['name'];?>" disabled>
				</div>
			</div>
			<div id="lightControl">
				<div class="form-group">
					<label for="switch" class="control-label col-sm-offset-1 col-sm-2">Override Switch All</label>
					<div class="col-sm-9" id="switch">
						<?php
							/*
							$sql="SELECT state, currbrightness FROM bulb WHERE bulbid=".$_GET['bulbid'];
							$result=mysql_query($sql);
							$row=mysql_fetch_array($result);

								$marker['state'] = $row['state'];
								$marker['currbrightness'] = $row['currbrightness'];
							
							mysql_free_result($result);
						
							if ($marker['state'] == "on")
								echo "<div id=\"switchedON\" class=\"btn-group\" style=\"\">";
							else if (($marker['state'] == "off") || ($marker['state'] == "cnbr"))
								echo "<div id=\"switchedON\" class=\"btn-group\" style=\"display:none\">";
							*/

							//Switched OFF
							echo "<div id=\"switchedON\" class=\"btn-group\" style=\"display:none\">";
						?>
							<button id="clickOFF" type="button" class="btn btn-warning btn-lg active">&nbsp;ON&nbsp;</button>
							<button id="slideOFF" onclick="toggledisplay('switchedON');" type="button" class="btn btn-default btn-lg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
						</div>
						<?php
							$offline = " ";

							/*
							if ($marker['state'] == "off")
								echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"\">";
							else if ($marker['state'] == "on")
								echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"display:none\">";
							else {
								echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"\">";
								$offline = "disabled";
							}
							*/

							echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"\">";
						?>
							<button id="slideON" onclick="toggledisplay('switchedOFF');" type="button" class="btn btn-default btn-lg" <?php echo $offline;?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
							<button id="clickON" type="button" class="btn btn-default btn-lg active" <?php echo $offline;?>>OFF&nbsp;</button>
						</div>
						<script>

						var state = "off";
						var level = 0;
						/*
						var state = "<?php echo $marker['state'];?>";
						var level = <?php echo $marker['currbrightness'];?>;
						*/

						function switchON() {
							var ip;
							var bulbid;
							/*
							var ip = "<?php echo $marker['ipaddress'];?>";
							var bulbid = <?php echo $marker['bulbid'];?>;
				
							$.get('http://'+ip+'/ilawcontrol.php?state=on&level=10&mode=control', {}, 
								function(data){
									console.log(data);
								});
							$.get('./bulbDB.php?bulbid='+bulbid+'&state=on&level=10&mode=control', {}, 
								function(data){
									console.log(data);
								});
							*/
							var markersArray = <?php echo json_encode($markersArray); ?>;

							for (var i = 0; i < <?php echo $count; ?>; i++){
								ip = markersArray[i]["ipaddress"];
								bulbid = markersArray[i]["bulbid"];

								$.get('http://'+ip+'/ilawcontrol.php?state=on&level=10&mode=control', {}, 
									function(data){
										console.log(data);
									});
								$.get('./bulbDB.php?bulbid='+bulbid+'&state=on&level=10&mode=control', {}, 
									function(data){
										console.log(data);
									});								
							}

							state = "on";
						}
						function switchOFF() {
							var ip;
							var bulbid;

							/*
							var ip = "<?php echo $marker['ipaddress'];?>";
							var bulbid = <?php echo $marker['bulbid'];?>;
				
							$.get('http://'+ip+'/ilawcontrol.php?state=off&level=0&mode=control', {}, 
								function(data){
									console.log(data);
								});
							$.get('./bulbDB.php?bulbid='+bulbid+'&state=off&level=0&mode=control', {}, 
								function(data){
									console.log(data);
								});
							*/
							var markersArray = <?php echo json_encode($markersArray); ?>;

							for (var i = 0; i < <?php echo $count; ?>; i++){
								ip = markersArray[i]["ipaddress"];
								bulbid = markersArray[i]["bulbid"];

								$.get('http://'+ip+'/ilawcontrol.php?state=off&level=0&mode=control', {}, 
									function(data){
										console.log(data);
									});
								$.get('./bulbDB.php?bulbid='+bulbid+'&state=off&level=0&mode=control', {}, 
									function(data){
										console.log(data);
									});	

								//Quick Fix: Redundancy
								$.get('http://'+ip+'/ilawcontrol.php?state=off&level=0&mode=control', {}, 
									function(data){
										console.log(data);
									});
								$.get('./bulbDB.php?bulbid='+bulbid+'&state=off&level=0&mode=control', {}, 
									function(data){
										console.log(data);
									});															
							}

							state = "off";
						}
						function toggledisplay(elementID)
						{	
							(function(style) {
								style.display = style.display === 'none' ? '' : 'none';
							})(document.getElementById(elementID).style);
							if (elementID == 'switchedON')
							{
								(function(style) {
									style.display = style.display === 'none' ? '' : 'none';
								})(document.getElementById('switchedOFF').style);
								document.getElementById('brightness').value = "0";
								$('#slider-range-min').slider( "value", 0 );
								$('#slider-range-min').slider({ disabled: true });
								switchOFF();
							}
							if (elementID == 'switchedOFF')
							{
								(function(style) {
									style.display = style.display === 'none' ? '' : 'none';
								})(document.getElementById('switchedON').style);
								document.getElementById('brightness').value = "10";//1
								$('#slider-range-min').slider( "value", 10 );//1
								$('#slider-range-min').slider({ disabled: false });
								switchON();
							}
						}
						</script>
					</div>
				</div>
				<div class="form-group" id="brightnessSlider">
				<?php
				/*
						$sql="SELECT state, currbrightness  FROM bulb WHERE bulbid=".$_GET['bulbid'];
						$result=mysql_query($sql);
						$row=mysql_fetch_array($result);

							$marker['state'] = $row['state'];
							$marker['currbrightness'] = $row['currbrightness'];
							
						mysql_free_result($result);
				*/
				?>
					
				<label for="brightness" class="col-sm-offset-1 col-sm-2 control-label">Brightness</label>
  				<div class="col-sm-2">
	  				<input type="text" class="form-control input-lg" id="brightness" disabled>
	  			</div>
				<div class="col-sm-7">
  					<div id="slider-range-min"></div>
				</div>
				<script>
					var state = "off";
					var level = 0;

					/*
					state = "<?php //echo $marker['state'];?>";
					level = <?php //echo $marker['currbrightness'];?>;
					*/
				
					$('#slider-range-min .ui-slider-handle').draggable();
					$(function() {
						$( "#slider-range-min" ).slider({
							range: "min",
							value: level, //current brightness given a particular light
							min: 0,
							max: 100,
							slide: function( event, ui ) {
								$( "#brightness" ).val( ui.value );
							}
						});
						$( "#brightness" ).val( $( "#slider-range-min" ).slider( "value" ) );
					});
					$('#slider-range-min').on( "slidechange", function( event, ui ) {
						var level = $( "#slider-range-min" ).slider( "value" );
						//var ip = "<?//php echo $marker['ipaddress'];?>";
						//var ip = "testing";
						var ip;
						//var bulbid = <?php //echo $marker['bulbid'];?>;
						var bulbid;
						var markersArray = <?php echo json_encode($markersArray); ?>;
						
						if(state == "on"){
							for (var i = 0; i < <?php echo $count; ?>; i++){
								ip = markersArray[i]["ipaddress"];
								bulbid = markersArray[i]["bulbid"];

								$.get('http://'+ip+'/ilawcontrol.php?state=on&level='+level+'&mode=control', {}, 
									function(data){
										console.log(data);
									});
								$.get('./bulbDB.php?bulbid='+bulbid+'&state=on&level='+level+'&mode=control', {}, 
									function(data){
										console.log(data);
									});							
							}				
						}
					});			

					/*
					if ((state == "cnbr") || (state == "off"))
						$('#slider-range-min').slider({ disabled: true });
					else
						$('#slider-range-min').slider({ disabled: false });
					*/
				</script>		
		
			</div>
			</div>
		</form>
		<!--<div class="liveChart">
			<div class="embed-container">
			<?php //$url="http://192.168.1.113/ProjectiLaw/chart_api/live_chart.php?bulbid=".$marker['bulbid']."&limit=10&fields=%22watts,timestamp%22";?>
				<iframe id="liveChart" class="col-sm-offset-3 col-sm-9" src=<?php //echo $url;?> frameborder="0"></iframe>
			</div>
		</div>-->
	</div>
</div>	
<?php
include './footer.php';
//include './foot.php';
?>
		<!--<script>
			var bulbid = <?php //echo $marker['bulbid'];?>;
			setInterval(function(){
				$.ajax({
					cache: false,
					type: "GET",
					url: "./lightcontrol.php?bulbid="+bulbid,
					success: function(data){
						$('#lightControl').html(data);
					}
				});
			}, 1500);
		</script>-->
		<script type="text/javascript">
			if ((state == "cnbr") || (state == "off"))
				$('#slider-range-min').slider({ disabled: true });
			else
				$('#slider-range-min').slider({ disabled: false });
		</script>
	</body>
</html>