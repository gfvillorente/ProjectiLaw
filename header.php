	<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="./"><h3 class="brand-name"><strong><span class="company-branding">i</span>Law</strong></h3></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li id="messages"><a href="#"><span class="glyphicon glyphicon-envelope"></span><span class="badge badge-warning">2</span></a></li>
            <li id="notifications"><a href="#"><span class="glyphicon glyphicon-exclamation-sign"></span><span class="badge badge-warning">1</span></a></li>
            <li id="account" class="active dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> <?php echo $_SESSION['user'];?></a>
            	<ul class="dropdown-menu">
            		<li class="dropdown-header"><strong>Account</strong></li>
            		<li><a href="#"><small>Edit Profile</small></a></li>
            		<li><a href="./signout.php"><small>Sign Out</small></a></li>
            	</ul>
            </li>
            <li id="settings" class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span></a>
            	<ul class="dropdown-menu">
            		<li class="dropdown-header"><strong>Settings</strong></li>
            		<li><a href="#"><small>Custom Reports</small><span class="glyphicon glyphicon-file pull-right"></span></a></li>
            		<li><a href="#"><small>Set Notifications</small><span class="glyphicon glyphicon-flag pull-right"></span></a></li>
            		<?php if($_SESSION['user'] == 'Admin') echo '<li>'; else echo '<li class="disabled">'?><a href="#"><small>Manage Users<span class="glyphicon glyphicon-user pull-right"></span></small><span class="glyphicon glyphicon-user pull-right"></span></a></li>
            	</ul>
            </li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>
