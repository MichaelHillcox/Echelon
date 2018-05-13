<?php
$auth_user_here = false;
$pagination = false;
$b3_conn = false;
require ROOT.'app/bootstrap.php';

// set and clean vars of unwanted materials
$username = cleanvar($_POST['username']);
$display = cleanvar($_POST['display']);
$pw1 = cleanvar($_POST['pw1']);
$pw2 = cleanvar($_POST['pw2']);

$key = cleanvar($_POST['key']);
$email = cleanvar($_POST['email']);

if($pw1 != $pw2) // if the passwords don't match send them back
	sendBack('The supplied passwords to do match');

// check for empty inputs
emptyInput($display, 'display name');
emptyInput($username, 'username');
emptyInput($pw1, 'your new password');

// check the new email address is a valid email address
if(!filter_var($email,FILTER_VALIDATE_EMAIL))
	sendBack('That email address is not valid');

## Check if key and email are valid ##
$valid_key = $dbl->verifyRegKey($key, $email, $instance->config['sesson-expire']);
if(!$valid_key && key == "0") // if the key sent is a valid one
	sendBack('The key or email you submitted are not valid.');

## Add user to users table ##
// generate a new salt for the user
$salt = genSalt();

// find the hash of the supplied password and the new salt
$password = genPW($pw1, $salt);

if($valid_key) {
	$results = $dbl->getGroupAndIdWithKey($key); // find the permissions for the user that are assoc with the sent key
	$group = $results[0]; // perms for user
	$admin_id = $results[1]; // id of the admin who added this user
}
else { //self registered
	if($config['cosmos']['self_reg'] != 'true') {
		sendBack('Self registration is disabled!');
	}
	$group = 1;
	$admin_id = 0;
}

// username, display, email, password, salt, permissions, admin_id
$result = $dbl->addUser($username, $display, $email, $password, $salt, $group, $admin_id);
if($result == false) // if user was not added to the Db
	sendBack('There was an error, account not setup.');

## Update user_keys table to deactive key ##
if($valid_key)
	$update = $dbl->deactiveKey($key);

// If we have gotten this far then nothing should of gone wrong so we send backa good message
set_good('Your account has been created, you can now login.');
send('../login');
exit;