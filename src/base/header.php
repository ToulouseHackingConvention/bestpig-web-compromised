<!doctype html>
<html lang="en">
  <head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="">
	<link rel="icon" href="assets/images/favicon.ico">

	<title>My personal website</title>

	<!-- Bootstrap core CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

	<link href="assets/css/website-template.css" rel="stylesheet">
  </head>

  <body>

	<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
	  <a class="navbar-brand" href="#"><img src="assets/images/logo.png" /></a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
		<ul class="navbar-nav mr-auto">
		  <li class="nav-item <?php if (CURRENT_PAGE == 'HOME') {echo 'active';} ?>">
			<a class="nav-link" href="index.php">Home</a>
		  </li>
		  <li class="nav-item <?php if (CURRENT_PAGE == 'ADMIN') {echo 'active';} ?>">
			<a class="nav-link" href="admin.php">Admin</a>
		  </li>
		</ul>
		
		<ul class="nav navbar-nav navbar-right">
			<li class="nav-item <?php if (CURRENT_PAGE == 'LOGIN') {echo 'active';} ?>">
			<?php if (!isset($_SESSION['logged'])) { ?>
				<a class="nav-link" href="login.php">Login</a>
			<?php } else { ?>
				<a class="nav-link" href="logout.php">Logout (<?php echo htmlentities($_SESSION['logged']) ?>)</a>
			<?php } ?>
			</li>
		</ul>
	  </div>
	</nav>

	<main role="main" class="container">