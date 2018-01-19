<?php
$page = "settings-game";
$page_title = "Games Settings";
$auth_name = 'manage_settings';
require ROOT.'app/bootstrap.php';

global $instance;

if($no_games && $_GET['t'] != 'add')
	send('game-settings?t=add');

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

require ROOT.'app/views/global/header.php';

if($is_add) : ?>

	<div class="page-header no-bottom">
		<h1>Add a New Game</h1>
	</div>

	<nav aria-label="">
		<ul class="pager">
			<li class="previous"><a href="game-settings"><span aria-hidden="true">&larr;</span> Go Back</a></li>
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
						foreach($instance::$supportedGames as $key => $value) :

							echo '<option value="'.$key.'">'.$value.'</option>';

						endforeach;
						?>
					</select>
				</div>
                <p>
                   Once you've added the game to the echelon database you will then have to go to the config file to add
                    the games database details. For security reasons this is the most secure way of storing your database details.
                </p>
			</div>
		</div>

        <button type="submit" name="game-settings-sub" class="btn btn-primary" >Add Game</button>

		<input type="hidden" name="cng-pw" value="on" />
		<input type="hidden" name="type" value="add" />
		<input type="hidden" name="token" value="<?php echo $add_game_token; ?>" />
	</form>

<?php else: ?>

	<div class="page-header no-bottom">
		<h1>Game Settings: <?php echo $game_name; ?></h1>
	</div>
	<div class="row spacer-bottom">
		<div class="col-md-6">
			<form action="" class="form-horizontal" method="get">
				<label class="control-label align-left col-md-3">Select Game: </label>
				<div class="col-sm-7">
					<select name="game" class="form-control" onchange="this.form.submit()">
						<?php

						// TODO: Come back and rewrite this page.. :P
						foreach ($dbl->getGamesList() as $games):
							if( $game_id == $games['id'] )
								echo '<option selected value="'.$games['id'].'">'. $games['name'] .'</option>';
							else
								echo '<option value="'.$games['id'].'">'. $games['name'] .'</option>';

						endforeach;
						?>
					</select>
				</div>
			</form>
		</div>
		<div class="col-lg-6">
			<a href="game-settings?t=add" class="float-right btn btn-primary" title="Add a Game (DB) to Echelon">Add Game<span aria-hidden="true">&rarr;</span></a>
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
				<h3 class="panel-title">Echelon Plugins</h3>
			</div>
			<div class="panel-body">
				<div class="list-group "><?php
					$plugins_enabled = $config['game']['plugins'];

					foreach(glob(ROOT.'plugins/*') as $name) :

						$name = basename($name);

						if(!empty($plugins_enabled)) :
							if(in_array($name, $plugins_enabled))
								$check = 'checked="checked" ';
							else
								$check = '';

						else:
							## we need this now because it is not in the inc because of no active plugins
							require_once ROOT.'app/classes/Plugins.php'; // require the plugins base class
						endif;

						$file = ROOT.'plugins/'.$name.'/class.php'; // abolsute path - needed because this page is include in all levels of this site
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
		<input type="hidden" name="game" value="<?php echo cleanvar($_GET['game']); ?>" />


	</form>

	<br />

<?php endif;

require ROOT.'app/views/global/footer.php';
?>