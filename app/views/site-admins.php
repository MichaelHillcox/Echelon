<?php

function delUserLink($id, $token) {

    if($_SESSION['user_id'] == $id) // user cannot delete themselves
        return NULL;
    else
        return '<form action="actions?req=user-edit" method="post" class="user-del">
				<input type="hidden" value="'.$token.'" name="token" />
				<input type="hidden" value="'.$id.'" name="id" />
				<input type="hidden" value="del" name="t" />
				<input class="harddel" type="image" src="assets/images/user_del.png" alt="Delete" title="Delete this user forever" />
			</form>';

}

$page = "sa";
$page_title = "Site Adminisration";

// Setup
$type = isset($_GET['t']) ? $_GET['t'] : false;
$is_edit_user = false;
$is_view_user = false;
$is_permissions = false;
$is_perms_group = false;
$is_perms_group_add = false;

if($type) {
	if($type == 'perms' OR $type == 'perms-group' OR $type == 'perms-add')
		$auth_name = 'edit_perms';
		
	elseif($type == 'user')
		$auth_name = 'siteadmin';
		
	elseif($type == 'edituser')
		$auth_name = 'edit_user';
		
} else {
	$auth_name = 'siteadmin';
	
}

## Require the inc files and start up class ##
require ROOT.'app/bootstrap.php';

// If this is a view a user in more detail page
if($type == 'user') :
	$id = $_GET['id'];
	if(!isID($id)) {
		set_error('Invalid data sent. Request aborted.');
		send('site-admins');
	}
	
	## Get a users details
	$result = $dbl->getUserDetails($id);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error("That user doesn't exist, please select a real user");
		send('site-admins');
		exit;
	} else {
		## Setup information vars ##
		$username = $result[0];
		$display = $result[1];
		$email = $result[2];
		$ip = $result[3];
		$group = $result[4];
		$admin_id = $result[5];
		$first_seen = $result[6];
		$last_seen = $result[7];
		$admin_name = $result[8];
	}
	
	$ech_logs = $dbl->getEchLogs($id, NULL, 'admin'); // get the echelon logs created by this user (note: admin_id is admin group not the id stored in log)
	
	$token_del = genFormToken('del'.$id);

	$is_view_user = true;
endif; // end 

// if this is an edit user page
if($type == 'edituser') :
	if(!isID($_GET['id'])) {
		set_error('Invalid data sent. Request aborted.');
		send('site-admins');
	} else
		$uid = $_GET['id'];
	
	## Get a users details
	$result = $dbl->getUserDetailsEdit($uid);
	if(!$result) { // there was no user matching the sent id // throw error and sedn to SA page
		set_error('No user matches that id.');
		send('site-admins');
		exit;
	} else {
		## Setup information vars ##
		$u_username = $result[0];
		$u_display = $result[1];
		$u_email = $result[2];
		$u_group_id = $result[3];
	}
	
	// setup form token
	$ad_edit_user_token = genFormToken('adedituser');
	
	// get the names and id of all B3 Groups for select menu
	$ech_groups = $dbl->getGroups();
	
	// set referance var
	$is_edit_user = true;

endif;

## Permissions Setup ##
if($type == 'perms') :

	$is_permissions = true; // helper var
	$page = "perms";
	$page_title = "Echelon Group Management";

endif;

if($type == 'perms-group') :
	
	$group_id = cleanvar($_GET['id']);
	$group_id = (int)$group_id;
	$is_perms_group = true; // helper var
	
	$group_info = $dbl->getGroupInfo($group_id);

	$group_name = $group_info[0];
	$group_perms = $group_info[1];
	$page = "perms";
	$page_title = $group_name." Group";
	

endif;

if($type == 'perms-add') :

	$is_perms_group_add = true;

endif;

## Require Header ##	
require ROOT.'app/views/global/header.php';

if($is_edit_user) :

?>
    <nav aria-label="">
        <ul class="pager">
            <li class="previous"><?= echUserLink($uid, $u_display, null, '<span aria-hidden="true">&larr;</span> Go Back'); ?></li>
        </ul>
    </nav>

    <div class="page-header">
        <h1>Edit <?php echo $u_display; ?></h1>
    </div>
		
    <form action="actions?req=user-edit" method="post" class="panel panel-default panel-info">
        <div class="panel-heading"><h3 class="panel-title">Edit</h3></div>
        <div class="panel-body">
            <div class="form-group">
                <label for="display">Display Name:</label>
                <input class="form-control" type="text"  name="display" id="display" value="<?php echo $u_display; ?>" />
            </div>

            <div class="form-group">
                <label for="username">Username:</label>
                <input class="form-control" type="text" name="username" id="username" value="<?php echo $u_username; ?>" />
             </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input class="form-control" type="text" name="email" id="email" value="<?php echo $u_email; ?>" />
            </div>

            <div class="form-group">
                <label for="group">Group</label>
                <select class="form-control" name="group" id="group">
                    <?php foreach($ech_groups as $group) :
                        if($group['id'] == $u_group_id)
                            echo '<option value="'.$group['id'].'" selected="selected">'.$group['display'].'</option>';
                        else
                            echo '<option value="'.$group['id'].'">'.$group['display'].'</option>';
                    endforeach; ?>
                </select>
            </div>

            <input type="hidden" name="token" value="<?php echo $ad_edit_user_token; ?>" />
            <input type="hidden" name="id" value="<?php echo $uid; ?>" />

            <input type="submit" class="btn btn-primary" name="ad-edit-user" value="Edit <?php echo $u_display; ?>" />
        </div>
    </form>

    <form action="actions?req=user-edit" method="post" class="panel panel-default panel-danger">
        <div class="panel-heading">
            <h3 class="panel-title">Edit Password</h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="display">Password</label>
                <input class="form-control" type="password"  name="password" id="" />
            </div>

            <div class="form-group">
                <label for="username">Password Confirm</label>
                <input class="form-control" type="password" name="password-confirm" id="" />
            </div>

            <input type="hidden" name="token" value="<?php echo $ad_edit_user_token; ?>" />
            <input type="hidden" name="id" value="<?php echo $uid; ?>" />

            <input type="submit" class="btn btn-primary" name="ad-edit-user-password" value="Edit Password" />
        </div>
    </form>

<?php elseif($is_view_user) : ?>
    <nav aria-label="">
        <ul class="pager">
            <li class="previous"><a href="site-admins" title="Go back to site admin page" class="float-left"><span aria-hidden="true">&larr;</span> Site Admin</a></li>
        </ul>
    </nav>

	<span class="float-right"><span class="float-left"><?php echo delUserLink($id, $token_del)?></span><?= '<a href="site-admins?t=edituser&amp;id='.$id.'" title="Edit '. $username .'"><img src="assets/images/user_edit.png" alt="edit" /></a>' ?></span>
	
	<table class="user-table table table-striped table-hover">
		<caption><img src="assets/images/cd-page-icon.png" width="32" height="32" alt="" /><?php echo $display; ?><small>Everything Echelon knows about <?php echo $display; ?></small></caption>
		<tbody>
			<tr>
				<th>Name</th>
					<td><?php echo  tableClean($username); ?></td>
				<th>Display Name</th>
					<td><?php echo $display; ?></td>
			</tr>
			<tr>
				<th>Email</th>
					<td><?php echo emailLink($email, $display); ?></td>
				<th>IP Address</th>
					<td><?php echo ipLink($ip); ?></td>
			</tr>
			<tr>
				<th>First Seen</th>
					<td><?php echo date($instance->config['time-format'], $first_seen); ?></td>
				<th>Last Seen</th>
					<td><?php echo date($instance->config['time-format'], $last_seen); ?></td>
			</tr>
			<tr>
				<th>Creator</th>
					<td colspan="3"><?php echo echUserLink($admin_id, $admin_name); ?></td>
			</tr>
		</tbody>
	</table>
	

	<table class="table table-striped table-hover">
		<caption>Echelon Logs<small>created by <?php echo $display; ?></caption>
		<thead>
			<tr>
				<th>id</th>
				<th>Type</th>
				<th>Message</th>
				<th>Time Added</th>
				<th>Client</th>
				<th>Game</th>
			</tr>
		</thead>
		<tfoot>
			<tr><th colspan="5"></th></tr>
		</tfoot>
		<tbody>
			<?php displayEchLog($ech_logs, 'admin'); ?>
		</tbody>
	</table>
	
<?php elseif($is_permissions) : ?>

	<nav aria-label="">
		<ul class="pager">
			<li class="previous"><a href="site-admins" title="Go back to site admin page" ><span aria-hidden="true">&larr;</span> Site Admin</a></li>
			<li class="next"><a href="site-admins?t=perms-add" title="Add a new Echelon group">Add Group <span aria-hidden="true">&rarr;</span></a></li>
		</ul>
	</nav>

    <div class="page-header no-bottom">
        <h1>Groups</h1>
        <p>A list of all the Echelon Groups</p>
    </div>

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th>id</th>
				<th>Group Name</th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"></td>
			</tr>
		</tfoot>
		
		<tbody>
			<?php
				$ech_list_groups = $dbl->getGroups();
				
				$num_rows = count($ech_list_groups);
				
				if($num_rows > 0) :
					foreach($ech_list_groups as $group):
						$id = $group['id'];
						$name = $group['display'];
						
						$alter = alter();
						$name_link = echGroupLink($id, $name);
						
						// setup heredoc (table data)			
						$data = <<<EOD
						<tr class="$alter">
							<td>$id</td>
							<td><strong>$name_link</strong></td>
							<td>&nbsp;</td>
						</tr>
EOD;

						echo $data;
					endforeach;
				else:
				
					echo '<tr><td colspan="3">There are no groups in the Echelon database. <a href="site-admins?t=perms-add" title="Add a new group to Echelon">Add Group</a></td></tr>';
				
				endif;
			
			?>
		</tbody>
	</table>
	
<?php elseif($is_perms_group) : ?>

    <nav aria-label="">
        <ul class="pager">
            <li class="previous"><a href="site-admins?t=perms" title="Go back to permissions management homepage"><span aria-hidden="true">&larr;</span> Permissions</a></li>
        </ul>
    </nav>

    <div class="page-header no-bottom">
        <h1>Permissions for the <?php echo $group_name; ?> Group</h1>
        <p>Select groups permission set to specify which parts of Echelon the user can use</p>
    </div>

    <form action="actions?req=perms-edit&gid=<?php echo $group_id; ?>" method="post">

        <div class="form-group">
            <label for="g-name">Name of Group:</label>
            <input class="form-control" type="text" name="g-name" id="g-name" value="<?= $group_name ?>" />
        </div>

        <h3>Group Premissions</h3>
        <div class="perms-list">
            <?php
            $perms_token = genFormToken('perm-group-edit');
            $perms_list = explode(",", $group_perms);
            $add_g_token = genFormToken('perm-group-add');
            $perms = $dbl->getPermissions(); // gets a comprehensive list of Echelon groups

            foreach ($perms as $perm):
                $p_id = $perm['id'];
                $p_name = $perm['name'];
                $p_desc = $perm['desc'];

                $p_name_read = ucwords(str_replace('_', ' ', $p_name));

                if($p_id != ""):
                    echo '<div class="item"><label for="'. $p_name .'"><input id="'.$p_name.'" type="checkbox" ', (in_array($p_id, $perms_list) ? 'checked="checked"' : ''),' name="' . $p_name . '" />',
                    '<div class="desc">',
                        '<div class="name">'.$p_name_read.'</div>',
                        '<p>'.$p_desc.'</p>',
                    '</div>',
                    '</label>';
                    echo '</div>';
                endif;

            endforeach;

            ?>
        </div>
        <input type="hidden" name="token" value="<?php echo $perms_token; ?>" />
        <input type="hidden" name="og-name" value="<?php echo $group_name; ?>" />
        <input class="btn btn-primary" type="submit" value="Edit Group" />

    </form>
	
<?php elseif($is_perms_group_add) : ?>

    <nav aria-label="">
        <ul class="pager">
            <li class="previous"><a href="site-admins?t=perms" title="Go back to permissions management homepage"><span aria-hidden="true">&larr;</span> Permissions</a></li>
        </ul>
    </nav>

    <div class="page-header no-bottom">
        <h1>Add Echelon Group</h1>
        <p>Create a Groups permission set to specify which parts of Echelon the user can use</p>
    </div>
	
	<form action="actions?req=perms-edit&t=add" method="post">

        <div class="form-group">
            <label for="g-name">Name of Group:</label>
            <input class="form-control" type="text" name="g-name" id="g-name" />
        </div>

        <h3>Group Premissions</h3>
        <div class="perms-list">
		<?php
		
			$add_g_token = genFormToken('perm-group-add');
			$perms = $dbl->getPermissions(); // gets a comprehensive list of Echelon groups

            foreach ($perms as $perm):
					$p_id = $perm['id'];
					$p_name = $perm['name'];
					$p_desc = $perm['desc'];
					
					$p_name_read = ucwords(str_replace('_', ' ', $p_name));

					if($p_id != ""):
						echo '<div class="item"><label for="'. $p_name .'"><input id="'.$p_name.'" type="checkbox" name="' . $p_name . '" />',
                            '<div class="desc">',
                                '<div class="name">'.$p_name_read.'</div>',
                                '<p>'.$p_desc.'</p>',
                            '</div>',
                        '</label>';
						echo '</div>';
					endif;

			endforeach;
		
		?>
        </div>
		<input type="hidden" name="token" value="<?php echo $add_g_token; ?>" />
		<input class="btn btn-primary" type="submit" value="Add Group" />
	
	</form>
<?php else : ?>
<nav aria-label="" class="float-right">
	<ul class="pager">
		<li class="next"><a href="site-admins?t=perms" title="Manage Echelon User Permissions">User Permissions <span aria-hidden="true">&rarr;</span></a></li>
	</ul>
</nav>

<div class="page-header no-bottom">
	<h1>Echelon Users</h1>
	<p>A list of all people who can login to Echelon.</p>
</div>

<div id="management">
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Users</a></li>
		<li role="presentation"><a href="#add" aria-controls="add" role="tab" data-toggle="tab">Add Echelon User</a></li>
		<li role="presentation"><a href="#registration" aria-controls="reg" role="tab" data-toggle="tab">Registration Keys</a></li>
		<li role="presentation"><a href="#blacklist" aria-controls="blacklist" role="tab" data-toggle="tab">Echelon Blacklist</a></li>
		<li role="presentation"><a href="#addblacklist" aria-controls="addblacklist" role="tab" data-toggle="tab">Add to Blacklist</a></li>
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="home">
			<table class="table table-striped table-hover" summary="A list of people who have access to login to Echelon">
				<thead>
				<tr>
					<?php if(GRAVATAR) echo '<th></th>'; ?>
					<th>#</th>
					<th>Name</th>
					<th>Group</th>
					<th>Email</th>
					<th>IP Address</th>
					<th>First Seen</th>
					<th>Last Seen</th>
					<th>Tools</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<?php
					if(GRAVATAR)
						echo '<th colspan="9"></th>';
					else
						echo '<th colspan="8"></th>';
					?>
				</tr>
				</tfoot>
				<tbody>
				<?php
				$users_data = $dbl->getUsers();
				foreach($users_data['data'] as $users): // get data from query and loop
					$id = $users['id'];
					$name = $users['display'];
					$group = $users['namep'];
					$email = $users['email'];

					$time_add = date($instance->config['time-format'], $users['first_seen']);
					$time_edit = date($instance->config['time-format'], $users['last_seen']);
					$ip = ipLink($users['ip']);
					$email_link = emailLink($email, $name);

					if(GRAVATAR) // if use gravatar
						$grav = '<td>'.$mem->getGravatar($email).'</td>';

					$alter = alter();
					$token_del = genFormToken('del'.$id);
					$name_link = echUserLink($id, $name);
					$user_img_link = echUserLink($id, '<img src="assets/images/user_view.png" alt="view" />', $name);
					$user_edit_link = '<a href="site-admins?t=edituser&amp;id='.$id.'" title="Edit '. $name .'"><img src="assets/images/user_edit.png" alt="edit" /></a>';
					$user_del_link = delUserLink($id, $token_del);

					// setup heredoc (table data)
					$data = <<<EOD
			<tr class="$alter">
				$grav
				<td>$id</td>
				<td><strong>$name_link</strong></td>
				<td>$group</td>
				<td>$email_link</td>
				<td>$ip</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
				<td class="actions">
					$user_del_link
					$user_edit_link
					$user_img_link
				</td>
			</tr>
EOD;

					echo $data;
				endforeach;
				?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane spacer" id="add">
			<?php
			$ech_groups = $dbl->getGroups();
			$add_user_token = genFormToken('adduser');
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Add Echelon User</h3>
				</div>
				<div class="panel-body">
					<form action="actions?req=user-add" method="post">
						<div class="form-group">
							<div class="row">
								<div class="col-md-8">
									<label for="au-email">Email of User:</label>
									<input class="form-control" type="text" name="email" />
								</div>
								<div class="col-md-4">
									<label for="group">User Group:</label>
									<select class="form-control" name="group">
										<?php foreach($ech_groups as $group) :
											echo '<option value="'.$group['id'].'">'.$group['display'].'</option>';
										endforeach; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="au-comment">Comment:</label><br />
							<textarea name="comment" class="form-control" rows="6" ></textarea>
						</div>

						<button class="btn btn-primary" type="submit" name="add-user">Add User</button>

						<input type="hidden" name="token" value="<?php echo $add_user_token; ?>" />
					</form>
				</div>
			</div>
		</div>
		<div role="tabpanel" class="tab-pane spacer" id="registration">
			<h3>Registration Keys</h3>
			<p>A list of valid keys for Echelon registrations</p>
			<table class="table table-striped table-hover" summary="A list of valid keys for Echelon registration">
				<thead>
				<tr>
					<th>Registration Key</th>
					<th>Email <small>(assoc. with key)</small></th>
					<th>Admin</th>
					<th>Comment</th>
					<th>Added</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<th colspan="6"></th>
				</tr>
				</tfoot>
				<tbody>
				<?php
				$counter = 1;
				$keys_data = $dbl->getKeys($instance);

				$num_rows = $keys_data['num_rows'];

				if($num_rows > 0) :

					foreach($keys_data['data'] as $reg_keys): // get data from query and loop

						$reg_key = $reg_keys['reg_key']; // the reg key
						$comment = cleanvar($reg_keys['comment']); // comment about key
						$time_add = date($instance->config['time-format'], $reg_keys['time_add']);
						$email = emailLink($reg_keys['email'], '');
						$admin_link = echUserLink($reg_keys['admin_id'], $reg_keys['display']);

						$alter = alter();

						$token_keydel = genFormToken('keydel'.$reg_key);

						if($mem->id == $admin_id) // if the current user is the person who create the key allow the user to edit the key's comment
							$edit_comment = '<img src="" alt="[Edit]" title="Edit this comment" class="edit-key-comment" />';
						else
							$edit_comment = '';

						// setup heredoc (table data)
						$data = <<<EOD
			<tr class="$alter">
				<td class="key">$reg_key</td>
				<td>$email</td>
				<td>$admin_link</td>
				<td><span class="comment">$comment</span> $edit_comment</td>
				<td><em>$time_add</em></td>
				<td class="actions">
					<form action="actions?req=key-edit" method="post" id="regkey-del-$counter">
						<input type="hidden" value="$token_keydel" name="token" />
						<input type="hidden" value="$reg_key" name="key" />
						<input type="hidden" value="del" name="t" />
						<input type="submit" name="keydel" value="Delete" class="action del harddel" title="Delete this registraion key" />
					</form>
				</td>
			</tr>
EOD;

						echo $data;
						$counter++;
					endforeach;

				else:

					echo '<tr><td colspan="6">There are no registration keys active on file</td></tr></tr>';

				endif;
				?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane spacer" id="blacklist">
			<h3>Echelon Blacklist</h3>
			<p>A list of people banned from accessing this website.</p>
			<table class="table table-striped table-hover" summary="A list of people banned from accessing this website">
				<thead>
				<tr>
					<th>id</th>
					<th>IP Address</th>
					<th>Active</th>
					<th>Comment</th>
					<th>Admin</th>
					<th>Added</th>
					<th></th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<th colspan="7"></th>
				</tr>
				</tfoot>
				<tbody>
				<?php
				$bl_data = $dbl->getBL();
				$num_rows = $bl_data['num_rows'];

				if($num_rows > 0) :

					foreach($bl_data['data'] as $bl): // get data from query and loop
						$id = $bl['id'];
						$ip = $bl['ip'];
						$active = $bl['active'];
						$reason = $bl['reason'];
						$time_add = $bl['time_add'];
						$admin = $bl['admin'];

						$time_add = date($instance->config['time-format'], $time_add);
						$ip = ipLink($ip);

						$alter = alter();

						$token = genFormToken('act'.$id);

						if($active == 1) {
							$active = 'Yes';
							$actions = '<form action="actions?req=blacklist" method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="token" value="'.$token.'" />
						<input type="submit" name="deact" value="De-active" class="action del" title="De-active this ban" />
						</form>';
						} else {
							$active = 'No';
							$alter .= " inact";
							$actions = '<form action="actions?req=blacklist" method="post">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="token" value="'.$token.'" />
						<input type="submit" name="react" value="Re-active" class="action plus" title="Re-active this ban" />
						</form>';
						}

						unset($token);

						if($admin == '')
							$admin = 'Auto Added';

						// setup heredoc (table data)
						$data = <<<EOD
				<tr class="$alter">
					<td>$id</td>
					<td><strong>$ip</strong></td>
					<td>$active</td>
					<td>$reason</td>
					<td>$admin</td>
					<td><em>$time_add</em></td>
					<td>
						$actions
					</td>
				</tr>
EOD;

						echo $data;
					endforeach;

				else:

					echo '<tr><td colspan="7">There are no IPs on the blacklist</td></tr>';

				endif;
				?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane spacer" id="addblacklist">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Add to Blacklist</h3>
				</div>
				<div class="panel-body">
					<form action="actions?req=blacklist" method="post">
						<div class="form-group">
							<div class="row">
								<div class="col-md-4">
									<label for="bl-ip" class="ip-label">IP Address:</label>
									<input type="text" name="ip" placeholder="192.168.0.1" class="form-control" />
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="bl-reason">Reason:</label>
							<textarea rows="6" name="reason" class="form-control" placeholder="Enter a reason for this ban..."></textarea>
						</div>

						<?php $bl_token = genFormToken('addbl'); ?>
						<input type="hidden" name="token" value="<?php echo $bl_token; ?>" />
						<button class="btn btn-danger" type="submit">Ban IP Address</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>


<?php
    endif; // end if on what kind of page this is
	require ROOT.'app/views/global/footer.php';
?>