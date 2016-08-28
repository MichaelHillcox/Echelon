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
require 'app/views/global/header.php';
if($step == 1) : // if not key is sent ask for one
?>
<fieldset>
	<legend>Whats your Registration Key?</legend>
	<p class="reg"><strong>Please use a registration key if you have one.</strong> Keys are sent out via email by the site admins.</p>
	<form action="register.php" method="post">
		<label for="key">Registration key:</label>
			<input type="text" id="key" size="40" name="key" />
		<label for="email">Email Address</label>
			<input type="text" id="email" size="40" name="email" />
		<input type="submit" id="submit-key-reg" value="Validate Key" />
	</form>
</fieldset>
<?php if($config['cosmos']['self_reg'] == 'true'):?>
<fieldset>
	<legend>Don't have a Registration Key?</legend>
	<p class="reg">If you don't have a registration key follow below to register without one. This will only register a limited account.</p>
	<form action="register.php" method="post">
		<input type="hidden" name="key" value="0" id="key" />
		<label for="email">Email Address</label>
			<input type="text" id="email" size="40" name="email" />
		<input type="submit" id="submit-nokey-reg" value="Register without a key" />
	</form>
</fieldset>
<?php endif; else : ?>
<fieldset>
	<legend>Setup Your Account</legend>
	
	<?php errors(); ?>
	<p class="reg">To finish your registration you need to setup your account. Please fill in all these boxes with corrent information.</p>
	<form action="actions/setup-user.php" method="post" id="reg-setup">
		<div class="form-left">
			<label for="uname-check">Username:</label>
				<input type="text" name="username" id="uname-check" />
				<div class="result"></div>
				<img class="loader" height="26px" width="26px" src="app/assets/images/indicator.gif" alt="Loading..." />
				
			<br class="clear" />
				
			<label for="display">Display Name:</label>
				<input type="text" name="display" id="display" />
		</div>
		
		<div class="form-right">
			<label for="pw1">Password:</label>
				<input type="password" name="pw1" id="pw1" />
				
			<label for="pw2">Password Again:</label>
				<input type="password" name="pw2" id="pw2" />
		</div>
		<br class="clear" />
			
		<input type="hidden" name="key" value="<?php echo $key; ?>" id="key" />
		<input type="hidden" name="email" value="<?php echo $email; ?>" />
			
		<input type="submit" value="Register" name="register" />
	
	</form>
</fieldset>
<?php endif; ?>

<?php require 'app/views/global/footer.php'; ?>