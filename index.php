<?php
$page = "home";
$page_title = "Home";
$auth_name = 'login';
$auth_user_here = true;
$b3_conn = false;
$pagination = false;
//require_once 'app/bootstrap.php';
// This is

// this is a test
// A fucking awful custom router.
$router = [
    'home'              => 'dashboard.php',
    'clients'           => 'clients.php',
    'client'            => 'clientdetails.php',
    'login'             => 'login.php',
    'active'            => 'active.php',
    'bans'              => 'bans.php',
    'ban-list'          => 'banlist.php',
    'plugin'            => 'plugins.php',
    'kicks'             => 'kicks.php',
    'map'               => 'map.php',
    'me'                => 'me.php',
    'notice'            => 'notices.php',
    'public-bans'       => 'pubbans.php',
    'register'          => 'register.php',
    'regulars'          => 'regular.php',
    'site-admins'       => 'sa.php',
    'settings'          => 'settings.php',
    'game-settings'     => 'settings-games.php',
    'server-settings'   => 'settings-server.php',
    'error'             => 'error.php',
    'admins'            => 'admins.php'
];

if( !isset($_GET['v']) || empty($_GET['v']) ) {
    include __DIR__ . "/" . $router['home'];
    exit;
}

$request = htmlentities(strip_tags($_GET['v'])); // This should be clean enough
if( !array_key_exists($request, $router) ) {
    // Throw error
    die("Route {$request} doesn't exist in this scope");
}

include __DIR__."/".$router[$request];