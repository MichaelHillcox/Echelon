<?php
$page = "settings-server";
$page_title = "Server Settings";
$auth_name = 'manage_settings';
require 'inc.php';

// We are using the game information that was pulled in setup.php
$game_token = genFormToken('serversettings');

$page_type = 'none';
if($_GET['t'])
	$page_type = cleanvar($_GET['t']);

if($page_type == 'add') : ## if add a server page ##

	$token = genFormToken('addserver');

elseif($page_type == 'srv') : ## if edit a server page ##
	
	$server_id = cleanvar($_GET['id']);
	if($server_id == '') {
		set_error('No server id chosen, please choose a server');
		send('game-settings.php');
		exit;
	}
	
	$token = genFormToken('editserversettings');
	
	## get server information
	$server = $dbl->getServer($server_id);
	
else: ## if a normal list page ##

	## Default Vars ##
	$orderby = "id";
	$order = "ASC"; // either ASC or DESC

	## Sorts requests vars ##
	if($_GET['ob'])
		$orderby = addslashes($_GET['ob']);

	if($_GET['o'])
		$order = addslashes($_GET['o']);

	## allowed things to sort by ##
	$allowed_orderby = array('id', 'name', 'ip', 'pb_active');
	if(!in_array($orderby, $allowed_orderby)) // Check if the sent varible is in the allowed array 
		$orderby = 'id'; // if not just set to default id
	
	if($order == 'DESC')
		$order = 'DESC';
	else
		$order = 'ASC';
	
	## Get List ##
	if(!$no_servers) // if there are servers
		$servers = $dbl->getServerList($orderby, $order);
	
	## Find num of servers found ##
	if(!$servers) // if false
		$num_rows = 0;
	else
		$num_rows = count($servers);

endif;

require 'app/views/global/header.php';

if($num_games < 1) : ?>

	<h3>No Games Created</h3>
		<p>Please go to <a href="settings-games.php?t=add">Settings Games</a>, and add a game before you can add/edit any server settings</p>

<?php elseif($page_type == 'add') : ?>

	<div class="page-header no-bottom">
		<h1>Add Server</h1>
	</div>

	<nav aria-label="">
		<ul class="pager">
			<li class="previous"><a href="settings-server.php" title="Go back to the main server listing" ><span aria-hidden="true">&larr;</span> Server List</a></li>
		</ul>
	</nav>
	
	<form action="actions/settings-server.php" method="post">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Names & Game</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="name">Server Name:</label>
							<input type="text" name="name" class="form-control"  />
						</div>
						<div class="col-md-6">
							<label for="ip">IP Address:</label><?php tooltip('The public IP address of the server'); ?>
							<input type="text" name="ip" class="form-control" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="pb" id="pb" />
							Punkbuster&trade; Active?<?php tooltip('Is punkbuster running on this server?'); ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="game-id">Game:</label><?php tooltip('What game is this server to be connected with?'); ?>
					<select class="form-control" name="game-id" id="game-id">
						<?php
						$i = 0;
						$count = count($games_list);
						$count--; // minus 1
						while($i <= $count) :

							echo '<option value="'.$games_list[$i]['id'].'">'.$games_list[$i]['name'].'</option>';

							$i++;
						endwhile;
						?>
					</select>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Rcon Info</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-8">
							<label for="rcon-ip">Rcon IP:</label><?php tooltip('The IP used to connect to Rcon of this server'); ?>
							<input type="text" name="rcon-ip" class="form-control" />
						</div>
						<div class="col-md-4">
							<label for="rcon-port">Rcon Port:</label>
							<input type="number" class="form-control" name="rcon-port" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="rcon-pass">Rcon Password:</label>
					<input type="password" class="form-control" name="rcon-pass" />
				</div>
				<button class="btn btn-primary" type="submit" name="server-settings-sub"> Add Server</button>
			</div>
		</div>

		<input type="hidden" name="type" value="add" />
		<input type="hidden" name="cng-pw" value="on" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />

	</form>

<?php elseif($page_type == 'srv') : /* if edit server page */ ?>

	<div class="page-header no-bottom">
		<h1>Server Settings for <?php echo $server['name']; ?></h1>
	</div>

	<nav aria-label="">
		<ul class="pager">
			<li class="previous"><a href="settings-server.php" title="Go back to the main server listing"  ><span aria-hidden="true">&larr;</span> Server List</a></li>
			<li class="next"><a href="settings-server.php?t=add" title="Add a server">Add Server<span aria-hidden="true">&rarr;</span></a></li>
		</ul>
	</nav>

	<form action="actions/settings-server.php" method="post">

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Names & Game</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-6">
							<label for="name">Server Name:</label>
							<input type="text" name="name" class="form-control" value="<?php echo $server['name']; ?>" />
						</div>
						<div class="col-md-6">
							<label for="ip">IP Address:</label>
							<input type="text" name="ip" class="form-control" value="<?php echo $server['ip']; ?>" />
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="checkbox">
						<label >
							<input type="checkbox" name="pb" id="pb" <?php if($server['pb_active'] == 1) echo 'checked="checked"'; ?> />
							Punkbuster&trade; Active?<?php tooltip('Is punkbuster running on this server?'); ?>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="game-id">Game:</label><?php tooltip('What game is this server to be connected with?'); ?>
					<select class="form-control" name="game-id" id="game-id">
						<?php
						$i = 0;
						$count = count($games_list);
						$count--; // minus 1
						while($i <= $count) :

							echo '<option value="'.$games_list[$i]['id'].'">'.$games_list[$i]['name'].'</option>';

							$i++;
						endwhile;
						?>
					</select>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Rcon Info</h3>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<div class="row">
						<div class="col-md-8">
							<label for="rcon-ip">Rcon IP:</label>
							<input type="text" name="rcon-ip" class="form-control" value="<?php echo $server['rcon_ip']; ?>" />
						</div>
						<div class="col-md-4">
							<label for="rcon-port">Rcon Port:</label>
							<input type="number" class="form-control" name="rcon-port"  value="<?php echo $server['rcon_port']; ?>" /><br />
						</div>
					</div>
					<div class="checkbox">
						<label >
							<input type="checkbox" data-endis="password" data-default="active" name="cng-pw" />
							Change Rcon Password?
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="rcon-pass">Rcon Password:</label>
					<input type="password" data-endis-target="password" name="rcon-pass" class="form-control" />
				</div>
				<button class="btn btn-primary" type="submit" name="server-settings-sub">Save Server</button>
			</div>
		</div>

		<input type="hidden" name="type" value="edit" />
		<input type="hidden" name="token" value="<?php echo $token; ?>" />
		<input type="hidden" name="server" value="<?php echo $server_id; ?>" />


	</form>

<?php else : /* if normal list page type */ ?>

	<nav class="float-right" aria-label="">
		<ul class="pager">
			<li class="next"><a href="settings-server.php?t=add" title="Add a new server to Echelon DB">Add Server<span aria-hidden="true">&rarr;</span></a></li>
		</ul>
	</nav>

	<div class="page-header no-bottom">
		<h1>Servers</h1>
		<p>This is all the servers Echelon knows about, across all the games Echelon knows about</p>
	</div>

	<table class="table table-striped table-hover" summary="A list of game servers">
	<thead>
		<tr>
			<th>id
				<?php linkSort('id', 'id'); ?>
			</th>
			<th>Name
				<?php linkSort('name', 'Name'); ?>
			</th>
			<th>IP
				<?php linkSort('ip', 'Server IP'); ?>
			</th>
			<th>PB Enabled
				<?php linkSort('pb_active', 'Punkbuster Enabled Status'); ?>
			</th>
			<th>Game</th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th colspan="6"></th>
		</tr>
	</tfoot>
	<tbody>
		<?php
		if($num_rows > 0) : // query contains stuff
		 
			foreach($servers as $server): // get data from query and spit it out
				$id = $server['id'];
				$name = $server['name'];
				$game_id = $server['game'];
				$pb_active = $server['pb_active'];
				$ip = $server['ip'];
				$game_name = $server['game_name'];
				
				## row color
				$alter = alter();
				
				## Make it human readable
				if($pb_active == 1)
					$pb_active_read = '<span class="on">Yes</span>';
				else
					$pb_active_read = '<span class="off">No</span>';
					
				$ip_read = ipLink($ip);
				
				// set a warning that the active game has changed since the last page?
				if($game != $game_id)
					$warn = 'game';
				else
					$warn = '';
					
				$del_token = genFormToken('del-server'.$id);
			
				$table = <<<EOD
				<tr class="$alter">
					<td>$id</td>
					<td><strong><a href="settings-server.php?t=srv&amp;id=$id">$name</a></strong></td>
					<td>$ip_read</td>
					<td>$pb_active_read</td>
					<td><a href="settings-games.php?game=$game_id&amp;w=$warn" title="Edit the settings for $game_name">$game_name</a></td>
					<td>
						<a href="settings-server.php?t=srv&amp;id=$id"><img src="app/assets/images/edit.png" alt="[E]" /></a>
						<form style="display: inline;" method="post" action="actions/settings-server.php?t=del&amp;id=$id">
							<input type="hidden" name="token" value="$del_token" />
							<input class="harddel" type="image" title="Delete this Server" src="app/assets/images/delete.png" alt="[D]" />
						</form>
					</td>
				</tr>
EOD;

				echo $table;
			endforeach;

		else :
			echo '<tr class="odd"><td colspan="6">There are no servers would you like to <a href="settings-server.php?t=add" title="Add a new Server to Echelon DB">add a server</a>.</td></tr>';
		endif; // end if query contains
		?>
	</tbody>
	</table>

	<br />

<?php endif; // if no an empty id ?>

<?php require 'app/views/global/footer.php'; ?>