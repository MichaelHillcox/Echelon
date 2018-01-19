<?php
if (!empty($_SERVER["SCRIPT_FILENAME"]) && "bootstrap.php" == basename($_SERVER["SCRIPT_FILENAME"]))
    die ("Please do not load this page directly. Thanks!"); // do not edit

// TODO: Refactor all of this

// TODO: Remove this
// Enable Logs
error_reporting(E_ALL ^ E_NOTICE); // show all errors but notices

// TODO: Add a update checker
// TODO: Remove this. This should be somewhere else
// Make sure that echelon is installed
if( !file_exists(__DIR__."/config.php") ) // if echelon is not install (a constant is added to the end of the config during install) then die and tell the user to go install Echelon
    die('You still need to install Echelon. <a href="install/index.php">Install</a>');

// Include everything we're gunna need
require 'config.php'; // load the config file
require_once 'common/functions.php'; // require all the basic functions used in this site
require_once 'classes/LegacyDatabase.php'; // class to preform all DB related actions
require 'classes/Sessions.php'; // class to deal with the management of sesssions
require 'classes/LegacyMembers.php'; // class to preform all B3 DB related actions
require 'classes/Instance.php';
require 'classes/Helper.php';

$dbl = LegacyDatabase::getInstance(); // start connection to the local Echelon DB

// Setup the main core
$this_page = cleanvar($_SERVER["PHP_SELF"]);
$cookie_time = time()+60*60*24*31; // 31 days from now

## setup the game var ##
$game = 1;

if($_REQUEST['game']) {
    $game = cleanvar($_REQUEST['game']);
    setcookie("game", $game, $cookie_time, PATH); // set the cookie to game value
    send($_SERVER['HTTP_REFERER']);
} elseif($_COOKIE['game']) {
    $game = cleanvar($_COOKIE['game']);
} else
    setcookie("game", $game, $cookie_time, PATH); // set the cookie to game value


settype($game, "integer");
if(!$dbl->isActiveGame($game))
    set_warning('Attempting to access an inactive game');

$dbConfig = $dbl->getSettings();

// Setup an instance of Echelon
$instance = new Echelon\Instance(
    [
        "name" => $dbConfig['name'],
        'min-pass' => $dbConfig['min_pw_len'],
        'num-games' => $dbConfig['num_games'],
        'limit-rows' => $dbConfig['limit_rows'],
        'sesson-expire' => $dbConfig['user_key_expire'],
        'time-format' => $dbConfig['time_format'],
        'time-zone' => $dbConfig['time_zone']
    ]
);

// Add our helper utils
$helper = new Echelon\Helper($instance);

$config = [ "cosmos" => $dbl->getSettings() ];

// TODO: Remove this
## If SSL required die if not an ssl connection ##
if(HTTPS) :
    if(!detectSSL() && !isError()) { // if this is not an SSL secured page and this is not the error page
        sendError("SSL is not enabled. Please Ensure you have configured echelon correctly");
        exit;
    }
endif;

// define email constant
define("EMAIL", $config['cosmos']['email']);

## Time Zone Setup ##
$instance->config['time-zone'] = (empty($instance->config['time-zone']) ? 'Europe/London' : $instance->config['time-zone']);
define("NO_TIME_ZONE", $instance->config['time-zone'] == '');
date_default_timezone_set($instance->config['time-zone']);

// if $game is greater than num_games then game doesn't exist so send to error page with error and reset game to 1
if($instance->config['num-games'] == 0) {
    $no_games = true;

} elseif($game > $instance->config['num-games']) {
    setcookie("game", 1, time()+$cookie_time, $path); // set the cookie to game value
    set_error('That game doesn\'t exist');
    if($page != 'error')
        sendError('That game doesn\'t exist');
}

## Get the games Information for the current game ##
$config['game'] = $dbl->getGameInfo($game);

## setup the plugins into an array
if(!empty($config['game']['plugins'])) {
    $config['game']['plugins'] = explode(",", $config['game']['plugins']);
    $no_plugins_active = false;
} else
    $no_plugins_active = true;

## Get and setup the servers information into the array ##
$servers = $dbl->getServers($game);

$config['game']['servers'] = array(); // create array

## add server information to config array##
$i = 1; // start counter ("i") at 1

if(!empty($servers) && $page != 'banlist') :
    foreach($servers as $server) : // loop thro the list of servers for current game
        $config['game']['servers'][$i] = array();
        $config['game']['servers'][$i]['name'] = $server['name'];
        $config['game']['servers'][$i]['ip'] = $server['ip'];
        $config['game']['servers'][$i]['pb_active'] = $server['pb_active'];
        $config['game']['servers'][$i]['rcon_pass'] = $server['rcon_pass'];
        $config['game']['servers'][$i]['rcon_ip'] = $server['rcon_ip'];
        $config['game']['servers'][$i]['rcon_port'] = $server['rcon_port'];

        $i++; // increment counter
    endforeach;
endif;

if($config['game']['num_srvs'] > 1) :
    define("MULTI_SERVER", true);
    define("NO_SERVER", false);
elseif($config['game']['num_srvs'] == 1) :
    define("MULTI_SERVER", false);
    define("NO_SERVER", false);
else :	// equal to no servers
    define("MULTI_SERVER", false);
    define("NO_SERVER", true);
endif;

## Setup some handy easy to access information for the CURRENT GAME only ##
$game_id = $config['game']['id'];
$game_name = $config['game']['name'];
$game_name_short = $config['game']['name_short'];
$game_num_srvs = $config['game']['num_srvs'];
$game_active = $config['game']['active'];

## setup default page number so this doesn't have to be in every file ##
$page_no = 0;

## fire up the Sessions ##
$ses = new Session(); // create Session instance
$ses->sesStart('echelon', 0, PATH); // start session (name 'echelon', 0 => session cookie, path is echelon path so no access allowed oustide echelon path is allowed)

## create instance of the members class ##
$mem = new Member($_SESSION['user_id'], $_SESSION['name'], $_SESSION['email']);

## Is B3 needed on this page ##
if($b3_conn && $instance->config['num-games'] != 0) : // This is to stop connecting to the B3 Db for non B3 Db connection pages eg. Home, Site Admin, My Account
    require 'classes/B3Database.php'; // class to preform all B3 DB related actions

    $games = GAMES;
    // TODO: Fix this
    if( !isset( $games[$game_id] ) ) {
        sendError("You need add this games database config through the config.php file. This games id is: ".$game_id);
    }

    $db = B3Database::getInstance($games[$game_id]["host"], $games[$game_id]["username"], $games[$game_id]["password"], $games[$game_id]["database"], DB_B3_ERROR_ON); // create connection to the B3 DB
endif;

## Plugins Setup ##
if(!$no_plugins_active) : // if there are any registered plugins with this game

    require 'classes/Plugins.php'; // require the plugins base class

    $plugins = new Plugins();

    foreach($config['game']['plugins'] as $plugin) : // foreach plugin there is

        // file = root to www path + echelon path + path to plugin from echelon path
        $file = ROOT.'plugins/'.$plugin.'/class.php'; // abolsute path - needed because this page is include in all levels of this site

        if(file_exists($file)) :
            require $file;
            $plugins_class["$plugin"] = call_user_func(array($plugin, 'getInstance'), 'name');
        //$plugin::getInstance(); // create a new instance of the plugin (whatever, eg. xlrstats) plugin
        else :
            if($mem->reqLevel('manage_settings')) // only show the error to does who can fix it
                set_error('Unable to include the plugin file for the plugin '. $plugin .'<br /> In the directory: '. $file);
        endif;

    endforeach;

    Plugins::setPluginsClass($plugins_class);

endif;

## If auth needed on this page ##
if(!isset($auth_user_here))
    $auth_user_here = true; // default to login required

if($auth_user_here != false) // some pages do not need auth but include this file so this following line is optional
    $mem->auth($auth_name); // see if user has the right access level is not on the BL and has not got a hack counter above 3

## remove tokens from 2 pages ago to stop build up
if(!isLogin()) : // stop login page from using this and moving the vars
    $tokens = array();

    $num_tokens = count($_SESSION['tokens']);

    if($num_tokens > 0) :
        foreach($_SESSION['tokens'] as $key => $value) :
            $tokens[$key] = $value;
        endforeach;
        $_SESSION['tokens'] = array();
    endif;

endif;

## if no time zone set display error ##
if(NO_TIME_ZONE) // if no time zoneset show warning message
    set_warning("Setup Error: The website's time zone is not set, defaulting to use Europe/London (GMT)");