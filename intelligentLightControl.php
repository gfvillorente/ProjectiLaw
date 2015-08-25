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
$groupWarningMaps = "";
$badgeWarningMaps = "";
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
	<title>Intelligent Light Allocation | iLaw</title>
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
		}
		
    </style>
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
	<!--
	<link href='./fullcalendar/fullcalendar.css' rel='stylesheet' />
	<link href='./fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
	<script src='./lib/jquery.min.js'></script>
	<script src='./lib/jquery-ui.custom.min.js'></script>
	<script src='./fullcalendar/fullcalendar.min.js'></script>
	-->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script type="text/javascript" src="./js/jquery.timepicker.js"></script>
	<link rel="stylesheet" type="text/css" href="./css/jquery.timepicker.css" />	
	<style>
	
		#wrap {
			width: 100%;
			height: 100%;
			margin: 0 auto;
			}
	
		#external-events {
			float: left;
			width: 100%;
			padding: 0 10px;
			border: 1px solid #ccc;
			background: #eee;
			text-align: left;
			}
	
		#external-events h4 {
			font-size: 16px;
			margin-top: 0;
			padding-top: 1em;
			}
	
		.external-event { /* try to mimick the look of a real event */
			margin: 10px 0;
			padding: 2px 4px;
			background: #FF9900;
			color: #fff;
			font-size: .85em;
			cursor: pointer;
			}
	
		#external-events p {
			margin: 1.5em 0;
			font-size: 11px;
			color: #666;
			}
	
		#external-events p input {
			margin: 0;
			vertical-align: middle;
			}

		#calendar {
			float: left;
			width: 100%;
			height: 100%;
			}

	</style>
	<script>
		function changeView(){

		}
	</script>
	</head>
	<body onload="changeView()">
<?php
include './header.php';
?>
<!-- Modal for Create Snapshot -->
<div class="modal fade" id="createSnapshot" role="dialog">
    <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Create Schedule Snapshot</h4>
			</div>
			<div class="modal-body">
				<p>Would you like to create a snapshot of your current working schedule?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal" data-toggle="modal" data-target="#finishSnapshot" onclick="createScheduleSnapshot()">Create Snapshot</button>
			</div>
		</div>
    </div>
</div>
<!-- Confirm Snapshot Creation -->
<div class="modal fade" id="finishSnapshot" role="dialog">
    <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Schedule Snapshot</h4>
			</div>
			<div class="modal-body">
				<p>Schedule Snapshot Created Successfully</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" data-dismiss="modal">Okay</button>
			</div>
		</div>
    </div>
</div>
<!-- Revert Schedule -->
<div class="modal fade" id="revertSchedule" role="dialog">
    <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Revert to Saved Schedule Snapshot</h4>
			</div>
			<div class="modal-body">
				<p id="dateSnapshotID">Would you like to revert to your saved schedule snapshot?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-warning" data-dismiss="modal" data-toggle="modal" data-target="#finishRevert" onclick="revertSchedule()">Revert to Saved Schedule</button>
			</div>
		</div>
    </div>
</div>
<!-- Confirm Schedule Revert -->
<div class="modal fade" id="finishRevert" role="dialog">
    <div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Revert Schedule</h4>
			</div>
			<div class="modal-body">
				<p>Scheduled Reverted Successfully!</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-warning" data-dismiss="modal">Okay</button>
			</div>
		</div>
    </div>
</div>
<?php
include './rightnavigationbar.php';
?>
<div id="wrapContent">
	<div id="content">
		<div class="container-fluid">
			<div id='wrap'>
			  <div class="input-group">
			  	<span class="input-group-addon" id="basic-addon1">Target Consumption</span>
			    <input id="targetPCid" type="text" class="form-control" placeholder="Enter your target Power Consumption" aria-label="Watt-Hours">
			    <span class="input-group-addon">Watt-Hours</span>
			  </div>
			  <h2>Power Consumption for <?php echo date("M Y") ?>
			  	<span data-toggle="modal" data-target="#createSnapshot">
				  	<a class="btn btn-default" role="button" data-toggle="tooltip" data-placement="auto" title="Save Schedule Snapshot for easy Schedule Reversion">
				  		<span class="glyphicon glyphicon-screenshot"></span>
				  	</a>
			  	</span>
			  	<span data-toggle="modal" data-target="#revertSchedule">
				  	<a class="btn btn-default" role="button" data-toggle="tooltip" data-placement="auto" title="Revert to Saved Schedule Snapshot">
				  		<span class="glyphicon glyphicon-share"></span>
				  	</a>
			  	</span>
			  </h2>
			  <a href="#" data-toggle="tooltip" title="Enabling the Auto Light Intesity Adjustment will give the user less worries in making sure that the power consumption for the month will not exceed the target consumption. This feature recalculates how much reduction in light intensity should be executed should the Predicted Consumption exceed the Target Consumption. Predicted Consumption is calculated based on the cluster schedules set by the user.">
			  	<label class="checkbox-inline">
			  		<input id="enableALIA" type="checkbox" value="">Enable Auto Light Intensity Adjustment (ALIA) [to be implemented]
			  	</label>
			  </a>
			  <Br><Br>
			  <div class="progress">
			  	<a href="#" data-toggle="tooltip" data-placement="auto" title="Hooray!">
				    <div id="consumptionTarget" class="progress-bar progress-bar-warning" role="progressbar" style="width:40%">
				    </div>
				</a>
			  </div>
			  <div class="progress">
			  	<a href="#" data-toggle="tooltip" data-placement="auto" title="Hooray!">
				    <div id="consumptionCurrent" class="progress-bar progress-bar-info" role="progressbar" style="width:50%">
				    </div>
				</a>
			  </div>
			  <div class="progress">
			  	<a href="#" data-toggle="tooltip" data-placement="auto" title="Hooray!">
				    <div id="consumptionPredicted" class="progress-bar progress-bar-success" role="progressbar" style="width:60%">
				    </div>
				</a>
			  </div>
			 
			  <div class="jumbotron">
			  	<h2 id="intensityID"></h2>
			    <a href="#" class="btn btn-warning btn-lg" role="button" onclick="changeLightIntensities()">Commit New Schedule Changes</a>
			  </div>  
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

</body>
<script type="text/javascript">
var pcTarget;
var pcCurrent;
var pcPredicted;
var testDate = "<?php echo date("m"); ?>";
var intensityReduction;
var enableALIA;
var dateSnapshot;

function changeLightIntensities() {
	//refreshes every 10 seconds
	//setTimeout(changeLightIntensities, 10000);
	var reduction;
	if ((intensityReduction < 1) && (intensityReduction > 0)) {
		reduction = 1;
	}
	else if (intensityReduction > 1) {
		reduction = intensityReduction;
	}
	else {
		return;
	}

    $.ajax({url: "intelligentLightControlSchedUpdate.php?change=" + (1-((reduction*1.25)/100)), success: function(result){
    	//Refresh the graphs after the light intensity changes
        refreshPredictedPowerConsumption();
        setTimeout(function() {
        	changeLightIntensities();
        }, 500);
    }});
}

function refreshPredictedPowerConsumption() {
	//refreshes every 10 seconds
	setTimeout(refreshPredictedPowerConsumption, 10000);
    $.ajax({url: "intelligentLightControlData.php", success: function(result){
    	testConsumption = JSON.parse(result);
    	pcCurrent = parseInt(testConsumption.current);
        pcPredicted = parseInt(testConsumption.predicted);
        refreshProgressBars();
    }});
}

function createScheduleSnapshot() {
    $.ajax({url: "settingsdata.php?createsnapshot", success: function(result){
    	//alert("created snapshot");
    	$.ajax({url: "settingsdata.php?get", success: function(result){
	    	testConsumption = JSON.parse(result);
			dateSnapshot = testConsumption.datesnapshot;
			
	        refreshRevertSchedule();
	    }});
    }});
}

function revertSchedule() {
    $.ajax({url: "settingsdata.php?revertschedule", success: function(result){
    	//testResponse = '"' + result + '"';
    	//setTimeout(refreshProgressBars, 500);
    	refreshProgressBars();
    }});
}

function refreshRevertSchedule() {
	$("#dateSnapshotID").text("Would you like to revert to your saved schedule snapshot [" + dateSnapshot + "]?");
}

function refreshProgressBars() {
  var percentageTarget = 0,
      percentageCurrent = 0,
      percentagePredicted = 0,
      maxValue = 0,
      ratioPredOverTarg = 0;

  intensityReduction = 0;
  //Predicted will always be greater than Current because
  //predicted = current + predicted consumption * remaining hours

  //Predicted should be approximately equal to Target
  if (parseInt(pcTarget) >= parseInt(pcPredicted)) {
    maxValue = pcTarget;
    $("#consumptionPredicted").attr("class", "progress-bar progress-bar-success");
    $("#consumptionPredicted").text("Predicted Consumption: " + parseInt(pcPredicted) + " Watt-Hours");
    $("#consumptionPredicted").parent().attr("title", "Predicted Consumption: " + parseInt(pcPredicted) + " Watt-Hours");
    $(".jumbotron").hide();
  }
  else {
    maxValue = pcPredicted;
    $("#consumptionPredicted").attr("class", "progress-bar progress-bar-danger");
    $("#consumptionPredicted").text("Predicted Consumption: " + parseInt(pcPredicted) + " Watt-Hours (already Exceeds Target!)");
    $("#consumptionPredicted").parent().attr("title", "Predicted Consumption: " + parseInt(pcPredicted) + " Watt-Hours (already Exceeds Target!)");

    ratioPredOverTarg = (((pcPredicted/pcTarget)-1)*100);

    //Intensity reduction is 5% more of calculated for the sake of margins
    intensityReduction = (1 - ((pcTarget-pcCurrent)/(pcPredicted-pcCurrent))) * 100;

    if (enableALIA) {
    	//Automated Changing of Light Intensities
    	changeLightIntensities();
    	$(".jumbotron").hide();
    }
    else {
	    $("#intensityID").text("Predicted Consumption is " + parseFloat(ratioPredOverTarg.toFixed(2)) + 
	    	"% more of Target Consumption. It is suggested that the system reduce the intensity of lights by " + 
	    	parseFloat(intensityReduction.toFixed(2)) + "% to conform with the Target Consumption.");
	    $(".jumbotron").show();
    }
  };

  if (parseInt(pcTarget) < parseInt(pcCurrent)) {
    $("#consumptionTarget").attr("class", "progress-bar progress-bar-danger");
    $("#consumptionTarget").text("Target Consumption: " + parseInt(pcTarget) + " Watt-Hours (impossible)");
    $("#consumptionTarget").parent().attr("title", "Target Consumption: " + parseInt(pcTarget) + " Watt-Hours (impossible)");

    $("#intensityID").text("Not Possible to set Target Consumption to Less than your Current Consumption.");
    $(".jumbotron").show();
  }
  else {
    $("#consumptionTarget").attr("class", "progress-bar progress-bar-warning");
    $("#consumptionTarget").text("Target Consumption: " + parseInt(pcTarget) + " Watt-Hours");
    $("#consumptionTarget").parent().attr("title", "Target Consumption: " + parseInt(pcTarget) + " Watt-Hours");
  };

  percentageTarget = (pcTarget / maxValue) * 100;
  percentageCurrent = (pcCurrent / maxValue) * 100;
  percentagePredicted = (pcPredicted / maxValue) * 100;

  //Texts
  //$("#consumptionTarget").text("Target Consumption: " + pcTarget + " Watt-Hours");
  $("#consumptionCurrent").text("Current Consumption: " + parseInt(pcCurrent) + " Watt-Hours");
  $("#consumptionCurrent").parent().attr("title", "Current Consumption: " + parseInt(pcCurrent) + " Watt-Hours");

  //Widths
  $("#consumptionTarget").css("width", percentageTarget + "%");
  $("#consumptionCurrent").css("width", percentageCurrent + "%");
  $("#consumptionPredicted").css("width", percentagePredicted + "%");
}

function getTargetPowerConsumption() {
  var tempPC = $("#targetPCid").val();

  if (tempPC == "") {
    $.ajax({url: "settingsdata.php?get", success: function(result){
    	testConsumption = JSON.parse(result);
    	pcTarget = parseInt(testConsumption.targetmonthlyconsumption);
    	enableALIA = parseInt(testConsumption.en_alia);
		dateSnapshot = testConsumption.datesnapshot;

        refreshProgressBars();
        refreshRevertSchedule();
    }});
  };

  if (tempPC >= 0) {
    pcTarget = tempPC;

    $.ajax({url: "settingsdata.php?update&targetmonthlyconsumption=" + pcTarget, success: function(result){
    	//just activate the PHP file
    }});
  }
}
$("#targetPCid").keypress(function(e) {
    if(e.which == 13) {
      getTargetPowerConsumption();
      refreshProgressBars();
    }
});
$("#targetPCid").blur(function(){
  getTargetPowerConsumption();
  refreshProgressBars();
});

function getCurrentPowerConsumption() {
  var tempPC = $("#currentPCid").val();

  if (tempPC == "") {
  	return;
  };

  if (tempPC >= 0) {
    pcCurrent = tempPC;
  }
}
$("#currentPCid").keypress(function(e) {
    if(e.which == 13) {
      getCurrentPowerConsumption();
      refreshProgressBars();
    }
});
$("#currentPCid").blur(function(){
  getCurrentPowerConsumption();
  refreshProgressBars();
});

function getPredictedPowerConsumption() {
  var tempPC = $("#predictedPCid").val();

  if (tempPC == "") {
  	return;
  };

  if (tempPC >= 0) {
    pcPredicted = tempPC;
  }
}
$("#predictedPCid").keypress(function(e) {
    if(e.which == 13) {
      getPredictedPowerConsumption();
      refreshProgressBars();
    }
});
$("#predictedPCid").blur(function(){
  getPredictedPowerConsumption();
  refreshProgressBars();
});

$("#enableALIA").click(function() {
	if ($('#enableALIA').is(':checked')) {
		enableALIA = 1;
		changeLightIntensities();
	    //alert("checked");
	}
	else {
		enableALIA = 0;
	    //alert("NO check");
	}
});

$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip(); 
	$(".jumbotron").hide();
	getTargetPowerConsumption();
	refreshPredictedPowerConsumption();
	//refreshProgressBars();
});
</script>

</html>