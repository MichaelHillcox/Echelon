<?php
$page = "me";
$page_title = "My Account";
$auth_name = 'login';
require 'inc.php';

require 'app/views/global/header.php';
?>

<div class="page-header">
	<h1>Edit: My Account</h1>
</div>
<form action="actions/edit-me.php" method="post">
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

			<button id="edit-me-submit" type="submit" class="btn btn-primary btn-lg">Save</button>

		</div>
	</div>

</form>

<?php require 'app/views/global/footer.php'; ?>
