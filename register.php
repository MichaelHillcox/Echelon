<?php
$b3_conn = false;
$auth_user_here = false;
$pagination = false;
require 'inc.php';

if($mem->loggedIn()) { // if logged don't allow the user to register
	set_error('Logged in users cannot register');
	sendHome(); // send to the index/home page
}


if(!isset($_REQUEST['key'])) { 
	// if key does not exists or not self register
	$step = 1; // the user must input a matching key and email address

} else { // if key is sent
	
	// clean vars of unwanted materials
	$key = cleanvar($_REQUEST['key']);
	$email = cleanvar($_REQUEST['email']);
	
	// check the new email address is a valid email address
	if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {  
		set_error('That email is not valid');
	}
	
	// query db to see if key and email are valid
	$valid_key = $dbl->verifyRegKey($key, $email, $key_expire);
	if($valid_key || $key == "0") { // if the key sent is a valid one 
		$step = 2;
	} else {
		$step = 1;
		set_error('The key you submitted are not valid.' . $key . " end key");
	}

}

// basic page setup
$page = "register";
$page_title = "Register";
$dontShow = true;
require 'app/views/global/header.php';

if($step == 1) : // if not key is sent ask for one
?>

	<div id="loginScreen">
		<div id="loginContainer" class="extended">
			<form action="register.php" class="extended panel panel-default" method="post">
				<div class="panel-heading">
					<h1 class="panel-title">Registration</h1>
				</div>
				<div id="loginInput">
					<h3>Whats your Registration Key?</h3>
					<div class="alert alert-info" role="alert"><strong>Please use a registration key if you have one.</strong> Keys are sent out via email by the site admins.</div>

					<div class="form-group">
						<label for="key">Registration key:</label>
						<input type="text" maxlength="40" size="40" class="form-control" name="key" tabindex="1" required />
					</div>
					<div class="form-group">
						<label for="email">Email:</label>
						<input type="email" class="form-control" name="email" tabindex="2" required />
					</div>

					<div class="form-group">
						<input type="submit" id="submit-key-reg" class="btn btn-block btn-primary" value="Validate Key" tabindex="3"  />
					</div>
					<span class="gap"></span>
					<?php if($config['cosmos']['self_reg'] == 'true'): ?>
						<h3>Don't have a Registration Key?</h3>
						<div class="alert alert-info" role="alert">If you don't have a registration key follow below to register without one. This will only register a limited account.</div>

						<div class="form-group">
							<input type="hidden" value="0" class="form-control" name="key" tabindex="4" />
						</div>
						<div class="form-group">
							<label for="email">Email:</label>
							<input type="email" class="form-control" name="email" tabindex="5" required />
						</div>
						<div class="form-group">
							<input type="submit" id="submit-nokey-reg" class="btn btn-block btn-primary" value="Register without a key" tabindex="6" />
						</div>
					<?php endif; ?>
				</div>
			</form>
		</div>
	</div>

<?php else : ?>

	<div id="loginScreen">
		<div id="loginContainer" class="extended">
			<form action="actions/setup-user.php" class="panel panel-default" method="post">
				<div class="panel-heading">
					<h1 class="panel-title">Setup Your Account</h1>
				</div>
				<div id="loginInput">
					<?php errors(); ?>
					<div class="alert alert-info" role="alert">To finish your registration you need to setup your account. Please fill in all these boxes with correct information.</div>
					<div class="form-group">
						<label for="username">Username:</label>
						<input type="text" maxlength="64" class="form-control" name="username" tabindex="1" required />
					</div>
					<div class="form-group">
						<label for="display">Display Name:</label>
						<input type="text" maxlength="64" class="form-control" name="display" tabindex="1" required />
					</div>
					<div class="form-group">
						<label for="pw1">Password:</label>
						<input type="password" class="form-control" name="pw1" tabindex="2" required />
					</div>
					<div class="form-group">
						<label for="pw2">Confirm Password:</label>
						<input type="password" class="form-control" name="pw2" tabindex="2" required />
					</div>

					<input type="hidden" name="key" value="<?php echo $key; ?>" id="key" />
					<input type="hidden" name="email" value="<?php echo $email; ?>" />
					<div class="form-group">
						<input type="submit" class="btn btn-block btn-primary" name="register" value="Login" tabindex="4" />
					</div>

				</div>
			</form>
		</div>
	</div>

<?php endif; ?>

<?php require 'app/views/global/footer.php'; ?>