<?php
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'class.php' == basename($_SERVER['SCRIPT_FILENAME']))
  		die ('Please do not load this page directly. Thanks!');

/**
 * class chatlogs
 * desc: File to deal with Echelon plugin Chatlogger
 *
 */ 

class chatlogs extends Plugins {

	public static $instance;
	public $name;
	
	private function getClass() {
		$name =	get_class($this);
		$this->name = $name;
		return $name;
	}
	
	/**
	 *	You may edit below here
	 */
	
	// Next two vars need to have the same number of items, they should be in the same order aswell
	private $tables = 'chatlog';
	private $tables_names = 'Server 1';
	
	/**
	 * Gets the current instance of the class, there can only be one instance (this make the class a singleton class)
	 * note: this is needed as a work around for the inc.php file do not change
	 *
	 * @return object $instance - the current instance of the class
	 */
	public static function getInstance() {
        if (!(self::$instance instanceof self))
            self::$instance = new self();

        return self::$instance;
    }

	public function __construct() {
		parent::__construct($this->getClass()); // call the parent constructor (var: name)
		
		global $config; // get the site config
		global $game; // current game id
		
		$this->tables = explode(',', $config['cosmos']['chats_table_'.$game]);
		
		$this->tables_names = explode(',', $config['cosmos']['chats_names_'.$game]);	
	
		parent::setTitle('Chat logger');
		parent::setPagination(true);
		parent::setDescription("Shows an interactive view of all the servers with chat logging enabled");
		parent::setVersion(1.1);

		if(count($this->tables) != count($this->tables_names))
			parent::error('In your settings, there are not the same number of tables and table names listed.');
	
	} // end constructor
	
	public function __destruct() {
		parent::__destruct();
	}
	
	protected function getTables() {
		return $this->tables;
	}
	
	protected function getTablesNames() {
		return $this->tables_names;
	}

	/**
	 * Returns a list of chatlogs of the client
	 *
	 * @param int $cid - the client id of the user that we need the logs for
	 */
	public function returnClientLogs($cid) {
	
		$tables_info = $this->getTables(); // get the table information for the chatlogs queries
		$tables_names = $this->getTablesNames();
		
		global $tformat; // get the time format for use in the logs
	
		include 'chatlogs-cd.php'; // include the file
	}
	
	/**
	 * Returns the link to the needed in the nav for the full chatlogs page
	 */
	public function returnNav() {
	
		global $mem; // get pointer to the members class
		
		if($mem->reqLevel(__CLASS__)) :
		
			global $page; // bring in the current page var from main Echelon
			
			if($page == __CLASS__)
				$data = '<li class="n-chat selected">';
			else
				$data = '<li class="n-chat">';
			
			$data .= '<a href="'. PATH .'plugin.php?pl='.__CLASS__.'" title="Chatlogs from the server(s)">Chat Logs</a></li>';
		
			return $data;
		
		endif;
	
	}

    /**
     * Internal logic to get the page information
     *
     * @param $table_num
     * @return bool|null
     * @internal param string $table_name - name of the table to get records from (default to chatlog, plugin default)
     */
	private function pageLogic($table_num) {
        $table_name = "";

		if(empty($table_num)) {
			$table_name = 'chatlog';
			$table_num = 0;
		}
		
		$tables = $this->getTables();
		$tables_names = $this->getTablesNames();
		
		if(is_numeric($table_num))
			$table_name = $tables[$table_num];
		else
			return false;
	
		$db = B3Database::getPointer(); // get the db pointer
		
		$query = "SELECT id, msg_time, msg_type, client_id, client_name, client_team, msg 
				  FROM ". $table_name ." ORDER BY msg_time DESC LIMIT 100";
				  
		$results = $db->query($query); // run the query
		
		if($db->error) // if there is an error
			return NULL;

		return $results;
		
	} // edn pageLogic
	
	/**
	 * Return the fully formated page content for this plugin
	 */
	public function returnPage($table_num) {
	
		global $mem;
		global $config; // get the config servers data

		$logic = $this->pageLogic($table_num);
		
		if($logic == false)
			set_error('The chatlogs table you asked for does not exist, please select a real table.');
		elseif($logic == NULL)
			$db_error = true;
		
		if($db_error)
			return 'There was a database error in retrieving the chatlogs';
		
		## matching up tables
		$tables_names = $this->getTablesNames();
		$num_tables = count($tables_names);
		
		$content = '
			<div class="page-header">
				<h1>Chatlogger</h1>
				<p>A list of everything ever said in the servers</p>
			</div>
<div class="panel panel-default">
  <div class="panel-heading">Settings</div>
  <div class="panel-body">
		<fieldset class="search" id="chats-header" style="position: relative;">
			<form class="form-inline" action="plugin.php" method="get">
				<select class="form-control " name="v">';
		
				## select table
				$i = 0;
				while($i < $num_tables) :
				
					if($table_num == $i)
						$sel = 'selected="selected"';
					else
						$sel = NULL;
				
					$content .= '<option value="'. $i .'" '.$sel.'>'. $tables_names[$i] .'</option>';
					
					$i++;
				endwhile;
				
		$content .= '</select>
				<input type="hidden" name="pl" value="'.__CLASS__.'" />
				<input type="submit" class="btn btn-primary" value="Select" />
		
			</form>
			';
		
		if($mem->reqLevel('chats_talk_back')) :	
			$content .= '<form class="form-inline" action="'.PATH.'plugins/'.__CLASS__.'/actions.php" method="post" id="tb-form">
				<label class="chat-fh">Talk Back to the server:</label>
				<input class="form-control" type="text" name="talkback" id="talkback" />
				<select class="form-control" name="srv" id="tb-srv">';
				
				$i = 1;
				
				foreach($config['game']['servers'] as $server) :
				
					$content .= '<option value="'.$i.'">'.$server['name'].'</option>';
					
					$i++;
				
				endforeach;
			
			$content .=	'</select>
				<input type="submit" class="btn btn-primary" id="tb-sub" value="Talk Back" />
			</form>
	  ';
		endif;
		
		if($mem->reqLevel('chats_edit_tables')) :
			
			$content .= '
			<label class="chat-fh">Table Settings</label>
			<form class="form-inline" action="'.PATH.'plugins/'.__CLASS__.'/actions.php" method="post" id="c-settings">
			<small>Please specify each table seperate by a comma (eg. chatlog,chatlog2), and the same with the names. Put the corresponding names and tables in the same order.</small><br />
			
				<label id="tables">MySQL Table Names</label>
					<input type="text" name="tables" id="tables" class="form-control" value="'. implode(',', $this->getTables()) .'" />
				
				<label id="table-names">Name</label>
					<input type="text" name="table-names" id="table-names" class="form-control" value="'. implode(',', $this->getTablesNames()) .'" />
			
				<input type="submit" class="btn btn-primary" value="Edit Settings" />
			</form>';
			
		endif;
		
		$content .= '<span id="refreshcommand"></span></fieldset></div>
	</div>';
		
		if($logic['num_rows'] > 0) :
		
			if(empty($table_num))
				$table_num = 0;
		
			$content .= '
			<table class="table table-striped table-hover" id="chat" rel="'. $table_num .'">
				<thead>
					<tr>
						<th>id</th>
						<th>Name</th>
						<th>Type</th>
						<th>Message</th>
						<th>Time</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th colspan="5"></th>
					</tr>
				</tfoot>
				<tbody id="chatlog-body">';
		
		$content .= $this->buildLines($logic['data']);
			
		$content .= '</tbody></table>';
			
		else:
			$content .= 'There are no chatlog records in the selected table.';
		
		endif;
		
		return $content;
		
	} // end returnPage
	
	private function buildLines($data_set, $ani = false) {
	
		global $tformat; // get the standardised time format
		
		if($ani == 'tb')
			$ani = 'tb-row';
		elseif($ani == 'yes')
			$ani = 'animate';
		else
			$ani = '';

		if(count($data_set) > 0) :
	
		foreach($data_set as $data):
			$id = $data['id'];
			$msg_type = $data['msg_type'];
			$msg = cleanvar(removeColorCode($data['msg']));
			$client_link = clientLink($data['client_name'], $data['client_id']);
			$time_read = date($tformat, $data['msg_time']);
			
			## Highlight Commands ##
			if (substr($msg, 0,1) == '!' or substr($msg, 0,1) == '@')
				$msg = '<span class="chat-cmd">'. $msg ."</span>"; 
			
			$alter = alter();
	
			// setup heredoc (table data)			
			$data = <<<EOD
			<tr class="$alter $ani" id="$id">
				<td>$id</td>
				<td><strong>$client_link</strong></td>
				<td>$msg_type</td>
				<td>$msg</td>
				<td><em>$time_read</em></td>
			</tr>
EOD;

			$content .= $data;
			
		endforeach;
		
		endif;
		
		return $content;
	
	} // end buildLines
	
	private function getLastChatsDB($table_num, $id) {
	
		$db = B3Database::getPointer(); // get the db pointer
		
		$tables = $this->getTables(); // make an array from the tables list
		
		$table = $tables[$table_num]; // get the table from settings array
	
		$query = 'SELECT id, msg_time, msg_type, client_id, client_name, msg 
					FROM '. $table .' WHERE id > ? ORDER BY id DESC LIMIT 25';
					
		$stmt = $db->mysql->prepare($query) or die('Database Error'. $db->mysql->error);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		
		$stmt->bind_result($id, $msg_time, $msg_type, $client_id, $client_name, $msg);
		
		while($stmt->fetch()) :
		
			$data[] = array(
				'id' => $id,
				'msg_time' => $msg_time,
				'msg_type' => $msg_type,
				'client_id' => $client_id,
				'client_name' => $client_name,
				'msg' => $msg
			);
		
		endwhile;
		
		return $data;
	}
	
	public function getLastChats($table_num, $id) {
	
		$data = $this->getLastChatsDB($table_num, $id);
		
		return $this->buildLines($data, 'yes');
	}
	
	
	public function talkback($msg, $server_id, $last_id) {
	
		global $mem;
		global $config; // get the config servers data
	
		$talkback = cleanvar($msg);
		$srv_id = cleanvar($server_id);
	
		if($mem->reqLevel('chats_talk_back')) :
			if(!empty($talkback)) {
				
				// get the servers rcon password
				$rcon_pass = $config['game']['servers'][$srv_id]['rcon_pass'];
				$rcon_ip = $config['game']['servers'][$srv_id]['rcon_ip'];
				$rcon_port = $config['game']['servers'][$srv_id]['rcon_port'];
			
				$command = "say ^7(^3". $mem->name ."^7): ^2" . $talkback;
				$return = rcon($rcon_ip, $rcon_port, $rcon_pass, $command);
			} else
				sendBack('You left the message box empty, please fill in the box to send a message to the server');
		else :	
			sendBack('You do not have the correct privilages to talkback to the server');
		
		endif;
		
		$time = time();
		
		$data[] = array(
			'id' => $last_id,
			'msg_time' => $time,
			'msg_type' => 'TALKBACK',
			'client_id' => 0,
			'client_name' => $mem->name,
			'msg' => $msg
		);
		
		return $this->buildLines($data, 'tb');
	
	}
	/**
	 * Return the chats JS only on the chatlogs plugin page
	 */
	public function returnJS() {
	
		global $page; // get the current page name
	
		if($page == __CLASS__) // if this is the chatlogs page, load the JS
			return '<script src="'. PATH .'plugins/'.__CLASS__.'/chats.js"></script>';
		
	}
	
	public function editSettings($tables, $names) {
	
		global $game; // current game id
	
		$dbl = LegacyDatabase::getInstance(); // get Echelon db pointer
		$db = B3Database::getPointer(); // get B3 Db pointer
		
		$tables_array = explode(',', $tables);
		
		foreach($tables_array as $table) : // check each table exists
			$query = "SELECT id FROM $table LIMIT 1";
			
			if(!$stmt = $db->mysql->prepare($query)) // if table does not exist then prepare will fail
				return false; // if not return false
		endforeach;
		
		// Update the tables row
		$result = $dbl->updateSettings($tables, 'chats_table_'.$game, 's');
		if(!$result) {
			$result = $dbl->setSettings($tables, 'chats_table_'.$game, 's');
			if(!$result)
				return false;
		}
		
		// update the names row
		$result = $dbl->updateSettings($names, 'chats_names_'.$game, 's');
		if(!$result) {
			$result = $dbl->setSettings($names, 'chats_names_'.$game, 's');
			if(!$result)
				return false;
		}
			
		return true;
	}

} // end class