<?php
session_start();

if (!isset($_SESSION['logged'])) {
	header('Location: login.php');
	exit();
}

define('CURRENT_PAGE', 'ADMIN');

include_once('base/header.php');
?>

	  <div class="website-template">
		<h1>Well done ;)</h1>
		<br />
		<p class="lead">The flag is : <b><?php echo htmlentities(file_get_contents('flag.txt')) ?></b></p>
	  </div>

<?php
include_once('base/footer.php');
?>