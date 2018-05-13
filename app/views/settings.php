<?php
$page = "settings";
$page_title = "Settings";
$auth_name = 'manage_settings';
$b3_conn = true;
require ROOT.'app/bootstrap.php';

// get a list of main Echelon settings from the config table
$settings = $dbl->getSettings('cosmos');

$token_settings = genFormToken('settings');

// Check for new version
if($mem->reqLevel('see_update_msg') && (isSA() || isHome())) :
	$latest = getEchVer();
	if((date('N') == 1) && ECH_VER !== $latest && $latest != false) // if current version does not equal latest version show warning message
		set_warning('You are not using the lastest version of Echelon ('.$latest.'), please check the <a href="http://www.bigbrotherbot.com/forums/" title="Check the B3 Forums">B3 Forums</a> for more information.');
endif;

require ROOT.'app/views/global/header.php';
?>
<div class="page-header no-bottom">
    <h1>Echelon Settings</h1>
    <p>You can manage the entire Echelon instance from here</p>
</div>

<form action="actions?req=settings" method="post" id="settings-f">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">General Echelon Settings</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-6">
						<label for="name">Site Name:</label><?php tooltip('The name of your site (eg your clanname)'); ?>
						<input type="text" class="form-control" name="name" value="<?php echo $settings['name']; ?>">
					</div>
					<div class="col-lg-6">
						<label for="email">Echelon Admin Email:</label><?php tooltip('Email for the admin of this site'); ?>
						<input type="text" class="form-control" name="email" value="<?php echo $settings['email']; ?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="adminName">Name of Site Admin:</label><?php tooltip('Name of the admin for this site'); ?>
				<input type="text" class="form-control" name="adminName" value="<?php echo $settings['admin_name']; ?>">
			</div>
			<div class="form-group">
				<label for="limit_rows">Max rows in tables</label><?php tooltip('Default number of rows that are shown in tables'); ?>
				<input type="number" name="limit_rows" value="<?php echo $settings['limit_rows']; ?>" class="int form-control">
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-lg-6">
						<label for="time_format">Format of time:</label><?php tooltip('This time format will be used for almost all times displayed on the website'); ?>
						<input class="form-control" type="text" name="time_format" value="<?php echo $settings['time_format']; ?>">
						<p class="help-block">Time format field is the PHP <a class="external" href="http://php.net/manual/en/function.date.php" title="PHP time format setup">time format</a>.</p>
					</div>
					<div class="col-lg-6">
						<label for="time_zone">Time Zone:</label><?php tooltip('Timezone of your game server or web server'); ?>
						<input class="form-control" type="text" name="time_zone" value="<?php echo $settings['time_zone']; ?>">
						<p class="help-block">Timezone field uses PHP <a class="external" href="http://php.net/manual/en/timezones.php" title="PHP time zone lisiting">time zones</a>.</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Security Settings</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="min_pw_len">Minimum password length for users</label><?php tooltip('Minimum length for Echelon user passwords'); ?>
				<input type="number" name="min_pw_len" value="<?php echo $settings['min_pw_len']; ?>" class="int form-control">
			</div>
			<div class="form-group">
				<label for="user_key_expire">Days a user reg. key is active</label><?php tooltip('Number of days a registration key will remain valid after the time it was created'); ?>
				<input type="number" name="user_key_expire" value="<?php echo $settings['user_key_expire']; ?>" class="int form-control">
			</div>
			<div class="checkbox">
				<label >
					<input type="checkbox" name="https"<?php if($settings['https'] == 1) echo ' checked="checked"'; ?> />
					SSL connection required
				</label>
			</div>
			<p class="help-block">Forces HTTPS, only enable if you have an SSL cert. Consult the <a href="https://github.com/MichaelHillcox/Echelon/wiki/SSL" class="external help-docs">Help Docs</a> before you enable this setting.</p>

			<div class="checkbox">
				<label >
					<input type="checkbox" name="allow_ie"<?php if($settings['allow_ie'] == 1) echo ' checked="checked"'; ?>>
					Allow Internet Explorer <?php tooltip('If unchecked, this bans users from using Internet Explorer anywhere but the Public Ban List page'); ?>
				</label>
			</div>
		</div>
	</div>

	<?php if(!$no_games) : ?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Require password for client level edits</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="pw_req_level"<?php if($settings['pw_req_level'] == 1) echo ' checked="checked"'; ?> />
						Require password
					</label>
				</div>

				<select class="form-control" name="pw_req_level_group">
					<?php
						$b3_groups = $db->getB3Groups();
						foreach($b3_groups as $group) :
							$gid = $group['id'];
							$gname = $group['name'];
							if($settings['pw_req_level_group'] == $gid)
								echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
							else
								echo '<option value="'.$gid.'">'.$gname.'</option>';
						endforeach;
					?>
				</select>
			</div>
		</div>
	</div>

	<?php endif; ?>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Email Messages</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="email_header">Text to start all emails:</label><?php tooltip('All emails sent by Echelon will user this email header.'); ?>
				<textarea class="form-control" name="email_header"><?php echo $settings['email_header']; ?></textarea>
			</div>
			<div class="form-group">
				<label for="email_footer">Text to end all emails:</label><?php tooltip('This template will be appended to the end of all emails'); ?>
				<textarea class="form-control" name="email_footer"><?php echo $settings['email_footer']; ?></textarea>
			</div>
			<p class="help-block">There are some varibles that can be used in the email templates, <strong>%name%</strong> is replaced with the users name, and <strong>%ech_name%</strong> is replaced with the name of the website (eg. your clan name)</p>

		</div>
	</div>


	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Regular Users</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="regular-tag">Clan tags to be excluded from regulars:</label><?php tooltip('All listed clan tags will not be shown in the regulars table. Seperate multiple tags with a space.'); ?>
				<input class="form-control" type="text" name="reg_tag" id="regular-tag" value="<?php echo $settings['reg_clan_tags'];?>"/>
			</div>
			<div class="form-group">
				<label for="regular-conn">Connections to be regular:</label><?php tooltip('Number of connections that a player needs to be considered a regular'); ?>
				<input class="form-control" type="number" name="reg_conn" id="regular-conn" value="<?php echo $settings['reg_connections'];?>"/>
			</div>
			<div class="form-group">
				<label for="regular-time">Last seen limit for regulars (in days):</label><?php tooltip('A player has to have logged in within this number of days in oreder to be considered as a regular'); ?>
				<input class="form-control" type="number" name="reg_time" id="regular-time" value="<?php echo $settings['reg_days'];?>"/>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">Registration</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label >
						<input type="checkbox" name="self_reg" value="<?php echo $settings['self_reg'];?>"/>
						Allow self registration:<?php tooltip('Allow users to register an echelon account?'); ?>
					</label>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-danger">
		<div class="panel-heading ">
			<h3 class="panel-title">Verify Yourself</h3>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="verify-pw">Your current password:</label><?php tooltip('Please enter your current Echelon user password so that we know that it is really you editing settings'); ?>
				<input class="form-control" type="password" name="password" id="verify-pw" />
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-primary" name="settings-sub" >Save Echelon Settings</button>
			</div>
		</div>
	</div>

	<input type="hidden" name="token" value="<?php echo $token_settings; ?>" />
</form>

<?php require ROOT.'app/views/global/footer.php'; ?>
