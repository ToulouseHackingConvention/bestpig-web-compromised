<?php
session_start();

define('CURRENT_PAGE', 'LOGIN');

include_once('base/sha1.php');

if (!empty($_POST['username']) && !empty($_POST['password'])) {
	$account = json_decode(file_get_contents('configs/account.json'));

	if ($_POST['username'] == $account->username && hsha1($_POST['password']) == $account->password) {
		$_SESSION['logged'] = $account->username;
		header('Location: admin.php');
		exit();
	}
	else {
		$error = "Invalid username of password";
	}
}

include_once('base/header.php');
?>

	  <div class="website-template">
		<h1>Admin login page</h1>
		<br />
		<?php if (isset($error)) { ?>
		<div class="alert alert-danger">
		  <strong>Error!</strong> <?php echo htmlentities($error); ?>
		</div>
		<?php } ?>
		<form class="form-signin" method="POST">	
			<label for="username" class="sr-only">Username</label>
			<input name="username" type="text" id="username" class="form-control" placeholder="Username" required autofocus>
			<br />
			<label for="password" class="sr-only">Password</label>
			<input name="password" type="password" id="password" class="form-control" placeholder="Password" required>
			<br />
			<button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
		</form>
	  </div>

<?php
include_once('base/footer.php');
?>