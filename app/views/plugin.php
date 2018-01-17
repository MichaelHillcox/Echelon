<?php
$page = "plugin";
$page_title = "Plugin Page";
$auth_name = 'login';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
$query_normal = false;
require 'app/bootstrap.php';

if(!isset($_GET['pl']) || $_GET['pl'] == '') {
	sendError('plug'); // send to error page with no plugin specified error
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

## Require Header ##	
require 'app/views/global/header.php';

if($mem->reqLevel($Cplug->getPagePerm())) // name of the plugin is also the name of the premission associated with it
	echo $Cplug->returnPage($varible); // return the relevant page information for this plugin

require 'app/views/global/footer.php';