<?php
$page = "settings-game";
$page_title = "Games Settings";
$auth_name = 'manage_settings';
require 'inc.php';

if($no_games && $_GET['t'] != 'add')
	send('settings-games.php?t=add');

if($_GET['t'] == 'add') : // if add game type page

	$is_add = true;
	$add_game_token = genFormToken('addgame');

else : // if edit current game settings

	$is_add = false;
	// We are using the game information that was pulled in setup.php
	$game_token = genFormToken('gamesettings');

	if($_GET['w'] == 'game')
		set_warning('You have changed game/DB since the last page!');
		
endif;

require 'app/views/global/header.php';

if($is_add) : ?>

	<div class="page-header no-bottom">
		<h1>Add a New Game</h1>
	</div>

	<nav aria-label="">
		<ul class="pager">
			<li class="previous"><a href="settings-games.php"><span aria-hidden="true">&larr;</span> Go Back</a></li>
		</ul>
	</nav>
	
	<form action="actions/settings-game.php" method="post">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Names & Game</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="name">Full Name:</label>
							<input type="text" name="name" class="form-control"  />
						</div>
						<div class="col-md-6">
							<label for="name-short">Short Name:</label>
							<input type="text" name="name-short" class="form-control"  />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="game-type">Game:</label>
					<select class="form-control" name="game-type">
						<?php
						foreach($supported_games as $key => $value) :

							echo '<option value="'.$key.'">'.$value.'</option>';

						endforeach;
						?>
					</select>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Names & Game</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="db-host">DB Hostname:</label>
							<input type="text" name="db-host" class="form-control" tabindex="3" />
						</div>
						<div class="col-md-6">
							<label for="db-name">DB Name:</label>
							<input type="text" name="db-name" class="form-control" tabindex="4" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="db-user">DB User:</label>
							<input type="text" name="db-user" class="form-control" tabindex="5" />
						</div>
						<div class="col-md-6">
							<label for="db-pw">DB Password:</label>
							<input type="password" name="db-pw" class="form-control" tabindex="6" />
						</div>
					</div>
				</div>
				<button type="submit" name="game-settings-sub" class="btn btn-primary" >Add Game</button>
			</div>
		</div>

		<input type="hidden" name="cng-pw" value="on" />
		<input type="hidden" name="type" value="add" />
		<input type="hidden" name="token" value="<?php echo $add_game_token; ?>" />
	</form>

<?php else: ?>

	<div class="page-header no-bottom">
		<h1>Game Settings <?php echo $game_name; ?></h1>
	</div>
	<div class="row spacer-bottom">
		<div class="col-md-6">
			<?php $this_cur_page = basename($_SERVER['SCRIPT_NAME']); ?>
			<form action="<?= PATH . $this_cur_page ?>" class="form-horizontal" method="get">
				<label class="control-label align-left col-md-3">Select Game: </label>
				<div class="col-sm-7">
					<select name="game" class="form-control" onchange="this.form.submit()">
						<?php

						// TODO: Come back and rewrite this page.. :P
						foreach ($dbl->getGamesList() as $games):

							// not happy about this bit :P
							if( $game_name_short == $games['name_short'] )
								echo '<option selected value="'.$games['id'].'">'. $games['name_short'] .'</option>';
							else
								echo '<option value="'.$games['id'].'">'. $games['name_short'] .'</option>';

						endforeach;
						?>
					</select>
				</div>
			</form>
		</div>
		<div class="col-lg-6">
			<a href="settings-games.php?t=add" class="float-right btn btn-primary" title="Add a Game (DB) to Echelon">Add Game<span aria-hidden="true">&rarr;</span></a>
		</div>

	</div>

	<form action="actions/settings-game.php" method="post">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Names & Settings</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="name">Full Name:</label>
							<input type="text" name="name" class="form-control" value="<?php echo $game_name; ?>" />
						</div>
						<div class="col-md-6">
							<label for="name-short">Short Name:</label>
							<input type="text" name="name-short" class="form-control" value="<?php echo $game_name_short; ?>" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<h4>Enable / Disabled Game</h4>
					<div class="checkbox">
						<label for="enable">
							<input id="enable" type="checkbox" name="enable" value="enable" <?php if($game_active) : ?>checked="checked"<?php endif;?> />
							Enable/Disable
						</label>
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">B3 DB Information</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="db-host">Hostname:</label>
							<input type="text" name="db-host" class="form-control" value="<?php echo $game_db_host; ?>" />
						</div>
						<div class="col-md-6">
							<label for="db-user">User:</label>
							<input type="text" name="db-user" class="form-control" value="<?php echo $game_db_user; ?>" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="cng-pw" data-endis="password" />
							Change DB Password?
						</label>
					</div>

					<label >DB Password:</label>
					<input class="form-control" data-endis-target="password" type="password" name="db-pw"/>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Echelon Plugins</h3>
			</div>
			<div class="panel-body">
				<div class="list-group "><?php
					$plugins_enabled = $config['game']['plugins'];

					foreach(glob(getenv("DOCUMENT_ROOT").PATH.'app/plugins/*') as $name) :

						$name = basename($name);

						if(!empty($plugins_enabled)) :
							if(in_array($name, $plugins_enabled))
								$check = 'checked="checked" ';
							else
								$check = '';

						else:
							## we need this now because it is not in the inc because of no active plugins
							require_once 'app/classes/Plugins.php'; // require the plugins base class
						endif;

						$file = getenv("DOCUMENT_ROOT").PATH.'app/plugins/'.$name.'/class.php'; // abolsute path - needed because this page is include in all levels of this site
						if(file_exists($file)) {
							include_once $file;
							$plugin = call_user_func(array($name, 'getInstance'), 'name');
							$title = $plugin->getTitle();
							$desc = $plugin->getDescription();
						} else {
							$title = $name;
							$desc = "None Provided";
						}

						echo '
							<a class="list-group-item">
								
								<h4 class="checkbox list-group-item-heading"><label><input id="'. $name .'" type="checkbox" name="plugins[]" value="'. $name .'" '. $check .'/>
						'. $title .'</label></h4>
								
						<p class="list-group-item-text">'.$desc.'</p>
					</a>';
					endforeach;
					?>

				</div>

			</div>
		</div>

		<div class="panel panel-danger ">
			<div class="panel-heading">
				<h3 class="panel-title">Verify Identity</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="verify-pw">Your current password:</label>
					<input class="form-control" type="password" name="password"  />
				</div>
				<button type="submit" name="game-settings-sub" class="btn btn-primary">Save Settings</button>
			</div>
		</div>

		<input type="hidden" name="type" value="edit" />
		<input type="hidden" name="token" value="<?php echo $game_token; ?>" />
		<input type="hidden" name="game" value="<?php echo $game; ?>" />


	</form>

	<br />

<?php endif;

require 'app/views/global/footer.php';
?>