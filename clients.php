<?php
$page = "client";
$page_title = "Clients Listing";
$auth_name = 'clients';
$b3_conn = true; // this page needs to connect to the B3 database
$pagination = true; // this page requires the pagination part of the footer
$query_normal = true;
require 'app/bootstrap.php';

##########################
######## Varibles ########

## Default Vars ##
$orderby = "id";
$order = "ASC";

$is_search = false;

## Sorts requests vars ##
if($_GET['ob'])
	$orderby = addslashes($_GET['ob']);

if($_GET['o'])
	$order = addslashes($_GET['o']);

// allowed things to sort by
$allowed_orderby = array('id', 'name', 'connections', 'group_bits', 'time_add', 'time_edit');
// Check if the sent varible is in the allowed array 
if(!in_array($orderby, $allowed_orderby))
	$orderby = 'id'; // if not just set to default id

## Page Vars ##
if ($_GET['p'])
  $page_no = addslashes($_GET['p']);

$start_row = $page_no * $limit_rows;

## Search Request handling ##
if($_GET['s']) {
	$search_string = addslashes($_GET['s']);
	$is_search = true; // this is then a search page
}

if($_GET['t']) {
	$search_type = $_GET['t']; //  no need to escape it will be checked off whitelist
	$allowed_search_type = array('all', 'name', 'alias', 'pbid', 'ip', 'id', 'guid');
	if(!in_array($search_type, $allowed_search_type))
		$search_type = 'all'; // if not just set to default all
}


###########################
######### QUERIES #########

$query = "SELECT c.id, c.name, c.connections, c.time_edit, c.time_add, c.group_bits, g.name as level
	FROM clients c LEFT JOIN groups g
	ON c.group_bits = g.id WHERE c.id != 1 ";

if($is_search == true) : // IF SEARCH
        $search_string = trim($search_string);
	if($search_type == 'name') { // name
		$query .= "AND c.name LIKE '%$search_string%' ORDER BY $orderby";
		
	} elseif($search_type == 'alias') { // alias this one requires an extra join so its a different query
		$query = "SELECT c.id, c.name, c.connections, c.time_edit, c.time_add, c.group_bits, a.alias, g.name as level
		FROM clients c INNER JOIN aliases a ON c.id = a.client_id LEFT JOIN groups
		g ON c.group_bits = g.id WHERE a.alias LIKE '%$search_string%' AND c.id != 1 ORDER BY $orderby";
		
	} elseif($search_type == 'id') { // ID
                $search_id = $search_string;
                if(substr($search_id, 0, 1) == '@')
                  $search_id = substr($search_id, 1);
		$query .= "AND c.id = '$search_id' ORDER BY $orderby";
		
	} elseif($search_type == 'pbid') { // PBID
		$query .= "AND c.pbid LIKE '%$search_string%' ORDER BY $orderby";
		
	} elseif($search_type == 'ip') { // IP
		// $query = "SELECT c.id, c.name, c.connections, c.time_edit,
		//   c.time_add, c.group_bits, ipa.ip, g.name as level FROM
		//   clients c INNER JOIN ipaliases ipa ON c.id = ipa.client_id LEFT
		//   JOIN groups g ON c.group_bits = g.id WHERE (c.ip LIKE '%$search_string%' OR ipa.ip LIKE '%$search_string%') AND c.id != 1 ORDER BY $orderby";
		$query .= "AND c.ip LIKE '%$search_string%' ORDER BY $orderby";
	} elseif($search_type == 'guid') {

		$query .= "AND c.guid LIKE '%$search_string%' ORDER BY $orderby";
	}
	else
	{
		// ALL again a modified query as all is responsible for checking aliases
                $search_id = $search_string;
                if(substr($search_id, 0, 1) == '@')
                  $search_id = substr($search_id, 1);
		$query .= "AND (c.name LIKE '%$search_string%' OR c.pbid LIKE '%$search_string%' OR c.ip LIKE '%$search_string%' OR c.id = '$search_id')
			ORDER BY $orderby";
	}
else : // IF NOT SEARCH
	$query .= sprintf("ORDER BY %s ", $orderby);

endif; // end if search request

## Append this section to all queries since it is the same for all ##
if($order == "DESC")
	$query .= " DESC"; // set to desc 
else
	$query .= " ASC"; // default to ASC if nothing adds up

$query_limit = sprintf("%s LIMIT %s, %s", $query, $start_row, $limit_rows); // add limit section

## Require Header ##	
require 'app/views/global/header.php';

if(!$db->error) :
?>

<fieldset class="search form-inline">
	<div class="page-header">
		<h1>Clients</h1>
		<p>
			<?php
			if($search_type == "all")
				echo 'You are searching all clients that match <span class="badge">'.$search_string.'</span> there are <span class="badge">'. $total_rows .'</span>.';
			elseif($search_type == 'name')
				echo 'You are searching all clients names for <span class="badge">'.$search_string.'</span> there are <span class="badge">'. $total_rows .'</span>.';
			elseif($search_type == 'alias')
				echo 'You are searching all clients aliases for <span class="badge">'.$search_string.'</span> there are <span class="badge">'. $total_rows .'</span>.';
			elseif($search_type == 'pbid')
				echo 'You are searching all clients Punkbuster Guids for <span class="badge">'.$search_string.'</span> there are <span class="badge">'. $total_rows .'</span>.';
			elseif($search_type == 'id')
				echo 'You are searching all clients B3 IDs for <span class="badge">'.$search_string.'</span> there are <span class="badge">'. $total_rows .'</span>.';
			elseif($search_type == 'ip')
				echo 'You are searching all clients IP addresses for <span class="badge">'.$search_string.'</span> there are <span class="badge">'. $total_rows .'</span>.';
			else
				echo 'A list of all players who have ever connected to the server.';
			?>
		</p>
	</div>
	<form action="clients.php" method="get" class="spacer" id="c-search">
		<div class="form-group">
			<div class="input-group">
				<div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
				<input type="text" class="form-control" type="text" autocomplete="off" name="s" id="search" onkeyup="suggest(this.value);" onBlur="fill();" value="<?php echo $search_string; ?>" />
			</div>

			<div class="suggestionsBox" id="suggestions" style="display: none;">
				<div class="suggestionList" id="suggestionsList">&nbsp;</div>
			</div>

			<select class="form-control" name="t">
				<option value="all" <?php if($search_type == "all") echo 'selected="selected"' ?>>All Records</option>
				<option value="name" <?php if($search_type == "name") echo 'selected="selected"' ?>>Name</option>
				<option value="alias" <?php if($search_type == "alias") echo 'selected="selected"' ?>>Alias</option>
				<option value="pbid" <?php if($search_type == "pbid") echo 'selected="selected"' ?>>PBID</option>
				<option value="ip" <?php if($search_type == "ip") echo 'selected="selected"' ?>>IP Address</option>
				<option value="id" <?php if($search_type == "id") echo 'selected="selected"' ?>>Player ID</option>
				<option value="guid" <?php if($search_type == "guid") echo 'selected="selected"' ?>>Player GUID</option>
			</select>

			<input type="submit" class="btn btn-primary" id="sub-search" value="Search" />
			<img src="app/assets/images/indicator.gif" alt="Loading...." title="We are searching for posible matches, please wait" id="c-s-load" />
		</div>
	</form>
</fieldset>

<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th>Name
				<?php linkSortClients('name', 'Name', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Client-id
				<?php linkSortClients('id', 'Client-id', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Level
				<?php linkSortClients('group_bits', 'Level', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Connections
				<?php linkSortClients('connections', 'Connections', $is_search, $search_type, $search_string); ?>
			</th>
			<th>First Seen
				<?php linkSortClients('time_add', 'First Seen', $is_search, $search_type, $search_string); ?>
			</th>
			<th>Last Seen
				<?php linkSortClients('time_edit', 'Last Seen', $is_search, $search_type, $search_string); ?>
			</th>
			<?php if($search_type == 'alias') echo('<th>Alias Matched</th>');?>
		</tr>
	</thead>
	<tbody>
	<?php
	if($num_rows > 0) : // query contains stuff
	 
		foreach($data_set as $client): // get data from query and loop
			$cid = $client['id'];
			$name = $client['name'];
			$level = $client['level'];
			$connections = $client['connections'];
			$time_edit = $client['time_edit'];
			$time_add = $client['time_add'];
			$alias = $client['alias'];
			$time_add = date($tformat, $time_add);
			$time_edit = date($tformat, $time_edit);
			
			$alter = alter();
				
			$client = clientLink($name, $cid);
			
			
			// setup heredoc (table data)			
			if($search_type != 'alias') :
			$data = <<<EOD
			<tr class="$alter">
				<td><strong>$client</strong></td>
				<td>@$cid</td>
				<td>$level</td>
				<td>$connections</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
			</tr>
EOD;
			else :
			$data = <<<EOD
				<tr class="$alter">
				<td><strong>$client</strong></td>
				<td>@$cid</td>
				<td>$level</td>
				<td>$connections</td>
				<td><em>$time_add</em></td>
				<td><em>$time_edit</em></td>
				<td>$alias</td>
			</tr>
EOD;
			endif;

		echo $data;
		endforeach;
	else :
		$no_data = true;
	
		echo '<tr class="odd"><td colspan="6">';
		if($is_search == false)
			echo 'There are no clients in the database.';
		else
			echo 'Your search for <strong>'.$search_string.'</strong> has returned no results.';
		echo '</td></tr>';
	endif; // no records
	?>
	</tbody>
</table>

<?php
	endif; // db error

	require 'app/views/global/footer.php';
?>
