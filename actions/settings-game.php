<?php
$auth_name = 'manage_settings';
$b3_conn = true; // needed to test the B3 DB for a successful connection
require '../inc.php';

global $config, $tokens, $supported_games, $game, $mem;

## Check that the form was posted and that the user did not just stumble here ##
if(!isset($_POST['game-settings-sub'])) :
	set_error('Please do not call that page directly, thank you.');
	send('../index.php');
endif;

## Find Type ##
$is_add = false;
if($_POST['type'] == 'add')
	$is_add = true;

if( $_POST['type'] != 'edit' && $_POST['type'] != 'add' )
	sendBack('Missing Data');

## Check Token ##
if($is_add) {
	//if(!verifyFormToken('addgame', $tokens)) // verify token
		//ifTokenBad('Add Game');
} else {
	if(!verifyFormToken('gamesettings', $tokens)) // verify token
		ifTokenBad('Game Settings Edit');
}

## Get Vars ##
$name = cleanvar($_POST['name']);
$name_short = cleanvar($_POST['name-short']);
$game_type = null;
if($is_add)
	$game_type = cleanvar($_POST['game-type']);

// plugins enabled
$g_plugins = $_POST['plugins'];
// Verify Password
$password = $_POST['password']; // do not clean passwords
$enable = cleanvar($_POST['enable']);


## Check for empty vars ##
emptyInput($name, 'game name');
emptyInput($name_short, 'short version of game name');

if(!$is_add)
	emptyInput($password, 'your current password');
	
if($is_add) :
	## Check game is supported ##
	if(!array_key_exists($game_type, $supported_games))
		sendBack('That game type does not exist, please choose a game');
endif;

$enabled = "";
if(!empty($g_plugins)) :
	foreach($g_plugins as $plugin) :
		$enabled .= $plugin.',';
	endforeach;

	$enabled = substr($enabled, 0, -1); // remove trailing comma
endif;

$enable == null ? $enable = 0 : $enable = 1;

## Update DB ##
if($is_add) : // add game queries

	$result = $dbl->addGame($name, $game_type, $name_short);

	if($result === false) // if everything is okay
		sendBack('There is a problem, the game information was not saved.');

	$id = $result['id'];

	$dbl->addGameCount(); // Add one to the game counter in config table	
	
else : // edit game queries
	$mem->reAuthUser($password, $dbl);
	$result = $dbl->setGameSettings($game, $name, $name_short, $enabled, $enable); // update the settings in the DB
	if(!$result)
		sendBack('Something did not update. Did you edit anything?');
endif;

## Return with result message
if($is_add)
	set_good('Game Added! You can now go to your config and add the database settings for the id of: '.$id);
else 
	set_good('Your settings have been updated');
send('../settings-games.php');
