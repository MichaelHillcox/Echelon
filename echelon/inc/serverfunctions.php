<?php

/**
 * Sends an rcon comand to a q3a server
 *
 * @param string $rcon_ip - IP for rcon connections
 * @param string $rcon_port - Port for rcon connection
 * @param string $rcon_pass - Server rcon Password
 * @param string $command - The rcon command being sent
 * @return string
 */
function q3aRcon($rcon_ip, $rcon_port, $rcon_pass, $command) {

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
 * Sends an rcon comand to a frostbite server
 *
 * @param string $rcon_ip - IP for rcon connections
 * @param string $rcon_port - Port for rcon connection
 * @param string $rcon_pass - Server rcon Password
 * @param string $command - The rcon command being sent
 * @return string
 */
function frostbiteRcon($rcon_ip, $rcon_port, $rcon_pass, $command) {

	
}