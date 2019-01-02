<?php
if (!empty($_SERVER["SCRIPT_FILENAME"]) && "config.php" == basename($_SERVER["SCRIPT_FILENAME"]))
	die ("Please do not load this page directly. Thanks!"); // do not edit

// Start Editing From below here
// General
define("DB_CON_ERROR_SHOW", TRUE); 			// show DB connection error if any (values: TRUE/FALSE)
define("GRAVATAR", TRUE);					// show gravatars image in header (values: TRUE/FALSE)
define("DB_B3_ERROR_ON", TRUE); 			// show detailed error messages on B3 DB query failure (values TRUE/FALSE)

define("HTTPS", FALSE);
define("USE_MAIL", %use_mail%); 			// whether to use the mail server
define("PATH", "%ech_path%");				// path to echelon from root of web directory. include starting and trailing (eg. "/echelon/" )

// Connection info to connect to the
// database containing the echelon tables
define("DBL_HOSTNAME", "%db_host%"); 		// hostname of where the server is located
define("DBL_USERNAME", "%db_user%"); 		// username that can connect to that DB
define("DBL_PASSWORD", "%db_pass%"); 		// Password for that user
define("DBL_DB", "%db_name%"); 				// Password for that user

define("GAMES", [
    "-1" => [ // ID should be the ID you where given when you added the game to the database
        "username" => "",
        "password" => "",
        "database" => "",
        "host"     => ""
    ]
]);

// IGNORE BELOW HERE
define("ECH_VER", "3.0.0a1");
define("SALT", '%ech_salt%');

define("VER_CHECK_URL", ""); // Not yet supported
define("INSTALLED", %installed%);
define("SES_SALT", '%ses_salt%');

define("ECH_LOG", __DIR__."/.bin/log.txt"); // location of the Echelon Log file
