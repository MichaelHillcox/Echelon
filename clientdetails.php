<?php
$page = "clientdetails";
$page_title = "Client Details";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = false; // this page requires the pagination part of the footer
require 'inc.php';

## Do Stuff ##
if($_GET['id'])
	$cid = $_GET['id'];

if(!isID($cid)) :
	set_error('The client id that you have supplied is invalid. Please supply a valid client id.');
	send('clients.php');
endif;

if($cid == '') {
	set_error('No user specified, please select one');
	send('clients.php');
}

## Get Client information ##
$query = "SELECT c.ip, c.connections, c.guid, c.name, c.mask_level, c.greeting, c.time_add, c.time_edit, c.group_bits, g.name
		  FROM clients c LEFT JOIN groups g ON c.group_bits = g.id WHERE c.id = ? LIMIT 1";
$stmt = $db->mysql->prepare($query) or die('Database Error '. $db->mysql->error);
$stmt->bind_param('i', $cid);
$stmt->execute();
$stmt->bind_result($ip, $connections, $guid, $name, $mask_level, $greeting, $time_add, $time_edit, $group_bits, $user_group);
$stmt->fetch();
$stmt->close();

## Require Header ##
$page_title .= ' '.$name; // add the clinets name to the end of the title

require 'app/views/global/header.php';
?>
<div class="row">
<div class="col-md-3">
	<div id="clientInfo" class="panel panel-default">
		<div class="panel-heading"><h3 class="name"><?= tableClean($name) ?></h3></div>

		<div class="profileItem">
			<div class="title">@B3ID</div>
			<div class="body"><?= $cid ?></div>
		</div>
		<div class="profileItem">
			<div class="title">Level</div>
			<div class="body"><?= ($user_group == NULL) ? 'Un-registered' : $user_group ?></div>
		</div>
		<div class="profileItem">
			<div class="title">Connections</div>
			<div class="body"><?= $connections ?></div>
		</div>
		<div class="profileItem">
			<div class="title">IP Address</div>
			<div class="body">
				<?php
				$ip = tableClean($ip);
				if($mem->reqLevel('view_ip')) :
					if ($ip != "") { ?>
						<a href="clients.php?s=<?php echo $ip; ?>&amp;t=ip" title="Search for other users with this IP address"><?php echo $ip; ?></a>
						<a href="http://www.geoiptool.com/en/?IP=<?php echo $ip; ?>" title="Show Location of IP origin on map"><img src="app/assets/images/globe.png" width="16" height="16" alt="L" /></a>
						<?php
					} else {
						echo "(No IP address available)";
					}
				else:
					echo '(You do not have access to see the IP address)';
				endif; // if current user is allowed to see the player's IP address
				?>
			</div>
		</div>

		<div class="profileItem">
			<div class="title">First Seen</div>
			<div class="body"><?= date($tformat, $time_add) ?></div>
		</div>
		<div class="profileItem">
			<div class="title">Last Seen</div>
			<div class="body"><?= date($tformat, $time_edit) ?></div>
		</div>
		<div class="profileItem">
			<div class="title">GUID</div>
			<div class="body"><?= guidLink($mem, $config['game']['game'], $guid) ?></div>
		</div>
	</div>

	<?php
		## Plugins Client Bio Area ##

		if(!$no_plugins_active) {
			$data = $plugins->displayCDBio();

			foreach ($data as $plugin):
				echo '<div class="panel panel-default">';
					echo '<div class="panel-heading">'.$plugin["title"].'</div>';
					echo $plugin["content"];
				echo '</div>';
			endforeach;
		}
	?>
</div>

<!-- Start Echelon Actions Panel -->
<div class="col-md-9">
	<div class="page-header clean">
		<h1>Actions</h1>
	</div>
	<div id="actions">
		<ul id=dictionTabs" class="nav nav-tabs">
			<?php // Oh yeah, This is why I hate php. I remember now! ?>
			<?php if($mem->reqLevel('comment')) { 			?>
				<li class="active">
					<a title="Add a comment to this user" rel="cd-act-comment" class="cd-tab">Comment</a>
				</li>
			<?php } ?>
			<?php if($mem->reqLevel('greeting')) { 			?>
				<li>
					<a title="Edit this user's greeting" rel="cd-act-greeting" class="cd-tab">Greeting</a>
				</li>
			<?php } ?>
			<?php if($mem->reqLevel('ban')) { 				?>
				<li>
					<a title="Add Ban/Tempban to this user" rel="cd-act-ban" class="cd-tab">Ban</a>
				</li>
			<?php } ?>
			<?php if($mem->reqLevel('edit_client_level')) { ?>
				<li>
					<a title="Change this user's user level" rel="cd-act-lvl" class="cd-tab">Change Level</a>
				</li>
			<?php } ?>
			<?php if($mem->reqLevel('edit_mask')) { 		?>
				<li>
					<a title="Change this user's mask level" rel="cd-act-mask" class="cd-tab">Mask Level</a>
				</li>
			<?php } ?>
			<?php
				if(!$no_plugins_active)
					$plugins->displayCDFormTab();
			?>
		</ul>
		<div id="actions-box" class="spacer">
			<?php
				if($mem->reqLevel('comment')) :
				$comment_token = genFormToken('comment');
			?>
			<div id="cd-act-comment" class="act-slide" style="display: block;">

				<form action="actions/b3/comment.php" method="post">
					<input type="hidden" name="token" value="<?php echo $comment_token; ?>" />
					<input type="hidden" name="cid" value="<?php echo $cid; ?>" />

					<div class="form-group">
						<label for="comment">Comment:</label>
						<textarea title="comment" class="form-control" name="comment" rows="3"></textarea>
					</div>

					<div class="form-group">
						<button type="submit" name="comment-sub" class="btn btn-default">Add Comment</button>
					</div>
				</form>
			</div>
			<?php
				endif;
				if($mem->reqLevel('greeting')) :
				$greeting_token = genFormToken('greeting');
			?>
			<div id="cd-act-greeting" class="act-slide">
				<form action="actions/b3/greeting.php" method="post">
					<div class="form-group">
						<label for="greeting">Greeting Message:</label>
						<textarea title="greeting" class="form-control" name="greeting" rows="3"><?php echo $greeting; ?></textarea>
					</div>

					<div class="form-group">
						<button type="submit" name="greeting-sub" class="btn btn-default">Edit Greeting</button>
					</div>

					<input type="hidden" name="token" value="<?php echo $greeting_token; ?>" />
					<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
				</form>
			</div>
			<?php
				endif;
				if($mem->reqLevel('ban')) :
				$ban_token = genFormToken('ban');
			?>
			<div id="cd-act-ban" class="act-slide">
				<form action="actions/b3/ban.php" method="post">

					<div class="checkbox">
						<label>
							<input type="checkbox" name="pb" id="pb" />
							Permanent Ban?
						</label>
						<?php tooltip('Is this ban to last forever?'); ?>
					</div>

					<div id="ban-duration">
						<label for="duration">Duration:</label>
						<div class="form-group form-inline">
							<input title="duration" type="text" class="form-control" name="duration" />
							<select class="form-control" name="time">
								<option value="m">Minutes</option>
								<option value="h">Hours</option>
								<option value="d">Days</option>
								<option value="w">Weeks</option>
								<option value="mn">Months</option>
								<option value="y">Years</option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label for="reason">Reason:</label>
						<input class="form-control" type="text" name="reason" id="reason" />
					</div>

					<div class="form-group">
						<button type="submit" name="ban-sub" class="btn btn-danger">Ban User</button>
					</div>

					<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
					<input type="hidden" name="c-name" value="<?php echo $name; ?>" />
					<input type="hidden" name="c-ip" value="<?php echo $ip; ?>" />
					<input type="hidden" name="c-pbid" value="<?php echo $guid; ?>" />
					<input type="hidden" name="token" value="<?php echo $ban_token; ?>" />
				</form>
			</div>
			<?php
				endif; // end hide ban section to non authed
				$b3_groups = $db->getB3Groups(); // get a list of all B3 groups from the B3 DB

				if($mem->reqLevel('edit_client_level')) :
				$level_token = genFormToken('level');
			?>
			<div id="cd-act-lvl" class="act-slide">
				<form action="actions/b3/level.php" method="post">
					<div class="form-group">
						<label for="level">Level:</label>
						<select class="form-control" name="level" id="level">
							<?php
							foreach($b3_groups as $group) :
								$gid = $group['id'];
								$gname = $group['name'];
								if($group_bits == $gid)
									echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
								else
									echo '<option value="'.$gid.'">'.$gname.'</option>';
							endforeach;
							?>
						</select>
					</div>

					<div id="level-pw" class="form-group">
						<label for="password">Your Current Password:</label>
						<input class="form-control" type="password" name="password" id="password" />

					</div>

					<div class="form-group">
						<input type="submit" name="level-sub" class="btn btn-default" value="Change Level" />
					</div>
					<input type="hidden" name="old-level" value="<?php echo $group_bits; ?>" />
					<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
					<input type="hidden" name="token" value="<?php echo $level_token; ?>" />
				</form>
			</div>
			<?php
				endif; // end if
				if($mem->reqLevel('edit_mask')) :
				$mask_lvl_token = genFormToken('mask');
			?>
			<div id="cd-act-mask" class="act-slide">
				<form action="actions/b3/level.php" method="post">
					<div class="form-group">
						<label for="mlevel">Mask Level:</label>
						<select class="form-control" name="level" id="mlevel">
							<?php
								foreach($b3_groups as $group) :
									$gid = $group['id'];
									$gname = $group['name'];
									if($mask_level == $gid)
										echo '<option value="'.$gid.'" selected="selected">'.$gname.'</option>';
									else
										echo '<option value="'.$gid.'">'.$gname.'</option>';
								endforeach;
							?>
						</select>
					</div>

					<input type="hidden" name="old-level" value="<?php echo $group_bits; ?>" />
					<input type="hidden" name="cid" value="<?php echo $cid; ?>" />
					<input type="hidden" name="token" value="<?php echo $mask_lvl_token; ?>" />
					<button type="submit" name="mlevel-sub" class="btn btn-default">Change Mask</button>
				</form>
			</div>
			<?php
				endif;

				## Plugins CD Form ##
				if(!$no_plugins_active)
					$plugins->displayCDForm($cid)

			?>
		</div><!-- end #actions-box -->
	</div><!-- end #actions -->

	<div class="page-header clean">
		<h1>Logs</h1>
	</div>

	<ul id="actionNav" class="nav nav-tabs">
		<li class="active"><a data-relation="aliases">Aliases</a></li>
		<li><a data-relation="ipAliases">IP Aliases</a></li>
		<li><a data-relation="echelonLogs">Echelon Logs</a></li>
		<li><a data-relation="penalties">Penalties</a></li>
		<li><a data-relation="adminActions">Admin Actions</a></li>
	</ul>

	<div id="actionContent">

		<div class="" data-relation="aliases">
			<?php
				// notice on the query we say that time_add does not equal time_edit, this is because of bug in alias recording in B3 that has now been solved
				$query = "SELECT alias, num_used, time_add, time_edit FROM aliases WHERE client_id = ? ORDER BY time_edit DESC";
				$stmt = $db->mysql->prepare($query) or die('Alias Database Query Error'. $db->mysql->error);
				$stmt->bind_param('i', $cid);
				$stmt->execute();
				$stmt->bind_result($alias, $num_used, $time_add, $time_edit);

				$stmt->store_result(); // needed for the $stmt->num_rows call

				if($stmt->num_rows) :
					?>
			<table  class="table table-striped table-hover">
				<thead>
				<tr>
					<th>Alias</th>
					<th>Times Used</th>
					<th>First Used</th>
					<th>Last Used</th>
				</tr>
				</thead>
				<tfoot>
				<tr><th colspan="4"></th></tr>
				</tfoot>
				<tbody>
				<?php
					while($stmt->fetch()) :

						$time_add = date($tformat, $time_add);
						$time_edit = date($tformat, $time_edit);

						$alter = alter();

						$token_del = genFormToken('del'.$id);

						// setup heredoc (table data)
						$data = <<<EOD
						<tr class="$alter">
							<td><strong>$alias</strong></td>
							<td>$num_used</td>
							<td><em>$time_add</em></td>
							<td><em>$time_edit</em></td>
						</tr>
EOD;
						echo $data;

					endwhile; ?>
				</tbody>
			</table>
		</div>
				<?php
				else : // if there are no aliases connected with this user then put out a small and short message

					echo '<div class="spacer alert alert-info" role="alert">'.$name.' has no aliaises.</div>';

				endif;
			?>


		<?php
			//this is sub optimal, but without a better way to check b3 version...
			$result = $db->query("SHOW TABLES LIKE 'ipaliases'");
			if($result["num_rows"]): ?>

		<div class="hidden" data-relation="ipAliases">

			<?php
				// notice on the query we say that time_add does not equal time_edit, this is because of bug in alias recording in B3 that has now been solved
				$query = "SELECT ip, num_used, time_add, time_edit FROM ipaliases WHERE client_id = ? ORDER BY time_edit DESC";
				$stmt = $db->mysql->prepare($query) or die('IP Alias Database Query Error'. $db->mysql->error);
				$stmt->bind_param('i', $cid);
				$stmt->execute();
				$stmt->bind_result($ip, $num_used, $time_add, $time_edit);

				$stmt->store_result(); // needed for the $stmt->num_rows call

				if($stmt->num_rows) :?>
			<table class="table table-striped table-hover">
				<thead>
				<tr>
					<th>IP</th>
					<th>Times Used</th>
					<th>First Used</th>
					<th>Last Used</th>
				</tr>
				</thead>
				<tfoot>
				<tr><th colspan="4"></th></tr>
				</tfoot>
				<tbody>
				<?php
					while($stmt->fetch()) :

						$time_add = date($tformat, $time_add);
						$time_edit = date($tformat, $time_edit);

						$alter = alter();

						$token_del = genFormToken('del'.$id);

						// setup heredoc (table data)
						$data = <<<EOD
						<tr class="$alter">
							<td><a href="clients.php?s=$ip"><strong>$ip</strong></a></td>
							<td>$num_used</td>
							<td><em>$time_add</em></td>
							<td><em>$time_edit</em></td>
						</tr>
EOD;
					echo $data;
				endwhile; ?>
				</tbody>
			</table>
			<?php else :
				// if there are no aliases connected with this user then put out a small and short message
				echo '<div class="spacer alert alert-info" role="alert">.'.$name.' has no other IP\'s .</div>';
			endif;
			?>
		</div>
		<?php endif; ?>


		<?php
		## Get Echelon Logs Client Logs (NOTE INFO IN THE ECHELON DB) ##
		$ech_logs = $dbl->getEchLogs($cid, $game);

		$count = count($ech_logs);
		if($count > 0) : // if there are records ?>
			<table data-relation="echelonLogs" class="hidden table table-striped table-hover">
				<thead>
				<tr>
					<th>id</th>
					<th>Type</th>
					<th>Message</th>
					<th>Time Added</th>
					<th>Admin</th>
				</tr>
				</thead>
				<tfoot>
				<tr><th colspan="5"></th></tr>
				</tfoot>
				<tbody>
				<?php displayEchLog($ech_logs, 'client'); ?>
				</tbody>
			</table>
			<?php
		endif; // end hide is no records
		?>

		<table data-relation="penalties" class="hidden table table-striped table-hover">
			<thead>
			<tr>
				<th></th>
				<th>Type</th>
				<th>Added</th>
				<th>Duration</th>
				<th>Expires</th>
				<th>Reason</th>
				<th>Admin</th>
			</tr>
			</thead>
			<tfoot>
			<tr><td colspan="7"></td></tr>
			</tfoot>
			<tbody id="contain-pen">
			<?php
			    fetchPenalties('client' );
			?>
			</tbody>
		</table>


		<table data-relation="adminActions" class="hidden table table-striped table-hover">
			<thead>
			<tr>
				<th></th>
				<th>Type</th>
				<th>Added</th>
				<th>Duration</th>
				<th>Expires</th>
				<th>Reason</th>
				<th>Client</th>
			</tr>
			</thead>
			<tfoot>
			<tr><td colspan="7"></td></tr>
			</tfoot>
			<tbody id="contain-admin">
			<?php
			fetchPenalties('admin' );

			?>
			</tbody>
		</table>

	</div> <!-- end actionContent -->

	<?php
	// Were the incompatibility begins
	// TODO: document this change
	if(!$no_plugins_active)
		$plugins->displayCDlogs($cid);
	?>
</div>
</div>

<?php

function fetchPenalties( $type_inc ) {

	if(empty($type_inc))
		$type_inc = 'client';

	if($type_inc == 'client')
		$query = "SELECT p.id, p.type, p.time_add, p.time_expire, p.reason, p.data, p.inactive, p.duration, 
	COALESCE(c.id,'1') as admin_id, COALESCE(c.name, 'B3') as admin_name 
	FROM penalties p LEFT JOIN clients c ON c.id = p.admin_id WHERE p.client_id = ? ORDER BY id DESC";
	else
		$query = "SELECT p.id, p.type, p.time_add, p.time_expire, p.reason, p.data, p.inactive, p.duration, 
	COALESCE(c.id,'1') as admin_id, COALESCE(c.name, 'B3') as admin_name 
	FROM penalties p LEFT JOIN clients c ON c.id = p.client_id WHERE p.admin_id = ? ORDER BY id DESC";

	global $mem, $type, $time_expire, $reason, $duration, $db, $tformat, $cid, $pid, $time_add, $data, $inactive, $admin_id, $admin_name;

	$stmt = $db->mysql->prepare($query) or die('<tr class="table-good"><td colspan="7"><span>Problem getting records from the database</span></td></tr>');
	$stmt->bind_param('i', $cid); // bind in the client_id for the query
	$stmt->execute(); // run query
	$stmt->store_result(); // store the result - needed for the num_rows check
	if($stmt->num_rows) : // if results exist
		$stmt->bind_result($pid, $type, $time_add, $time_expire, $reason, $data, $inactive, $duration, $admin_id, $admin_name);
		while($stmt->fetch()) : // fetcht the results and store in an array
			// Change into readable times
			$time_add = date($tformat, $time_add);

			$time_expire_read = timeExpire($time_expire, $type, $inactive);
			$reason = tableClean(removeColorCode($reason));
			$data = tableClean($data);
			$admin_name = tableClean($admin_name);

			if($type_inc!= 'Kick' && $type_inc!= 'Notice' && $time_expire != '-1')
				$duration = time_duration($duration*60, 'yMwdhm'); // all penalty durations are stored in minutes, so multiple by 60 in order to get seconds
			else
				$duration = '';

			// Row odd/even colouring
			$alter = alter();

			if($admin_id != 1) // if admin is not B3 show clientdetails link else show just the name
				$admin_link = '<a href="clientdetails.php?id='.$admin_id.'" title="View the client\'s page">'.$admin_name.'</a>';
			else
				$admin_link = $admin_name;

			if($mem->reqLevel('unban')) // if user has access to unban show unban button
				$unban = unbanButton($pid, $cid, $type, $inactive);
			else
				$unban = '';

			if($mem->reqLevel('edit_ban')) // if user  has access to edit bans show the button
				$edit_ban = editBanButton($type, $pid, $inactive);
			else
				$edit_ban = '';

			$row = <<<EOD
		<tr class="$alter">
			<td>$pid<br /> $unban $edit_ban</td>
			<td>$type</td>
			<td>$time_add</td>
			<td>$duration</td>
			<td>$time_expire_read</td>
			<td>$reason<br /><em>$data</em></td>
			<td>$admin_link</td>	
		</tr>
EOD;
			echo $row;
		endwhile;
	else : // if no results
		if($type_inc == 'client')
			echo '<tr class="table-good"><td colspan="7"><span>This user has no recorded penalties!</span></td></tr>';
		else
			echo '<tr class="table-good"><td colspan="7"><span>This user has no recorded admin actions!</span></td></tr>';
	endif;
	$stmt->close();
}

$customPageScripts = <<< EOT
<script src="app/assets/js/jquery.colorbox-min.js"></script>
<script src="app/assets/js/cd.js"></script>
<script>
	$('#level-pw').hide();

	// check for show/hide PW required for level change
	if ($('#level').val() >= {$config['cosmos']['pw_req_level_group']}) {
		$("#level-pw").show();
	}
	$('#level').change(function(){
		if ($('#level').val() >= 64) {
			$("#level-pw").slideDown();
		} else {
			$("#level-pw").slideUp();
		}
	});
</script>
EOT;

// Close page off with the footer
require 'app/views/global/footer.php';
?>
