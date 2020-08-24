<?php
	// TODO: Redo
	error_reporting(E_ALL ^ E_NOTICE); // show all errors but notices

    define("ROOT", __DIR__."/../../");

    require ROOT.'app/common/functions.php';
	require ROOT.'app/classes/Sessions.php';
	require ROOT.'app/classes/LegacyMembers.php';

    ## fire up the Sessions ##
    // For the install these don't need to be perfect as a new secure cookie will be setup for echelon itself
    define('SES_SALT', 'install');
    define('SALT', 'install');

    $ses = new Session(); // create Session instance
    $ses->sesStart('install_echelon', 0, '/', null, null, randPass(6)); // start session (name 'echelon', 0 => session cookie, path is echelon path so no access allowed oustide echelon path is allowed)

    if( !isset($_SESSION['tmphash']) ) {
        $_SESSION['tmphash'] = hash("sha256", time().rand(0, 9999));
    }

	if( isset($_GET["del"]) && $_GET['del'] == $_SESSION['tmphash'] ) {
	    rmdir_recursive(__DIR__);
        $_SESSION['tmphash'] = "";
	    header("Location ../");
    }

    function rmdir_recursive($dir) {
        foreach(scandir($dir) as $file) {
            if ('.' === $file || '..' === $file) continue;
            if (is_dir("$dir/$file")) rmdir_recursive("$dir/$file");
            else unlink("$dir/$file");
        }
        rmdir($dir);
    }

    if( file_exists( realpath(ROOT.'app/config.php') ) ) {
        if (!isset($_GET['t']) || $_GET['t'] != 'done' || ($_GET['t'] == 'done' && $_GET['z'] != $_SESSION['tmphash']) )
            die("You have already installed Echelon. Please delete this page");
    }

	if($_GET['t'] == 'install') :

		## find the Echelon directory ##
		$install_dir = $_SERVER['SCRIPT_NAME'];
		$echelon_dir = preg_replace('#install/index.php#', '', $install_dir);

		// Create log file
		if( !file_exists(ROOT."app/.bin/") ) {
			mkdir(ROOT . "app/.bin", 0777, true);

		    if( !file_exists(ROOT . "app/.bin/log.txt") )
		        file_put_contents(ROOT . "app/.bin/log.txt", "");
		}

		## Create an Echelon salt
		$ech_salt = genSalt(16);
		$ses_salt = randPass(6);

		## Get the form information ##
		$email = cleanvar($_POST['email']);
		$username = cleanvar($_POST['username']);
		$password = cleanvar($_POST['password']);
		$password_conf = cleanvar($_POST['password-conf']);
		$useMail = cleanvar($_POST['useMail']);
		$db_host = cleanvar($_POST['db-host']);
		$db_user = cleanvar($_POST['db-user']);
		$db_pass = cleanvar($_POST['db-pass']);
		$db_name = cleanvar($_POST['db-name']);

		emptyInput($email, 'your email address');
		emptyInput($username,'your username');
        emptyInput($password, 'your password');
		emptyInput($password_conf, 'your password confirmation');
		emptyInput($db_pass, 'database password');
		emptyInput($db_host, 'database hostname');
		emptyInput($db_user, 'database username');
		emptyInput($db_name, 'database name');

		// check the new email address is a valid email address
		if(!filter_var($email,FILTER_VALIDATE_EMAIL))
			sendBack('That email is not valid');

		if( $password != $password_conf )
			sendBack('You\'re passwords need to match');

		$usingMail = 0;
		if($useMail === 'on')
			$usingMail = 1;

		## test connection is to the Db works ##
		define("DBL_HOSTNAME", $db_host); // hostname of where the server is located
		define("DBL_USERNAME", $db_user); // username that can connect to that DB
		define("DBL_PASSWORD", $db_pass); // Password for that user
		define("DBL_DB", $db_name); // name of the database to connect to
		define("DB_CON_ERROR_SHOW", TRUE);

		//basic server/install checks
		if(!function_exists('mysqli_connect'))
			sendBack('Echelon requires mysqli');

		// start connection to the DB
		require ROOT.'app/classes/LegacyDatabase.php';
		$dbl = LegacyDatabase::getInstance(true);
		$db = new PDO(sprintf("mysql:host=%s;dbname=%s", $db_host, $db_name), $db_user, $db_pass);

		if($dbl->install_error != NULL)
			sendBack("Database error. Please make sure you've imported the echelon.sql file to your mysql database");

        $queryArray = require __DIR__."/database-structure.php";
        foreach ($queryArray as $query):
            foreach ($query as $queryString):
                $db->query($queryString);
            endforeach;
        endforeach;

		$file_read = ROOT.'app/config.tmp.php';

		// Check the file out
		if(!file_exists($file_read))
			die('Config file does not exist');

        if(!is_readable($file_read))
			die('Config file is not readable or does not exsist');

        $tmp = file_get_contents($file_read);

        ## replace anything that needs to be replaced

        $tmp = preg_replace("/%ech_unique%/", $echKey, $tmp);
		$tmp = preg_replace("/%ech_path%/", $echelon_dir, $tmp);
		$tmp = preg_replace("/%ech_salt%/", $ech_salt, $tmp);
		$tmp = preg_replace("/%db_host%/", $db_host, $tmp);
		$tmp = preg_replace("/%db_user%/", $db_user, $tmp);
		$tmp = preg_replace("/%db_pass%/", $db_pass, $tmp);
		$tmp = preg_replace("/%db_name%/", $db_name, $tmp);
		$tmp = preg_replace("/%ses_salt%/", $ses_salt, $tmp);
		$tmp = preg_replace("/%use_mail%/", $usingMail, $tmp);
		$tmp = preg_replace( "/%installed%/", true, $tmp );

		if( !file_put_contents(ROOT.'app/config.php', $tmp) )
			sendBack('Couldn\'t write to the config file, please make sure that the PHP server may write to the echelon install');

		## Setup the random information for the original admin user ##
		$user_salt = genSalt(12);
		$pass_hash = genPw($password, $user_salt);

		## Add user to the database
		$result = $dbl->addUser(strtolower($username), $username, $email, $pass_hash, $user_salt, 2, 1);
		if(!$result)
			sendBack('Their was a problem adding the admin user to the admin tables, please check the users table exists in your Echelon database');

		//update the admins email address
		$dbl->updateSettings($email, 'email', 's');

        send('index.php?t=done&z='.$_SESSION['tmphash']); // send to a thank you done page that explains what next
	endif; // end install

    if( $_GET['t'] == "done" ) {
        $_SESSION['tmphash'] = "";
        $ses->logout();
    }
?>
<!DOCTYPE html>
<html>

	<head>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="/assets/images/logo-dark.png" />

        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

		<title>Echelon Install Package</title>
        <link rel="stylesheet" href="/assets/styles/master.min.css">
        <link rel="stylesheet" href="install.css">
	</head>

	<body>

        <?php if($_GET['t'] == 'done') :
                include ROOT."app/config.php";
            ?>

        <div class="jumbotron">
            <div class="container">
                <h1>Echelon is Installed</h1>
                <p>Thank-you for installing Echelon, B3 Dev. Team. Your admin account has been setup. Please enjoy Echelon <3</p>
                <p>
                    <a class="btn btn-success" href="../" role="button">Go to Echelon</a>
                    <a class="btn btn-primary" href="../me" role="button">Edit your profile</a>
                    <a class="btn btn-danger" href="?del=<?= $_SESSION['tmphash'] ?>" role="button">Remove install folder ( Recommended )</a>
                </p>
            </div>
        </div>

        <div class="container">
            <h3>What do I do next?</h3>
            <ul>
                <li>You are finished installing Echelon. <span class="imp">Please delete the install directory completely from the Echelon folder.</span> If you did not there are huge security concerns</li>
                <li>Read the Echelon the <a href="https://github.com/MichaelHillcox/Echelon/wiki/Usage-Help" title="Learn more about how to use Echelon">how to use Echelon guide</a>.</li>
                <li>Once you login to Echelon please go the Settings page to config you Echelon site</li>
                <li><a href="../">Enjoy Echelon!</a></li>
            </ul>
        </div>

        <?php else : ?>
        <div class="container">
            <div class="page-header">
                <h1>Echelon <small>B3 repository, investigation and control tool</small></h1>
            </div>

            <?php errors(); ?>

            <form action="index.php?t=install" method="post">

                <div class="panel panel-default">
                    <div class="panel-heading">Echelon Settings</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="useMail" checked="checked" />
                                    Use mail server
                                </label>
                            </div>
                            <p class="help-block">If this is not clicked echelon will not send any email, but will still use the addresses</p>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Admin Account</div>
                    <div class="panel-body">
                        <p>To install Echelon you'll need an admin account. Please fill in the bellow and please be sure to use a secure password. If you are not using a mail server then you'll not receive any conformation that your account has been set up.</p>
                        <br>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Your Email:</label>
                                    <input tabindex="1" class="form-control" type="email" name="email"  required="required" />
                                </div>
                                <div class="form-group">
                                    <label>Password:</label>
                                    <input tabindex="3" class="form-control" type="password" name="password" required="required" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Username:</label>
                                    <input tabindex="2" class="form-control" type="text" name="username" required="required" />
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password:</label>
                                    <input tabindex="4" class="form-control" type="password" name="password-conf" required="required" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Echelon Database Setup</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label>Database Host:</label>
                                    <input tabindex="5" class="form-control" type="text" name="db-host" required="required" />
                                    <p class="help-block">The host for the Echelon DB, eg. <strong>localhost</strong> or <strong>mysql.example.com</strong> or <strong>8.8.8.8</strong></p>

                                    <label>Database Username:</label>
                                    <input tabindex="7" class="form-control" type="text" name="db-user" value="echelon" required="required" />
                                    <p class="help-block">Username for the connection; default in setup is <strong>echelon</strong></p>
                                </div>
                                <div class="col-sm-6">
                                    <label>Database Name:</label>
                                    <input tabindex="6" class="form-control" type="text" name="db-name" value="echelon" required="required" />
                                    <p class="help-block">Name of the Echelon database, default is <strong>echelon</strong></p>

                                    <label>Database Password:</label>
                                    <input tabindex="8" class="form-control" type="password" name="db-pass" />
                                    <p class="help-block">Password for the Echelon database user</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="submit" class="btn btn-primary" name="install" value="Install Echelon" />
            </form>
        </div>
        <?php endif // close what kind of page ?>

        <footer>
            <div class="container">
                <p>
                    Echelon
                </p>
                <p class="links">
                    <a href="https://github.com/MichaelHillcox/Echelon/wiki/Help" title="Get help with Echelon">Echelon Help</a>
                    <a href="http://bigbrotherbot.net/forums/forum/" title="Visit the B3 Forums">B3 Forums</a>
                </p>
            </div>
        </footer>

        <script src="/assets/js/app.min.js"></script>
        <script src="/assets/js/jquery.plugins.js" type="text/javascript" charset="utf-8"></script>
	</body>
</html>
