<?php
if (!empty($_SERVER["SCRIPT_FILENAME"]) && "config.php" == basename($_SERVER["SCRIPT_FILENAME"]))
		die ("Please do not load this page directly. Thanks!"); // do not edit

##### Start Editing From below here #####

#############
## General ##
define("DB_CON_ERROR_SHOW", TRUE); // show DB connection error if any (values: TRUE/FALSE)
define("GRAVATAR", TRUE); // show gravatars image in header (values: TRUE/FALSE)
define("DB_B3_ERROR_ON", TRUE); // show detailed error messages on B3 DB query failure (values TRUE/FALSE)

define("USE_MAIL", FALSE); //whether to use the mail server

$path = "/Legacy-Echelon/echelon/"; // path to echelon from root of web directory. include starting and trailing (eg. "/echelon/" )
define("PATH", $path);

## Connection info to connect to the database containing the echelon tables
define("DBL_HOSTNAME", "localhost"); // hostname of where the server is located
define("DBL_USERNAME", "root"); // username that can connect to that DB
define("DBL_PASSWORD", "mysqlpass"); // Password for that user
define("DBL_DB", "echelon"); // Password for that user

#############################
///// IGNORE BELOW HERE /////

define("SALT", 'POJposjDPojspaodjpw'); // do not change ever, this is salt for hashes

$supported_games = array( // supported games
	"q3a" => "Quake 3 Arena",
	"cod" => "Call of Duty",
	"cod2" => "Call of Duty 2",
	"cod4" => "Call of Duty: Modern Warfare",
	"cod5" => "Call of Duty: World at War",
	"cod6" => "Call of Duty: Modern Warfare 2",
	"cod7" => "Call of Duty: Black Ops",
        "moh" => "Medal of Honor",
        "bfbc2" => "Battlefield: Bad Company 2",
	"iourt41" => "Urban Terror",
	"etpro" => "Enemy Territory",
	"wop" => "World of Padman",
	"smg" => "Smokin' Guns",
	"smg11" => "Smokin' Guns 1.1",
	"oa081" => "Open Arena",
        "alt" => "Altitude"
);

// URL to check for updates with
## Echelon Version ##
define("ECH_VER", "2.1.0.1");
define("VER_CHANNEL", "alpha");
define("VER_CHECK_URL", "http://projects.michaelhillcox.co.uk/echelon/index.php");

// Do not touch this varible
define("INSTALLED", 'yes');

// Do not touch this varible either
define("SES_SALT", 'RUjoiF');

$ech_log_path = getenv("DOCUMENT_ROOT").PATH."app/.bin/log.txt";
define("ECH_LOG", $ech_log_path); // location of the Echelon Log file
unset($ech_log_path);



