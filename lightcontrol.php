				<div class="form-group">
					<label for="switch" class="control-label col-sm-offset-1 col-sm-2">Switch</label>
					<div class="col-sm-9" id="switch">
						<?php
							$host="localhost"; // Host name 
							$username="pi"; // Mysql username 
							$password="raspberry"; // Mysql password 
							$db_name="ilaw"; // Database name 

							// Connect to server and select databse.
							$con=mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
							mysql_select_db("$db_name")or die("cannot select DB");

							$sql="SELECT state, currbrightness, ipaddress, bulbid FROM bulb WHERE bulbid=".$_GET['bulbid'];
							$result=mysql_query($sql);
							$row=mysql_fetch_array($result);

								$marker['state'] = $row['state'];
								$marker['currbrightness'] = $row['currbrightness'];
								$marker['ipaddress'] = $row['ipaddress'];
								$marker['bulbid'] = $row['bulbid'];
							
							mysql_free_result($result);
						
							if ($marker['state'] == "on")
								echo "<div id=\"switchedON\" class=\"btn-group\" style=\"\">";
							else if (($marker['state'] == "off") || ($marker['state'] == "cnbr"))
								echo "<div id=\"switchedON\" class=\"btn-group\" style=\"display:none\">";
						?>
							<button id="clickOFF" type="button" class="btn btn-warning btn-lg active">&nbsp;ON&nbsp;</button>
							<button id="slideOFF" onclick="toggledisplay('switchedON');" type="button" class="btn btn-default btn-lg">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
						</div>
						<?php
							$offline = " ";
							if ($marker['state'] == "off")
								echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"\">";
							else if ($marker['state'] == "on")
								echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"display:none\">";
							else {
								echo "<div id=\"switchedOFF\" class=\"btn-group\" style=\"\">";
								$offline = "disabled";
							}
						?>
							<button id="slideON" onclick="toggledisplay('switchedOFF');" type="button" class="btn btn-default btn-lg" <?php echo $offline;?>>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
							<button id="clickON" type="button" class="btn btn-default btn-lg active" <?php echo $offline;?>>OFF&nbsp;</button>
						</div>
						<script>
						var state = "<?php echo $marker['state'];?>";
						var level = <?php echo $marker['currbrightness'];?>;
						
						function switchON() {
							var ip = "<?php echo $marker['ipaddress'];?>";
							var bulbid = <?php echo $marker['bulbid'];?>;
				
							$.get('http://'+ip+'/ilawcontrol.php?state=on&level=1&mode=control', {}, 
								function(data){
									console.log(data);
								});
							$.get('./bulbDB.php?bulbid='+bulbid+'&state=on&level=1&mode=control', {}, 
								function(data){
									console.log(data);
								});
							state = "on";
						}
						function switchOFF() {
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
								document.getElementById('brightness').value = "1";
								$('#slider-range-min').slider( "value", 1 );
								$('#slider-range-min').slider({ disabled: false });
								switchON();
							}
						}
						</script>
					</div>
				</div>
				<div class="form-group" id="brightnessSlider">
				<?php
							$sql="SELECT state, currbrightness, ipaddress, bulbid FROM bulb WHERE bulbid=".$_GET['bulbid'];
							$result=mysql_query($sql);
							$row=mysql_fetch_array($result);

								$marker['state'] = $row['state'];
								$marker['currbrightness'] = $row['currbrightness'];
								$marker['ipaddress'] = $row['ipaddress'];
								$marker['bulbid'] = $row['bulbid'];

							
						mysql_free_result($result);
				?>
					
				<label for="brightness" class="col-sm-offset-1 col-sm-2 control-label">Brightness</label>
  				<div class="col-sm-2">
	  				<input type="text" class="form-control input-lg" id="brightness" disabled>
	  			</div>
				<div class="col-sm-7">
  					<div id="slider-range-min"></div>
				</div>
				<script>
					state = "<?php echo $marker['state'];?>";
					level = <?php echo $marker['currbrightness'];?>;
				
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
						var ip = "<?php echo $marker['ipaddress'];?>";
						var bulbid = <?php echo $marker['bulbid'];?>;
						if(state == "on"){
							$.get('http://'+ip+'/ilawcontrol.php?state=on&level='+level+'&mode=control', {}, 
								function(data){
									console.log(data);
								});
							$.get('./bulbDB.php?bulbid='+bulbid+'&state=on&level='+level+'&mode=control', {}, 
								function(data){
									console.log(data);
								});
						}
					});			
					if ((state == "cnbr") || (state == "off"))
						$('#slider-range-min').slider({ disabled: true });
					else
						$('#slider-range-min').slider({ disabled: false });
				</script>		
		
			</div>
