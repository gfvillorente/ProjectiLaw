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
$groupWarningReportsCluster = "list-group-item-warning";
$badgeWarningReportsCluster = "badge-warning";
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
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
		
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
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
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
<?php
include './rightnavigationbar.php';
?>
<div id="wrapContent">
	<div id="content">
		<div class="container-fluid">
			<div id="wrap">
				<div class="btn-group btn-group-xs pull-right">
					<a class="btn btn-default active" href="./readingstariffaveragecluster.php?clusterid=<?php echo $_GET['clusterid'];?>">Monthly Average</a>
				</div>
				<div id="chart"></div>

				<!-- Modal Message Tariffs -->
				<div id="myModal" class="modal fade">
					<div class="modal-dialog modal-sm">
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
								<h4 class="modal-title">Enter Tariff Rates (Php/KWH)</h4>
							</div>
							<div class="modal-body">
								<form class="form-horizontal">
									<div id="divmon0" class="form-group">
									  <label for="mon0" class="col-sm-2 control-label">Jan</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon0" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon1" class="form-group">
									  <label for="mon1" class="col-sm-2 control-label">Feb</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon1" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon2" class="form-group">
									  <label for="mon2" class="col-sm-2 control-label">Mar</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon2" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon3" class="form-group">
									  <label for="mon3" class="col-sm-2 control-label">Apr</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon3" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon4" class="form-group">
									  <label for="mon4" class="col-sm-2 control-label">May</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon4" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon5" class="form-group">
									  <label for="mon5" class="col-sm-2 control-label">Jun</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon5" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon6" class="form-group">
									  <label for="mon6" class="col-sm-2 control-label">Jul</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon6" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon7" class="form-group">
									  <label for="mon7" class="col-sm-2 control-label">Aug</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon7" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon8" class="form-group">
									  <label for="mon8" class="col-sm-2 control-label">Sep</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon8" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon9" class="form-group">
									  <label for="mon9" class="col-sm-2 control-label">Oct</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon9" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon10" class="form-group">
									  <label for="mon10" class="col-sm-2 control-label">Nov</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon10" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									<div id="divmon11" class="form-group">
									  <label for="mon11" class="col-sm-2 control-label">Dec</label>
									  <div class="col-sm-10">
										  <input type="text" class="form-control" id="mon11" aria-describedby="sizing-addon2" placeholder="0.0">
									  </div>
									</div>
									
								</form>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
								<button type="button" class="btn btn-warning" data-dismiss="modal" onclick=computeMonthlyCost()>Apply</button>
							</div>
						</div>
					</div>
				</div>	
				
				<div id="tariffbtn"><p><a data-toggle="modal" href="#myModal" class="btn btn-warning btn-large">Add Monthly Tariff Values</a></p></div>
				
				<div id="chartcost"></div>
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
			WHERE cluster_bulb.clusterid = ".$_GET['clusterid'];

	$result=mysql_query($sql);

	$bulbCtr = 0;
	$tempBulbCluster;
	while($row = mysql_fetch_array($result)) {
		$tempBulbCluster[$bulbCtr]['bulbid'] = $row['bulbid'];
		$bulbCtr++;		
	}	

	//echo json_encode($tempBulbCluster);

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
?>
<script>
var monthlyave = <?php echo json_encode($MonthlyAveragePower);?>;

var datamonthlyave = [];
var datamonthlycost = [];
var i=0;
var firstrun = true;

for (i = 0; i < 12; i++) {
	datamonthlyave[i] = parseFloat(monthlyave[i]);
}

hideZeroMonths();

$(function () {
    $('#chart').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: "Monthly Average Power Consumption for "+"<strong><?php echo $clustersArray[$_GET['clusterid'] - 1]['name'];?></strong>"
        },
        subtitle: {
            text: 'Source: Project ilaw'
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Energy Consumption (Watt Hour)' //'Real Power (Watts)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:#FF9900;padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} Watt-Hour</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: "<strong><?php echo $clustersArray[$_GET['clusterid'] - 1]['name'];?></strong>",
            //data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
			data: datamonthlyave,
			color: '#FF9900'
        }]
    });
});

function chartMonthlyCost() {
    $('#chartcost').highcharts({
        chart: {
            type: 'column'
        },
        title: {
            text: "Monthly Average Cost for "+"<strong><?php echo $clustersArray[$_GET['clusterid'] - 1]['name'];?></strong>"
        },
        subtitle: {
            text: 'Source: Project ilaw'
        },
        xAxis: {
            categories: [
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun',
                'Jul',
                'Aug',
                'Sep',
                'Oct',
                'Nov',
                'Dec'
            ]
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Cost (Pesos)'
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
            pointFormat: '<tr><td style="color:#9D9D9D;padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} Pesos</b></td></tr>',
            footerFormat: '</table>',
            shared: true,
            useHTML: true
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: [{
            name: "<strong><?php echo $clustersArray[$_GET['clusterid'] - 1]['name'];?></strong>",
            //data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
			data: datamonthlycost,
			color: '#9D9D9D'
        }]
    });
}

function updateMonthlyCost() {
	var chart = $('#chartcost').highcharts();
	var i=0;
	
	for (i = 0; i < 12; i++) {
		chart.series[0].data[i].update(datamonthlycost[i]);
	}
}

function computeMonthlyCost() {
	var monthId = "mon";
	var monthCost = 0;
	var i=0;

	for (i = 0; i < 12; i++) {
		monthId = "mon" + i;
		if(document.getElementById(monthId).value == "") {
			monthCost = 0;
		}
		else {
			monthCost = parseFloat(document.getElementById(monthId).value);
		}
		
		datamonthlycost[i] = (monthCost * datamonthlyave[i]) / 1000;
	}

	if(firstrun) {
		chartMonthlyCost();
		firstrun = false;
	}
	else {
		updateMonthlyCost();
	}
}

function hideZeroMonths() {
	var monthId = "divmon";
	var i=0;

	for (i = 0; i < 12; i++) {
		if(datamonthlyave[i] == 0) {
			monthId = "divmon" + i;
			document.getElementById(monthId).style.display = "none";
		}
	}
}

</script>
	
</body>
</html>

































