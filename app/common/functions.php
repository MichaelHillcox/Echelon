<?php
#### FUNCTIONS.PHP ####
## Basic functions that help run all pages on this site ##
## This page is included on all pages in this project ##

function fatalError( $message ) {
	$_SESSION['fatal_error_message'] = $message;
	include_once __DIR__ . "/../views/error.php";

	$_SESSION['fatal_error_message'] = '';
	return false;
}

/**
 * Checks if a password contains any unwanted characters
 *
 * @param string $pw - password string
 * @return bool
 */
function testPW($pw) {

	// no space
	if(preg_match('# #', $pw))
		return false;

	// no dbl quote
	if(preg_match('#"#', $pw))
		return false;
	
	// no single quote
	if(preg_match("#'#", $pw))
		return false;
	
	// no equals signs
	if(preg_match("#=#", $pw))
		return false;
	
	return true;
}

/**
 * Checks that the supplied id matches the required criteria 
 *
 * @param string $id - the id to check. The id is MySQL auto_increment id check
 * @return bool
 */
function isID($id) {

	// not empty
	if(empty($id))
		return false;
	
	// stops first number of id being a zero
	$fc = substr($id, 0, 1);
	if($fc == 0)
		return false;
		
	return is_numeric($id);
}

function delUserLink($id, $token) {

	if($_SESSION['user_id'] == $id) // user cannot delete themselves
		return NULL;
	else
		return '<form action="actions/user-edit.php" method="post" class="user-del">
				<input type="hidden" value="'.$token.'" name="token" />
				<input type="hidden" value="'.$id.'" name="id" />
				<input type="hidden" value="del" name="t" />
				<input class="harddel" type="image" src="app/assets/images/user_del.png" alt="Delete" title="Delete this user forever" />
			</form>';

}

function editUserLink($id, $name) {

	return '<a href="sa?t=edituser&amp;id='.$id.'" title="Edit '. $name .'"><img src="app/assets/images/user_edit.png" alt="edit" /></a>';
	
}

function displayEchLog($array, $style = 'client') {
	if(empty($array))
        return;

	global $instance;

	foreach($array as $ech_log) :
	
		## get vars
		$id = $ech_log['id'];
		$type = $ech_log['type'];
		$msg = tableClean($ech_log['msg']);
		$time_add = $ech_log['time_add'];
		$time_add_read = date($instance->config['time-format'], $time_add);
		$game_id = $ech_log['game_id'];
		$game = $ech_log['name_short'];
		
		## Page row color alternate
		$alter = alter();
		
		if($style == 'admin') :
		
			$cid = $ech_log['client_id'];
			$client_link = clientLink($cid, $cid, $game_id);
			
			$table = <<<EOD
			<tr class="$alter">
				<td>$id</td>
				<td>$type</td>
				<td>$msg</td>
				<td><em>$time_add_read</em></td>
				<td>$client_link</td>
				<td>$game</td>
			</tr>
EOD;

		else: // if client
		
			$user_name = tableClean($ech_log['user_name']);
			$user_link = echUserLink($ech_log['user_id'], $user_name);
		
			$table = <<<EOD
			<tr class="$alter">
				<td>$id</td>
				<td>$type</td>
				<td>$msg</td>
				<td><em>$time_add_read</em></td>
				<td>$user_link</td>
			</tr>
EOD;
		endif;
		
		echo $table; // echo out the formated data
			
	endforeach;

}

function alter() {

	static $alt = false;
	
	$alt = !$alt;
	
	if($alt)
		return 'odd';
	else
		return 'even';

}

/**
 * Sends an rcon comand to a server
 *
 * @param string $rcon_ip - IP for rcon connections
 * @param string $rcon_port - Port for rcon connection
 * @param string $rcon_pass - Server rcon Password
 * @param string $command - The rcon command being sent
 * @return string
 */
function rcon($rcon_ip, $rcon_port, $rcon_pass, $command) {

	$fp = fsockopen("udp://$rcon_ip",$rcon_port, $errno, $errstr, 2);
	@socket_set_timeout($fp, 2); // if error, ignore because some servers block this command

	if(!$fp) {
		return "$errstr ($errno)<br>\n";
	} else {
		$query = "\xFF\xFF\xFF\xFFrcon \"" . $rcon_pass . "\" " . $command;
		fwrite($fp,$query);
	}
	
	$data = '';
	while($d = fread($fp, 10000)) :
	    $data .= $d;
	endwhile;
	
	fclose($fp);
	$data = preg_replace("/....print\n/", "", $data);
	return $data;
}

/**
 * Spits out the unban/remove penalty button
 *
 * @param string $pen_id - id of the penalty to remove
 * @param string $cid - client_id of the client the penalty is against
 * @param string $type - the type of penalty it is
 * @param string $inactive - whether the penalty is active or not
 * @return string
 */
function unbanButton($pen_id, $cid, $type, $inactive) {
	
	$token = genFormToken('unban'.$pen_id); // gen form token with appened penalty id in order to make all the tokens unique

	// if pen is a tempban, ban or warning and it is still active then show unban
	if( ($type == 'TempBan' || $type == 'Ban' || $type == 'Warning') && ($inactive == 0) ) {
		return '<form method="post" action="actions/b3/unban.php" class="unban-form">
			<input type="hidden" name="token" value="'.$token.'" />
			<input type="hidden" name="cid" value="'.$cid.'" />
			<input type="hidden" name="banid" value="'.$pen_id.'" />
			<input type="hidden" name="type" value="'.$type.'" />
			<input type="image" value="Unban" name="unban-sub" src="app/assets/images/delete.png" title="De-Activate / Unban" />
		</form>';
	} else {
		return null;
	}

}

/**
 * Spits out the edit ban button
 *
 * @param string $type - the type of penalty it is
 * @param string $pen_id - id of the penalty to remove
 * @param string $inactive - whether the penalty is active or not
 * @return string
 */
function editBanButton($type, $pen_id, $inactive) {

	if( ($inactive == 0) && ($type == 'TempBan' || $type == 'Ban') ) { // if ban is active and the penalty is a Ban or Tempban show link
		return '<a onclick="editBanBox(this)" rel="'.$pen_id.'" class="edit-ban" title="Edit ban id &ldquo;'.$pen_id.'&rdquo;"><img src="app/assets/images/edit.png" alt="[EB]" /></a>';
	} else { // else show nothing
		return NULL;
	}

}

/**
 * Generates a general hash with sha1 and md5
 *
 * @param string $unhashed_text - the text you would like to hash
 * @return string
 */
function genHash($unhashed_text) {
	$md5 = md5($unhashed_text); // get md5
	$hashed = sha1(SALT.$md5); // get hash of text plus salt in sha1

	return $hashed; // return the inputted text
}

/**
 * Generates a password
 *
 * @param string $input - the actual clear text password
 * @param string $salt - the salt with which to hash the password
 * @return string $pw - hashed form of salt and inputted text
 */
function genPW($input, $salt) {
	$data = $input.$salt;
	$pw = hash("sha256", $data); // sha256 hash the passsword and the salt for an irrevrsible hash
	return $pw;
}

/**
 * Generates a new password salt
 *
 * @return string $salt
 */
function genSalt($length = 12) {
	return randPass($length);
}

/**
 * Generate a random password or string
 *
 * @param int $count - lenght of the string
 * @return string
 */
function randPass($count) {  

	$pass = str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'); //shuffle
	
	$rand_num = mt_rand(0,5); // get rand num for the rand start of substr
	
	return substr($pass, $rand_num, $count); //returns the password  
}

/**
 * Detect an AJAX request
 *
 * @return bool
 */
function detectAJAX() {
	/* AJAX check  */
	return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	// This method is not full proof since all servers do not support the $_SERVER['HTTP_X_REQUESTED_WITH'] varible.
}

/**
 * Detect an AJAX MS Internet Explorer
 *
 * @return bool
 */
function detectIE() {
    return isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false);
}

/**
 * Checks if a user has attempted to login to many times or has been caught hacking the site
 */
function locked() {
	if($_SESSION['wrong'] >= 3 || $_SESSION['hack'] >= 3) : // if the user has three wrongs or three hack attempts 
		// logout the user, then add the IP of the user to the Blacklist
	
		global $dbl;
		global $mem;
		
		if($mem->loggedIn())
			Session::logout(); // if they are logged in log them out

		$ip = getRealIp(); // get users ip
		$dbl->blacklist($ip); // add top blacklist
		writeLog('Locked out automatically.');
		sendLocked();
		
	endif;
}

/**
 * Checks Blacklist for the users IP address and if banned send to locked
 */
function checkBL() {
	global $dbl;
	$ip = getRealIp(); // find real IP
	$result = $dbl->checkBlacklist($ip); // query db and check if ip is on list
	
	if($result) {// if on blacklist
		sendLocked(); // send to locked page
		exit;
	}
}

/**
 * Find how many login attempts the user has made
 */
function trys() { //
	$trys = '';

	if($_SESSION['wrong'] != 0)
		$trys .= '<div class="alert alert-danger" role="alert">
	  <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	  <span class="sr-only">Error:</span>
		You have used '.$_SESSION['wrong'].' of 3 attempts to login</div>';

	return $trys;
}

/**
 * Add a number to the wrong login attempt counter
 *
 * @param string $num - num to add to the wrong counter
 */
function wrong($num) { // add $num to number of already recorded wrong attempts
	$_SESSION['wrong'] = $_SESSION['wrong'] . $num;
}

/**
 * Add a number to the hacking attempt counter
 *
 * @param string $num - num to add to the hacking attempt counter
 */
function hack($num) {
	//$_SESSION['hack'] = $_SESSION['hack'] . $num;
}

/**
 * Set an error message that is to be sent to the user
 *
 * @param string $msg - the error message
 */
function set_error($msg) {
	$_SESSION['error'] = $msg;
}

/**
 * Set a sucess message to be sent to the user
 *
 * @param string $msg - the message
 */
function set_good($msg) {
	$_SESSION['good'] = $msg;
}

/**
 * Set a warning message to be sent to the user
 *
 * @param string $msg - the warning message
 */
function set_warning($msg) {
	$_SESSION['warning'] = $msg;
}

function css_file($name) {
	echo '<link href="app/assets/styles/'. $name. '.css" rel="stylesheet" type="text/css" />';
}

/**
 * Get the IP address of the current user
 *
 * @return string $ip - IP address of the user
 */
function getRealIp() {
	if(!empty($_SERVER['HTTP_CLIENT_IP']))  // check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  // to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	else
		$ip = $_SERVER['REMOTE_ADDR'];
	return $ip;
}

/**
 * Check is a form var is empty if so set error and send back to reffering page
 *
 * @param string $var - the varible to check
 * @param string $field - the name of the varible (used in the error message) eg. 'your new password'
 */
function emptyInput($var, $field) {
	if(empty($var)) {
		$ref = $_SERVER['HTTP_REFERER'];
		set_error('You must put something in the '.$field.' field.');
		send($ref); // send back to referering page
	}
} // end function

/**
 * Check is a form var is empty, but accept 0's, if so set error and send back to reffering page
 *
 * @param string $var - the varible to check
 * @param string $field - the name of the varible (used in the error message) eg. 'your new password'
 */
function emptyInputNumeric($var, $field) {
	if(!isset($var)) {
		set_error('You must put something in the '.$field.' field.');
		$ref = $_SERVER['HTTP_REFERER'];
		send($ref); // send back to referering page
	}
} // end function

/**
 * Cleans var of unwanted materials
 *
 * @param string $var - var to be cleaned
 * @return string
 */
function cleanvar($var) {
	return trim(htmlentities(strip_tags($var), ENT_COMPAT | ENT_HTML401));
} // end clean var

/**
 * Send a user back to the reffering page with an error
 *
 * @param string $error - the error message that will be sent to the user
 */
function sendBack($error) {
	$ref = $_SERVER['HTTP_REFERER'];
	set_error($error);
	send($ref); // send back to referering page
	exit; // end script
}

/**
 * Send a user back to the reffering page with a sucess msg
 *
 * @param string $good - sucess message to be sent to the user
 */
function sendGood($good) {
	$ref = $_SERVER['HTTP_REFERER'];
	set_good($good);
	send($ref); // send back to referering page
	exit; // end script
}

/**
 * Send user to a given page
 *
 * @param string $where - page to send user to
 */
function send($where) {
	header("Location: {$where}");
}

/**
 * Send user to login page
 */
function sendLogin() { 
	header("Location: ".PATH."login");
}

/**
 * send to the locked page
 */
function sendLocked() {
	header("Location: ".PATH."error/?t=locked");
}

/**
 * Send to home page
 */
function sendHome() {
	header("Location: ".PATH);
}

/**
 * Send to the error page
 * @param null $add
 * @param null $message
 */
function sendError($add = NULL, $message = null) {
    if ( $message != NULL )
        header("Location: ".PATH."error?m=".base64_encode($message));
    else {
        if ($add == NULL)
            header("Location: " . PATH . "error.php");
        else
            header("Location: " . PATH . "error?t={$add}");
    }
}

/**
 * Handy tooltip creation function
 * Deprecated: $float
 */
function tooltip($msg, $float = false) {

	echo '<a class="tooltips" data-toggle="tooltip" title="'.$msg.'"></a>';
}

/**
 * Echo out simple clientdetails link
 */
function clientLink($name, $id, $game_id = NULL) {
	if(!empty($game_id))
		$href = '&amp;game='.$game_id;

	return '<a href="client?id='.$id.$href.'" title="Check out '.$name.' client information profile">'.$name.'</a>';
}


function guidLink($mem, $game, $guid) {
	$guid_len = strlen($guid);
	if($guid_len == 0) {
		return '(There is no GUID availible)';
	}
	elseif($mem->reqLevel('view_full_guid')) { // if allowed to see the full guid
		if(guidCheck($game, $guid))
			if( $game == 'cod' || $game = 'cod2' || $game = 'cod4' )
				return $guid;
			else
				return '<a data-toggle="tooltip" class="external " href="http://www.punksbusted.com/cgi-bin/membership/guidcheck.cgi?guid='.$guid.'" title="'.$guid.'>'.substr($guid, $guid_len / -2).'</a>';
		else
			return '<span class="red" title="This guid is invalid!">'.$guid.'</span>';
	}
	elseif($mem->reqLevel('view_half_guid')) { // if allowed to see the last half of guid, don't link  GUID look up site as it would give full GUID
		if(guidCheck($game, $guid))
			return substr($guid, $guid_len / -2);
		else
			return $guid.' <span class="red" title="This guid is only ' . $guid_len . ' characters long, it should be 32 characters!">['. $guid_len .']</span>';
	}
	else { // if not allowed to see any part of the guid
		return '(You do not have access to see the GUID)';
	}
}


function guidCheck($game, $guid) {
	switch($game) {
		case 'q3a':
		case 'oa081':
		case 'iourt41':
		case 'smg':
		case 'smg11':
		case 'etpro':
		case 'q3a':
			return preg_match('/^[A-F0-9]{32}$/', $guid);
			break;
		case 'moh':
		case 'bfbc2':
			return preg_match('/^EA_[a-f0-9]{32}$/i', $guid);
			break;
		case 'alt':
			return preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $guid);
			break;
		case 'cod':
		case 'cod2':
			return (strlen($guid) >= 6);
			break;
		case 'cod4':
			return (strlen($guid) >= 32);
			break;
		case 'cod5':
			return (strlen($guid) >= 9);
			break;
		case 'cod6':
			return (strlen($guid) >= 16);
			break;
		case 'cod7':
			return (strlen($guid) >= 5);
			break;
		default:
			return true;
	}
}

/**
 * parse IP address into link to ipwhois
 *
 * @param string $ip - ip address to use in link
 * @return string $msg - the link to whois of IP
 */
function ipLink($ip) {
	return '<a href="http://whois.domaintools.com/'.$ip.'/" class="external" title="WhoIs IP Search this User">'.$ip.'</a>';
}

/**
 * parse Email address into a mailto user link
 *
 * @param string $email - email address for link
 * @param string $name - name of the person
 * @return string $msg - the link to whois of IP
 */
function emailLink($email, $name) {
	if($name == '') // if name is not set make name the same as email
		$name = $email;

	return '<a href="mailto:'.$email.'" title="Send '.$name.' an email">'.$email.'</a>';
}

/**
 * Parse vars in a view user in more details link
 *
 * @param string $id - id of the user
 * @param string $name - name of the person
 * @param null $name_title
 * @param null $name_box
 * @return string $msg - the link to user
 */
function echUserLink($id, $name, $name_title = NULL, $name_box = NULL) {

	if(empty($name_title))
		$name_title = $name;
		
	if(empty($name_box))
		$name_box = $name;

	$msg = '<a href="sa?t=user&amp;id='.$id.'" title="View '.$name_title.' in more detail">'.$name_box.'</a>';
	return $msg;
}

function echGroupLink($id, $name) {
	$msg = '<a href="sa?t=perms-group&amp;id='.$id.'" title="View group '.$name.' in more detail">'.$name.'</a>';
	return $msg;
}

function linkSort($keyword, $title) {

	$this_p = cleanvar($_SERVER['PHP_SELF']);
	
	echo '<a title="Sort information by '.$title.' ascending." href="?ob='.$keyword.'&amp;o=ASC"><img src="app/assets/images/asc.png" width="10" height="6" alt="ASC" class="asc-img" /></a>
			&nbsp;
			<a title="Sort information by '.$title.' descending." href="?ob='.$keyword.'&amp;o=DESC"><img src="app/assets/images/desc.png" width="10" height="6" alt="DESC" class="desc-img" /></a>';

}

function linkSortType($keyword, $title, $t) {

	$this_p = cleanvar($_SERVER['PHP_SELF']);
	
	echo '<a title="Sort information by '.$title.' ascending." href="?ob='.$keyword.'&amp;o=ASC&amp;t='.$t.'"><img src="app/assets/images/asc.png" width="10" height="6" alt="ASC" class="asc-img" /></a>
			&nbsp;
			<a title="Sort information by '.$title.' descending." href="?ob='.$keyword.'&amp;o=DESC&amp;t='.$t.'"><img src="app/assets/images/desc.png" width="10" height="6" alt="DESC" class="desc-img" /></a>';

}

function linkSortClients($keyword, $title, $is_search, $search_type, $search_string) {

	$this_p = cleanvar($_SERVER['PHP_SELF']);
	
	if($is_search == false) :
		echo'<a title="Sort information by '.$title.' ascending." href="?ob='.$keyword.'&amp;o=ASC"><img src="app/assets/images/asc.png" width="10" height="6" alt="ASC" class="asc-img" /></a>
			&nbsp;
		<a title="Sort information by '.$title.' descending." href="?ob='.$keyword.'&amp;o=DESC"><img src="app/assets/images/desc.png" width="10" height="6" alt="DESC" class="desc-img" /></a>';
	else:
		echo'<a title="Sort information by '.$title.' ascending." href="?ob='.$keyword.'&amp;o=ASC&amp;s='.urlencode($search_string).'&amp;t='.$search_type.'"><img src="app/assets/images/asc.png" width="10" height="6" alt="ASC" class="asc-img" /></a>
			&nbsp;
		<a title="Sort information by '.$title.' descending." href="?ob='.$keyword.'&amp;o=DESC&amp;s='.urlencode($search_string).'&amp;t='.$search_type.'"><img src="app/assets/images/desc.png" width="10" height="6" alt="DESC" class="desc-img" /></a>';
	endif;

}

/**
 * Removes colour coding from a text string
 *
 * @param string $text - the text to clean
 * @return string - the cleaned text
 */
function removeColorCode($text) {
	return preg_replace_callback('/\\^([0-9])/', function(  ){ return ""; }, $text);
}

/**
 * Cleans/Escapes data for use in tables
 *
 * @param string $text - the text to clean
 * @return string - the cleaned/escaped text
 */
function tableClean($text) {
	return htmlspecialchars($text);
}

function timeExpire($time_expire, $type, $inactive) {

	global $instance;
	$time = time();

	if (($time_expire <= $time) && ($time_expire != -1)) {
		$msg = "<span class=\"p-expired\">".date($instance->config['time-format'], $time_expire)."</span>";

	} elseif ($time_expire == '-1') {
		$msg = "<span class=\"p-permanent\">Permanent</span>";

	} elseif ($time_expire > $time) {
		$msg = "<span class=\"p-active\">".date($instance->config['time-format'], $time_expire)."</span>";
	}

	if ($type == 'Kick') {
		$msg = "<em>(Kick Only)</em>";

	} elseif ($type == 'Notice'){
		$msg = "<span class=\"p-inactive\">Notice</span>";

	} elseif ($inactive == "1") {
		$msg = "<span class=\"p-inactive\">De-activated</span>";

	}
	
	if($msg == '') // if we got nothing then return unknown
		return '<em>(Unknwon)</em>';

	return $msg;
}

function timeExpirePen($time_expire) {
	global $instance;

	$msg = NULL;
	if (($time_expire <= time()) && ($time_expire != -1))
		$msg = "<span class=\"p-expired\">".date($instance->config['time-format'], $time_expire)."</span>";
	
	if ($time_expire == -1)
		$msg = "<span class=\"p-permanent\">Permanent</span>"; 
	
	if ($time_expire > time())
		$msg = "<span class=\"p-active\">".date($instance->config['time-format'], $time_expire)."</span>";
	
	return $msg;
}

/**
 * Get a penalty duration from your number and time frame
 *
 * @param string $time - time frame
 * @param int $duration - duration
 * @return int
 */
function penDuration($time, $duration) {

	if($time == 'h') // if time is in hours
		$durations = $duration*60;
	elseif($time == 'd') // time in days
		$durations = $duration*60*24;
	elseif($time == 'w') // time in weeks
		$durations = $duration*60*24*7;
	elseif($time == 'mn') // time in months (lets just say 30 days to a month)
		$durations = $duration*60*24*30;
	elseif($time == 'y') // time in years
		$durations = $duration*60*24*365;
	else // default time to mintues
		$durations = $duration;

	return $durations;
}

/**
 * Echelon logging function 
 */
function echLog($type, $message, $code = NULL, $traces = NULL) {

	if(empty($message))
		$message = 'There was an error of some sort';

 	if (file_exists(ECH_LOG) )
		$f = @fopen(ECH_LOG,'a');	
	else
		$f = @fopen(ECH_LOG,'w');
	
	// open the log file for appending
	if( $f ) : // returns false on error
		
		switch($type) {
			case 'mysql':
				$type_msg = 'MYSQL ERROR';
				break;
			
			case 'mysqlconnect':
				$type_msg = 'MYSQL CONNECTION ERROR';
				break;
				
			case 'hack':
				$type_msg = 'HACK ATTEMPT';
				break;
				
			case 'error':
			default:
				$type_msg = 'ERROR';
				break;
		}
		
		// construct the log message
		$log_msg = "-------\n".date("[Y-m-d H:i:s]") . $type_msg;
		
		if(isset($code) && !empty($code))
			$log_msg .=	" - Code: $code -" ;
			
		$log_msg .=	" Message: $message\n";
		if(!empty($traces))
			$log_msg .= "#Trace: \n" . $traces. "\n";
	
		// write the log message
		fwrite($f, $log_msg);
		
		// close the file connection
		fclose($f);
		
		return true;
	else:
		die('Couldn\'t find the Echelon Log at: '. ECH_LOG);
		
	endif;

}

/**
 * Send an email about a possible hack to the admin
 *
 * @param string $where - where the event happened
 */
function writeLog($where) {
    
	$ip = getRealIp(); // Get the IP from superglobal
	$host = gethostbyaddr($ip);    // Try to locate the host of the attack
	
	// create a logging message with php heredoc syntax
	$logging = <<<LOGMSGG
	There was a hacking attempt,.
	IP-Adress: {$ip}
	Host of Attacker: {$host}
	Point of Attack: {$where}
LOGMSGG;
// Awkward but LOG must be flush left
	
	// log the message
	echLog('hack', $logging); 
	
}

/**
 * Check if the suppled token is valid
 *
 * @param string $from - the form name
 * @param string $tokens - the server-side tokens array
 * @return bool
 */
function verifyFormToken($form, $tokens) {
        
	// check if a session is started and a token is transmitted, if not return an error
	// check if the form is sent with token in it
	// compare the tokens against each other if they are still the same
	if(isset($tokens[$form]) && isset($_POST['token']) && $tokens[$form] === $_POST['token']) 
		return true;
	return false;
}

/**
 * Same as above function but slight chnage to account for some login form differences
 */
function verifyFormTokenLogin($form) {
	// check if a session is started and a token is transmitted, if not return an error
	// check if the form is sent with token in it
	// compare the tokens against each other if they are still the same
    if(isset($_SESSION['tokens'][$form]) && isset($_POST['token']) && $_SESSION['tokens'][$form] === $_POST['token'])
		return true;
	return false;
}

/**
 * Generate and set a form token
 *
 * @set session vars
 * @param $form
 * @return bool
 */
function genFormToken($form) {
    
	// generate a token from an unique value, taken from microtime, you can also use salt-values, other crypting methods...
	$token = genHash(uniqid(microtime(), true));  
	
	// Write the generated token to the session variable to check it against the hidden field when the form is sent
	$_SESSION['tokens'][$form] = $token; 
	
	return $token;
}

/**
 * What to do if a bad token is found
 *
 * @param string $place - place this happened
 */
function ifTokenBad($place) {
	hack(1); // plus 1 to hack counter
	writeLog($place.' - Bad Token'); // make note in log
	sendBack('Hack Attempt Detected - If you continue you will be removed from this site');
	exit;
}

/**
 * Echos out all the different types of error/sucess/warning messages
 */
function errors() {
	if(empty($_SESSION['good']) && empty($_SESSION['error']) && empty($_SESSION['warning']) )
		return "";

	$type = "info";
	$message = "";
    if($_SESSION['good'] != '') {
        $message .= '<strong>Success!</strong> '.$_SESSION['good'];
		$type = "success";
        $_SESSION['good'] = '';
    }
	
    if($_SESSION['error'] != '') {
        $message .= '<strong>Error!</strong> '.$_SESSION['error'];
		$type = "danger";
        $_SESSION['error'] = '';
    }
	
	if($_SESSION['warning'] != '') {
        $message .= '<strong>Warning!</strong> '.$_SESSION['warning'];
		$type = "warning";
        $_SESSION['warning'] = '';
    }

	echo '<div class="alert alert-'.$type.' alert-dismissible" role="alert">',
			'<button type="button" class="close" data-dismiss="alert" aria-label="Close">',
			'<span aria-hidden="true">&times;</span>',
		'</button>',
			$message,
		"</div>";

    return "";
}

/**
 * Detect an SSL connection
 *
 * @return bool
 */
function detectSSL(){
	if($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 || $_SERVER['SERVER_PORT'] == 443)
		return true;
	return false;
}

/**
 * A function for making time periods readable
 *
 * @link        http://aidanlister.com/2004/04/making-time-periods-readable/
 * @param       int     number of seconds elapsed
 * @param       string  which time periods to display
 * @param       bool    whether to show zero time periods
 * @return 		string 	the human readable time
 */
function time_duration($seconds, $use = null, $zeros = false) {

	if($seconds == '') {
		return NULL;
	}
	
    // Define time periods
    $periods = array (
        'years'     => 31556926,
        'Months'    => 2629743,
        'weeks'     => 604800,
        'days'      => 86400,
        'hours'     => 3600,
        'minutes'   => 60,
        'seconds'   => 1
        );

    // Break into periods
    $seconds = (float) $seconds;
    foreach ($periods as $period => $value) {
        if ($use && strpos($use, $period[0]) === false) {
            continue;
        }
        $count = floor($seconds / $value);
        if ($count == 0 && !$zeros) {
            continue;
        }
        $segments[strtolower($period)] = $count;
        $seconds = $seconds % $value;
    }

    // Build the string
    foreach ($segments as $key => $value) {
        $segment_name = substr($key, 0, -1);
        $segment = $value . ' ' . $segment_name;
        if ($value != 1) {
            $segment .= 's';
        }
        $array[] = $segment;
    }

    return implode(', ', $array);
}


/**
 * DEPRECATED
 * Read current version of Echelon from master server
 *
 * @return	string	contents of that page
 */
function getEchVer(){

	$c = file_get_contents(VER_CHECK_URL."?need=version&channel=".VER_CHANNEL);
	if(!$c) {
		return false;
	} else {
		$string = cleanvar($c);
		return $string;
	}
	
}

// TODO: Test this function.
function hasUpdate() {
	$updateSource = file_get_contents(VER_CHECK_URL."?need=version&channel=".VER_CHANNEL);
	if(!$updateSource)
		return false;

	$responseData = json_decode($updateSource);
	return $responseData;
}


// TODO: All this needs to be refactored
/**
 * Simple isPage() functions
 */
function isHome() {
	global $page;

	if($page == 'home')
		return true;
	return false;
}

function isClients() {
	global $page;

	if($page == 'client')
		return true;
	return false;
}

function isCD() {
	global $page;

	if($page == 'clientdetails')
		return true;
	return false;
}

function isLogin() {
	global $page;

	if($page == 'login')
		return true;
	return false;
}

function isError() {
	global $page;

	if($page == 'error')
		return true;
	return false;
}


function isSettings() {
	global $page;

	if($page == 'settings')
		return true;
	return false;
}

function isSettingsGame() {
	global $page;

	if($page == 'settings-game')
		return true;
	return false;
}

function isSettingsServer() {
	global $page;
	
	if($page == 'settings-server')
		return true;
	return false;
}

function isSA() {
	global $page;

	if($page == 'sa')
		return true;
	return false;
}

function isPerms() {
	global $page;

	if($page == 'perms')
		return true;
	return false;
}

function isMe() {
	global $page;

	if($page == 'me')
		return true;
	return false;
}

function isPubbans() {
	global $page;

	if($page == 'pubbans')
		return true;
	return false;
}

function isMap() {
	global $page;

	if($page == 'map')
		return true;
	return false;
}
