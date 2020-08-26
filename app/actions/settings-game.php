<?php
$auth_name = 'manage_settings';
$b3_conn = true; // needed to test the B3 DB for a successful connection
require ROOT.'app/bootstrap.php';

global $config, $tokens, $game, $mem, $instance;

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
	if(!verifyFormToken('addgame', $tokens)) // verify token
		ifTokenBad('Add Game');
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
$g_plugins = isset($_POST['plugins']) ? $_POST['plugins'] : false;
// Verify Password
$password = isset($_POST['password']) ? $_POST['password'] : false; // do not clean passwords


## Check for empty vars ##
emptyInput($name, 'game name');
emptyInput($name_short, 'short version of game name');

if(!$is_add)
	emptyInput($password, 'your current password');
	
if($is_add) :
	## Check game is supported ##
	if(!array_key_exists($game_type, $instance::$supportedGames))
		sendBack('That game type does not exist, please choose a game');
endif;

$enabled = "";
if(!empty($g_plugins)) :
	foreach($g_plugins as $plugin) :
		$enabled .= $plugin.',';
	endforeach;

	$enabled = substr($enabled, 0, -1); // remove trailing comma
endif;

$enable = isset($_POST['enable']) && cleanvar($_POST['enable']) == "on" ? $enable = 0 : $enable = 1;
$path = ROOT."app/config/games/";

## Update DB ##
if($is_add) : // add game queries
    $dbhost = cleanvar($_POST['db-host']);
    $dbname = cleanvar($_POST['db-name']);
    $dbuser = cleanvar($_POST['db-user']);
    $dbpass = cleanvar($_POST['db-pass']);

//	$result = $dbl->addGame($name, $game_type, $name_short);
//
//	if($result === false) // if everything is okay
//		sendBack('There is a problem, the game information was not saved.');

    // New json bit
    // Generate a uid
    $uid = substr(sha1(base64_encode(time().random_bytes(18))), 0, 20); // This should be pretty random
    $fileName = $uid.".json";
    if( file_exists($path.$fileName) )
        sendBack('There is a problem, the game information was not saved.');

    file_put_contents($path.$fileName, json_encode([
        "id" => $uid,
        "name" => $name,
        "type" => $game_type,
        "short" => $name_short,
        "servers" => 0,
        "plugins" => [],
        "active" => true,
        "db" => [
            "host" => $dbhost,
            "name" => $dbname,
            "user" => $dbuser,
            "pass" => $dbpass
        ]
    ]));

//	$id = $result['id'];

	$dbl->addGameCount(); // Add one to the game counter in config table
	
else : // edit game queries
    $gameId = cleanvar($_POST['game']);

	$mem->reAuthUser($password, $dbl);

    $fileName = $gameId.".json";

    if (!file_exists($path.$fileName)) {
        sendBack('attempted to edit game that does not exist');
        return;
    }

    $gameData = json_decode(file_get_contents($path.$fileName));
    $gameUpdateData = [
        "name" => $name,
        "type" => $game_type,
        "short" => $name_short
    ];

    $successful = file_put_contents($path . $fileName, array_merge_recursive($gameData, $gameUpdateData));

//	$result = $dbl->setGameSettings($game, $name, $name_short, $enabled, $enable); // update the settings in the DB
	if(!$successful)
		sendBack('Something did not update. Did you edit anything?');
endif;

## Return with result message
if($is_add)
	set_good('Game Added!');
else 
	set_good('Your settings have been updated');

send('../game-settings');
