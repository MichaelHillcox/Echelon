<?php
$page = "me";
$page_title = "My Account";
$auth_name = 'login';
require 'inc.php';

if( isset($_POST['editme']) ):
    // set vars
    $display = cleanvar($_POST['name']);
    $email = cleanvar($_POST['email']);
    $cur_pw = cleanvar($_POST['password']);
    $change_pw = $_POST['change-pw']; // password is being hashed no need to validate

    if($change_pw == 'on') { // check to see if the password is to be changed
        $pass1 = $_POST['pass1'];
        $pass2 = $_POST['pass2'];

        if(!testPW($pass1))
            sendBack('Your new password contains illegal characters: = \' " or space');

        if($pass1 != $pass2) // if the passwords don't match send them back
            sendBack('The supplied passwords to do match');

        emptyInput($pass1, 'your new password');
        $is_change_pw = true; // this is a change password request aswell

    } else // this request requires no password change
        $is_change_pw = false;

    // check for empty inputs
    emptyInput($display, 'display name');
    emptyInput($email, 'email');
    emptyInput($cur_pw, 'your current password');

    // check the new email address is a valid email address
    if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        sendBack('That email is not valid');

    // check to see by comparing to session vars if the display name and email have been changed
    if($display != $mem->name || $email != $mem->email) // sent display name does not match session and same with email
        $is_change_display_email = true; // this is a change request
    else
        $is_change_display_email = false; // this is not a change request

    // if display/email not changed and its not a change pw request then return
    if( (!$is_change_display_email) && (!$is_change_pw) )
        sendBack('You didn\'t change anything, so Echelon has done nothing');

    if($is_change_display_email) : // if the display or email have been altered edit them if not skip this section
        // update display name and email
        $results = $dbl->editMe($display, $email, $mem->id);
        if(!$results) { // if false (if nothing happened)
            sendBack('There was an error updating your email and display name');
        } else { // its been changed so we must update the session vars
            $_SESSION['email'] = $email;
            $_SESSION['name'] = $display;
            $mem->setName($display);
            $mem->setEmail($email);
        }
    endif;

    ## if a change pw request ##
    if($is_change_pw) :

        $result = $mem->genAndSetNewPW($pass1, $mem->id, $min_pw_len); // function to generate and set a new password

        if(is_string($result)) // result is either true (success) or an error message (string)
            sendBack($result);
    endif;

    ## return good ##
    sendGood('Your user information has been successfully updated');
else:
    require 'app/views/global/header.php';
endif;
?>

<div class="page-header">
	<h1>Edit: My Account</h1>
</div>
<form action="" method="post">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Account Info</h3>
		</div>
		<div class="panel-body">
			<div class="form-group disabled">
				<label class="uname" disabled="disabled">Username:</label>
				<input class="form-control" type="text" name="uname" value="<?php echo $_SESSION['username']; ?>" disabled="disabled" />
				<p class="help-block">Contact the owner to have you're username changed</p>
			</div>
			<div class="form-group">
				<label for="name">Display Name:</label><?php tooltip('A name shown to all users, a name used to identify you'); ?>
				<input class="form-control" type="text" name="name" value="<?php echo $mem->name; ?>" tabindex="1" />
			</div>
			<div class="form-group">
				<label for="email">Email:</label><?php tooltip('A valid email address where Echelon can contact you'); ?>
				<input class="form-control" type="text" name="email" value="<?php echo $mem->email; ?>" tabindex="2" />
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Change your password</h3>
		</div>
		<div class="panel-body">
			<div class="checkbox">
				<label>
					<input type="checkbox" name="change-pw" data-endis="password" data-default="active" tabindex="3" />
					Change your password:
					<?php tooltip('Do you want to change your Echelon password'); ?>
				</label>
			</div>
			<div class="form-group">
				<label for="pass1">New Password:</label>
				<input type="password" data-endis-target="password" name="pass1" value="" class="form-control" tabindex="4" />
			</div>
			<div class="form-group">
				<label for="pass2">New Password Again:</label>
				<input type="password" data-endis-target="password" name="pass2" value="" class="form-control" tabindex="5" />
			</div>
		</div>
	</div>

	<div class="panel panel-danger">
		<div class="panel-heading">
			<h3 class="panel-title">Prove Identity</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="password">Your Current Password:</label>
				<input type="password" name="password" class="form-control" value="" tabindex="5" />
			</div>

			<button name="editme" id="edit-me-submit" type="submit" class="btn btn-primary btn-lg">Save</button>

		</div>
	</div>

</form>

<?php require 'app/views/global/footer.php'; ?>
