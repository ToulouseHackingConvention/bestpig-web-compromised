<?php
session_start();
define('CURRENT_PAGE', 'HOME');

include_once('base/header.php');
?>

	  <div class="website-template">
		<h1>Hello from my personal website</h1>
		<br />
		<p class="lead">
			All content has been removed due to repeatly site deface :/.
			<br />
			I don't understand how the hacker is able to hack my website. 
			<br />
			I changed my password many times but I still get hacked.
			<br /><br />
			Here are the source code of my website, if you found any flaw, please notice me.
			<br /><br />
			<a class="btn btn-primary" href="sources.zip" role="button">Download sources</a>
		</p>
	  </div>

<?php
include_once('base/footer.php');
?>