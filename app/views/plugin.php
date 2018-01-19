<?php

// This is a shitty work around because of the new router.
// Eventually I will work out a much better plugin system to stop us needing to server the file back like this
if( isset($_GET['asset']) ) {
    // TODO: Fix this?
    // NOTE: Not happy about this solution in the slightest.
    $plugin = strip_tags(htmlentities($_GET['pl']));
    $asset = strip_tags(htmlentities($_GET['asset']));
    if( file_exists(ROOT.'plugins/'.$plugin.'/'.$asset) ) {
        $tmp = explode(".", $asset);
        $pieces = end($tmp);
        if( $pieces ) {
            if( $pieces == ".css" )
                header("Content-type: text/css; charset: UTF-8");
            else
                header('Content-Type: text/javascript');

            header("Cache-Control: max-age=2592000");
            include ROOT . 'plugins/' . $plugin . '/' . $asset;
            exit;
        }
        header("HTTP/1.0 404 Not asd");
    }
    else
        header("HTTP/1.0 404 Not Found");
    exit;
}

if( isset($_GET['do']) ) {
    // TODO: allow plugins to define this location
    $plugin = strip_tags(htmlentities($_GET['pl']));
    if( file_exists(ROOT.'plugins/'.$plugin.'/actions.php') )
        include ROOT.'plugins/'.$plugin.'/actions.php';
    else
        header("error?m=".base64_encode("Something's gone wrong?"));
    exit;
}

$page = "plugin";
$page_title = "Plugin Page";
$auth_name = 'login';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
$query_normal = false;
require ROOT.'app/bootstrap.php';

if(!isset($_GET['pl']) || $_GET['pl'] == '') {
	sendError("No plugin specified"); // send to error page with no plugin specified error
	exit;
}
$plugin = addslashes(cleanvar($_GET['pl']));
	
$varible = NULL;
if(isset($_GET['v']))
	$varible = cleanvar($_GET['v']);
	
$page = $plugin; // name of the page is the plugin name
$Cplug = $plugins_class["$plugin"];

$page_title = $Cplug->getTitle(); // get the page title from the title of the plugin

$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] . '?pl=' . $_GET['pl'];

// Preform an account

## Require Header ##
require ROOT.'app/views/global/header.php';

if ($mem->reqLevel($Cplug->getPagePerm())) // name of the plugin is also the name of the premission associated with it
    echo $Cplug->returnPage($varible); // return the relevant page information for this plugin

require ROOT.'app/views/global/footer.php';
