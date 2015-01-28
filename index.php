<?php
session_start();
if (isset($_SESSION['loggedin'])&&($_SESSION['loggedin']==true))
	header("location:./home.php");
else
	$_SESSION['loggedin'] = false;
include './head.php';
?>
		<!-- Custom styles for this template -->
    	<link href="./css/signin.css" rel="stylesheet">
		<title>Sign in | iLaw</title>
	</head>
	<body>
		<div class="container">
		  <form class="form-signin" role="form" action="./checksignin.php" method="post">
		  	<h1 class="text-center"><strong><span class="company-branding">i</span>Law<strong></h1>
			<h3 class="form-signin-heading">Please sign in</h3>
			<input type="text" class="form-control" placeholder="Username" id="username" name="username" required autofocus>
			<input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
			<label class="checkbox">
			  <input type="checkbox" value="remember-me"> Remember me
			</label>
			<button class="btn btn-lg btn-warning btn-block" type="submit">Sign in</button>
		  </form>

		</div>
<?php
include './foot.php';
?>
