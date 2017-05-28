<?php
global $site_name, $page_title, $map_js, $no_plugins_active, $plugins, $mem, $no_game, $db_error, $limit_rows, $db;
## if the page has the normal query process & there is a connectionn to the B3 DB
if($query_normal && (!$db_error)) :
	$results = $db->query($query_limit);

	$num_rows = $results['num_rows']; // the the num_rows
	$data_set = $results['data']; // seperate out the return data set
endif;

## Pagination for pages with tables ## 
if($pagination == true && (!$db_error)) : // if pagination is needed on the page
	## Find total rows ##
	$total_num_rows = $db->query($query, false); // do not fetch the data
	$total_rows = $total_num_rows['num_rows'];
	
		$query_string_page = queryStringPage();
	
	// create query_string
	if($total_rows > 0) {

		$total_pages = totalPages($total_rows, $limit_rows);
		
		if($page_no > $total_pages) {
			$db->error = true;
			$db->error_msg = 'That page does not exists, please select a real page.';
		}
	} else
		$total_pages = 0;

endif;
?>
<!DOCTYPE html>
<html>

	<head>
		<title><?= $site_name ?> Echelon - <?= $page_title; ?></title>

		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="<?= PATH ?>app/assets/images/logo-dark.png" />

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="<?= PATH ?>app/assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?= PATH; ?>app/assets/styles/master.min.css" rel="stylesheet" media="screen" type="text/css" />

		<?php
		## Header JS for Map Page ##
		if(isMap())
			echo $map_js;

		// return any plugin CSS files
		if(!$no_plugins_active)
			$plugins->getCSS();

		?>
	</head>

	<body id="<?php echo $page; ?>">

		<nav id="navigation" class="navbar navbar-default">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#masterNav" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a id="logo" href="<?= PATH ?>"></a>
				</div>

				<div class="collapse navbar-collapse" id="masterNav">
					<?php if($mem->loggedIn()) : ?>
                        <ul class="nav navbar-nav">
                            <li <?php if(isHome()) echo ' class="active"'; ?>><a href="<?= PATH ?>">Home <span class="sr-only">(current)</span></a></li>
                            <?php
                                $games_list = $dbl->getActiveGamesList();
                                $count = count($games_list);
                                if($count > 0) : ?>
                                    <li class="dropdown">
                                        <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Games <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <?php
                                                $this_cur_page = basename($_SERVER['SCRIPT_NAME']);
                                                if(is_string(strstr($this_cur_page, '?'))) //hackey solution to allow plugin pages to encode vital information
                                                    $this_cur_page .= '&';
                                                else
                                                    $this_cur_page .= '?';

                                                foreach ( $games_list as $game ):
                                                    if($game_id == $game['id'])
                                                        echo '<li class="active">';
                                                    else
                                                        echo '<li>';
                                                    echo '<a href="'.PATH . $this_cur_page .'game='.$game['id'].'" title="Switch to this game">'.$game['name'].'</a></li>';
                                                endforeach;
                                            ?>
                                        </ul>
                                    </li>
                                <?php if($mem->reqLevel('clients')) : ?>
                                    <li class="dropdown">
                                        <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Clients <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">Clients</li>
                                            <li class="<?php if(isClients()) echo ' active'; ?>"><a href="<?php echo PATH; ?>clients.php" title="Clients Listing">Clients</a></li>
                                            <li class="<?php if($page == 'regular') echo ' active'; ?>"><a href="<?php echo PATH; ?>regular.php" title="Regular non admin visitors to your servers">Regular Visitors</a></li>
                                            <li class="<?php if($page == 'admins') echo ' active'; ?>"><a href="<?php echo PATH; ?>admins.php" title="A list of all admins">Admin Listing</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li class="dropdown-header">Admins</li>
                                            <li class="<?php if($page == 'active') echo ' active'; ?>"><a href="<?php echo PATH; ?>active.php" title="In-active admins">In-active Admins</a></li>
                                            <li class="<?php if(isMap()) echo ' active'; ?>"><a href="<?php echo PATH; ?>map.php" title="Player map">World Player Map</a></li>
                                        </ul>
                                    </li>
                                <?php endif;
                                if($mem->reqLevel('penalties')) : ?>
                                    <li class="dropdown">
                                        <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Penalties <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-header">In Game</li>
                                            <li class="<?php if($page == 'adminkicks') echo ' active'; ?>"><a href="<?php echo PATH; ?>kicks.php?t=a">Kicks</a></li>
                                            <li class="<?php if($page == 'adminbans') echo ' active'; ?>"><a href="<?php echo PATH; ?>bans.php?t=a">Bans</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li class="dropdown-header">B3</li>
                                            <li class="<?php if($page == 'b3kicks') echo ' active'; ?>"><a href="<?php echo PATH; ?>kicks.php?t=b" title="All kicks added automatically by B3">Kicks</a></li>
                                            <li class="<?php if($page == 'b3bans') echo ' active'; ?>"><a href="<?php echo PATH; ?>bans.php?t=b" title="All bans added automatically by B3">Bans</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li class="dropdown-header">All</li>
                                            <li class="<?php if(isPubbans()) echo ' active'; ?>"><a href="<?php echo PATH; ?>pubbans.php" title="A public list of bans in the database">Public Ban List</a></li>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                                <li class="dropdown">
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Other <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li class="dropdown-header">Misc</li>
                                        <li class="<?php if($page == 'notices') echo ' active'; ?>">
                                            <a href="<?php echo PATH; ?>notices.php" title="In-game Notices">Notices</a>
                                        </li>
                                        <li role="separator" class="divider"></li>
                                        <?php
                                        if(!$no_plugins_active) {
										    echo "<li class=\"dropdown-header\">Plugins</li>";
                                            $plugins->displayNav();
										}
                                        ?>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            <?php if($mem->reqLevel('manage_settings') || $mem->reqLevel('siteadmin') ) : ?>
                                <li class="dropdown">
                                    <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Echelon <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <?php if($mem->reqLevel('manage_settings')) : ?>
                                            <li class="dropdown-header">Settings</li>
                                            <li class="<?php if(isSettings()) echo 'active'; ?>"><a href="<?php echo PATH; ?>settings.php">Site Settings</a></li>

                                            <li class="<?php if(isSettingsGame()) echo 'active'; ?>">
                                                <a href="<?php echo PATH; ?>settings-games.php" title="Game Settings">Game Settings</a>
                                            </li>
                                            <li class="<?php if(isSettingsServer()) echo 'active'; ?>">
                                                <a href="<?php echo PATH; ?>settings-server.php" title="Server Settings">Server Settings</a>
                                            </li>
                                            <li role="separator" class="divider"></li>
                                        <?php endif; ?>

                                        <?php if($mem->reqLevel('siteadmin')) : ?>
                                            <li class="dropdown-header">Management</li>
                                            <li class="<?php if(isSA()) echo ' active'; ?>">
                                                <a href="<?php echo PATH; ?>sa.php" title="Site Administration">Site Admin</a>
                                            </li>
                                            <li class="<?php if(isPerms()) echo ' active'; ?>">
                                                <a href="<?php echo PATH; ?>sa.php?t=perms" title="User Permissions Management">Permissions</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </li>
							<?php endif; ?>
                        </ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle profile" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><div id="profileAvatar"><?= $mem->getGravatar($mem->email) ?></div><span id="profileName"><?= $mem->getCleanName(); ?></span> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li class="dropdown-header">Profile</li>
								<li><a href="<?= PATH ?>me.php">My Profile</a></li>
								<li class="dropdown-header">Games</li>
								<li class="disabled"><a onclick="return false;" class="disabled" href="javascript:void(0)">B3 Profiles</a></li>
								<li role="separator" class="divider"></li>
								<li><a href="<?= PATH ?>login.php?logout">Logout</a></li>
							</ul>
						</li>
					</ul>
					<?php else: ?>
						<ul class="nav navbar-nav">
							<li <?php if(isHome()) echo ' class="active"'; ?>><a href="<?= PATH ?>">Home<span class="sr-only">(current)</span></a></li>
							<li><a href="pubbans.php">Public Ban List</a></li>
						</ul>
						<ul class="nav navbar-nav navbar-right">
							<a href="login.php" class="navbar-btn btn btn-info">Login</a>
						</ul>
					<?php endif; ?>
				</div>
			</div>
		</nav>

		<?php if( isHome() ): ?>
			<div class="jumbotron">
				<div class="container">
					<h1>Welcome to Echelon <small><?php echo ECH_VER; ?></small></h1>

					<?php if($_SESSION['last_seen'] == '' && $_SESSION['username'] == 'admin') : /* Show this message to the admin user (the first user create) only on their first visit */ ?>
						<p>Welcome to Echelon for the first time, now all you need to do is go to the 'Echelon' tab in the navigation up above. You can choose from the list below to pick a game:</p>
						<div id="games">
							<div class="game">
								<div class="title"></div>
								<div class="game"></div>
							</div>
						</div>
					<?php endif; ?>

					<p>Welcome <?php echo $mem->displayName();  if(!$no_games) : ?> you are logged into the &ldquo;<?php echo $game_name; ?>&rdquo; database. You can change what game information you would like to see under the 'game' dropdown above.<?php endif; ?></p>

					<?php if(!$no_games) : ?><a href="clients.php" class="btn btn-info" title="Enter the repositorty and start exploring Echelon">View Clients</a><?php endif; ?>
					<a href="<?php echo $path; ?>login.php?logout" class="btn btn-danger" title="Sign out of Echelon">Log Out</a>
				</div>
			</div>
		<?php endif; ?>

		<?php if( !isset($dontShow) ) : ?>
		<div class="container">
			<div id="content">
		<?php endif;?>

				<?php
				## if Site Admin check for current Echelon Version and if not equal add warning
				errors(); // echo out all errors/success/warnings

				if($query_normal) : // if this is a normal query page and there is a db error show message

					if($db->error)
						dbErrorShow($db->error_msg); // show db error

				endif;
