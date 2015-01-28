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
	</body>
</html>