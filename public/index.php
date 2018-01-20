<?php

define("ROOT", __DIR__."/../");

// TODO: Make this better?
// A fucking awful custom router.
$router = [
    'home'              => 'dashboard.php',
    'clients'           => 'clients.php',
    'client'            => 'clientdetails.php',
    'login'             => 'login.php',
    'active'            => 'active.php',
    'bans'              => 'bans.php',
    'ban-list'          => 'banlist.php',
    'plugin'            => 'plugin.php',
    'kicks'             => 'kicks.php',
    'map'               => 'map.php',
    'me'                => 'me.php',
    'notice'            => 'notices.php',
    'public-bans'       => 'pubbans.php',
    'register'          => 'register.php',
    'regulars'          => 'regular.php',
    'site-admins'       => 'site-admins.php',
    'settings'          => 'settings.php',
    'game-settings'     => 'settings-games.php',
    'server-settings'   => 'settings-server.php',
    'error'             => 'error.php',
    'admins'            => 'admins.php',
    'actions'           => 'actions.php'
];

$currentLocation = explode("?", explode("/", $_SERVER['REQUEST_URI'])[1]);

if( !isset($currentLocation) || empty($currentLocation) || empty($currentLocation[0]) ) {
    include __DIR__ . "/../app/views/" . $router['home'];
    exit;
}

// Rewrite get data to the $_GET system
$request = $currentLocation[0]; // This should be clean enough

if( isset($currentLocation[1]) ) {
    $getData = explode("&", $currentLocation[1]);

    foreach ($getData as $get):
        $split = explode("=", $get);
        if( isset($split[1]) )
            $_GET[$split[0]] = $split[1];
        else
            $_GET[$split[0]] = "";
    endforeach;

    $_SERVER['QUERY_STRING'] = $currentLocation[1];
}

if( !array_key_exists($request, $router) ) {
    // Throw error
    die("Route {$request} doesn't exist in this scope");
}

include __DIR__."/../app/views/".$router[$request];
