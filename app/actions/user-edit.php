<?php
$auth_name = 'edit_user';
require ROOT.'app/bootstrap.php';

global $tokens, $instance, $dbl;

if(isset($_POST['t']) && $_POST['t'] == 'del') : // delete user

	## get and clean vars ##
	$token = cleanvar($_POST['token']);
	$id = cleanvar($_POST['id']);
	
	## check numeric id ##
	if(!is_numeric($id))
		sendBack('Invalid data sent, request aborted');
		
	# verify token #
	if(!verifyFormToken('del'.$id, $tokens))
		ifTokenBad('Delete Echelon User');

	$result = $dbl->delUser($id);
	if($result)
		sendGood('User has been deleted');
	else
		sendBack('There is a problem. The user has not been deleted');
		
	exit;

elseif(isset($_POST['ad-edit-user'])): // admin edit user
	
	## get and clean vars ##
	$username = cleanvar($_POST['username']);
	$display = cleanvar($_POST['display']);
	$email = cleanvar($_POST['email']);
	$group = cleanvar($_POST['group']);
	$id = cleanvar($_POST['id']);
	
	## check numeric id ##
	if(!is_numeric($id))
		sendBack('Invalid data sent, request aborted');
		
	# verify token #
	if(!verifyFormToken('adedituser', $tokens))
		ifTokenBad('Edit Echelon User');	
	
	$result = $dbl->editUser($id, $username, $display, $email, $group);
	if($result)
		sendGood($display."'s information has been updated");
	else
		sendBack('There is a problem. The user information has not been changed');

	exit;

elseif(isset($_POST['ad-edit-user-password"'])):
    $password = cleanvar($_POST['password']);
    $passwordConfirm = cleanvar($_POST['password-confirm']);
    $id = cleanvar($_POST['id']);

    ## check numeric id ##
    if(!is_numeric($id))
        sendBack('Invalid data sent, request aborted');

    # verify token #
    if(!verifyFormToken('adedituser', $tokens))
        ifTokenBad('Edit Echelon User');

    if( $passwordConfirm != $password )
        sendBack('Passwords do not match!');

    $res = Member::genAndSetNewPW($instance, $password, $id, $instance->config['min-pass']);
    if($res !== true )
        sendBack('Failed to update the password');
    else
        sendGood("Updated Password");

else:
	set_error('You cannot view this page directly');
	send('site-admins');

endif;