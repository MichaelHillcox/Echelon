<?php
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
		<meta http-equiv="Content-Type" content="text/html; charset="<?= $charset;?>" />
		<title><?= $site_name ?> Echelon - <?= $page_title; ?></title>

		<link rel="icon" type="image/png" href="app/assets/images/logo-dark.png" />
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet">
		<link rel="stylesheet" href="<?= PATH ?>app/assets/styles/fontawesome/css/font-awesome.min.css">
		<link href="<?= PATH; ?>app/assets/styles/master.min.css" rel="stylesheet" media="screen" type="text/css" />

		<?php
		## Include CSS For pages ##
		if(isLogin())
			css_file('login');

		if(isCD())
			css_file('cd');

		if(isSettings())
			css_file('settings');

		if(isHome())
			css_file('home');

		## Header JS for Map Page ##
		if(isMap())
			echo $map_js;

		// return any plugin CSS files
		if(!$no_plugins_active)
			$plugins->getCSS();

		?>
	</head>

	<body id="<?php echo $page; ?>">

		<div id="mainNav">
			<div class="container">
				<div id="logo"></div>
				<nav>
					<?php if($mem->loggedIn()) { ?>

						<li class="home<?php if(isHome()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>" title="Home Page">Home</a></li>

						<?php
						$games_list = $dbl->getActiveGamesList();
						$count = count($games_list);
						if($count > 0) : ?>

							<li >
								<a >Games<i class="fa fa-caret-down" aria-hidden="true"></i></a>
								<ul class="dd games-list">
									<?php
									$this_cur_page = basename($_SERVER['SCRIPT_NAME']);
									if(is_string(strstr($this_cur_page, '?'))) //hackey solution to allow plugin pages to encode vital information
										$this_cur_page .= '&';
									else
										$this_cur_page .= '?';

									foreach ( $games_list as $game ):
										if($game == $game['id'])
											echo '<li class="selected">';
										else
											echo '<li>';
										echo '<a href="'.PATH . $this_cur_page .'game='.$game['id'].'" title="Switch to this game">'.$game['name_short'].'</a></li>';
									endforeach;
									?>
								</ul>
							</li>

							<?php if($mem->reqLevel('clients')) : ?>
								<li >
									<a >Clients<i class="fa fa-caret-down" aria-hidden="true"></i></a>
									<ul >
										<li class="n-clients<?php if(isClients()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>clients.php" title="Clients Listing">Clients</a></li>
										<li class="n-active<?php if($page == 'active') echo ' selected'; ?>"><a href="<?php echo PATH; ?>active.php" title="In-active admins">In-active Admins</a></li>
										<li class="n-regular<?php if($page == 'regular') echo ' selected'; ?>"><a href="<?php echo PATH; ?>regular.php" title="Regular non admin visitors to your servers">Regular Visitors</a></li>
										<li class="n-admins<?php if($page == 'admins') echo ' selected'; ?>"><a href="<?php echo PATH; ?>admins.php" title="A list of all admins">Admin Listing</a></li>
										<li class="n-world<?php if(isMap()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>map.php" title="Player map">World Player Map</a></li>
									</ul>
								</li>
								<?php
							endif; // reqLevel clients DD

							if($mem->reqLevel('penalties')) :
								?>
								<li >
									<a >Penalties<i class="fa fa-caret-down" aria-hidden="true"></i></a>
									<ul >
										<li class="n-adminkicks<?php if($page == 'adminkicks') echo ' selected'; ?>"><a href="<?php echo PATH; ?>kicks.php?t=a">Admin Kicks</a></li>
										<li class="n-adminbans<?php if($page == 'adminbans') echo ' selected'; ?>"><a href="<?php echo PATH; ?>bans.php?t=a">Admin Bans</a></li>
										<li class="n-b3bans<?php if($page == 'b3kicks') echo ' selected'; ?>"><a href="<?php echo PATH; ?>kicks.php?t=b" title="All kicks added automatically by B3">B3 Kicks</a></li>
										<li class="n-b3bans<?php if($page == 'b3bans') echo ' selected'; ?>"><a href="<?php echo PATH; ?>bans.php?t=b" title="All bans added automatically by B3">B3 Bans</a></li>
										<li class="n-pubbans<?php if(isPubbans()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>pubbans.php" title="A public list of bans in the database">Public Ban List</a></li>
									</ul>
								</li>
								<?php
							endif; // end reqLevel penalties DD
							?>

							<li >
								<a >Other<i class="fa fa-caret-down" aria-hidden="true"></i></a>
								<ul >
									<li class="n-notices<?php if($page == 'notices') echo ' selected'; ?>">
										<a href="<?php echo PATH; ?>notices.php" title="In-game Notices">Notices</a>
									</li>
									<?php
									if(!$no_plugins_active)
										$plugins->displayNav();
									?>
								</ul>
							</li>

						<?php endif; // end if no games hide the majority of the navigation ?>

						<li >
							<a >Echelon<i class="fa fa-caret-down" aria-hidden="true"></i></a>
							<ul >

								<?php if($mem->reqLevel('manage_settings')) : ?>
									<li class="<?php if(isSettings()) echo 'selected'; ?>">
										<a href="<?php echo PATH; ?>settings.php">Site Settings</a>

										<ul class="second">
											<li class="<?php if(isSettingsGame()) echo 'selected'; ?>">
												<a href="<?php echo PATH; ?>settings-games.php" title="Game Settings">Game Settings</a>
											</li>
											<li class="<?php if(isSettingsServer()) echo 'selected'; ?>">
												<a href="<?php echo PATH; ?>settings-server.php" title="Server Settings">Server Settings</a>
											</li>
										</ul>
									</li>
								<?php endif; ?>

								<?php if($mem->reqLevel('siteadmin')) : ?>
									<li class="n-sa<?php if(isSA()) echo ' selected'; ?>">
										<a href="<?php echo PATH; ?>sa.php" title="Site Administration">Site Admin</a>
									</li>
									<li class="n-tools<?php if(isPerms()) echo ' selected'; ?>">
										<a href="<?php echo PATH; ?>sa.php?t=perms" title="User Permissions Management">Permissions</a>
									</li>
								<?php endif; ?>

								<li class="n-me<?php if(isMe()) echo ' selected'; ?>">
									<a href="<?= PATH ?>me.php" title="Edit your account">My Account</a>
								</li>
							</ul>
						</li>

					<?php } else { ?>

						<li class="login<?php if(isLogin()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>login.php" title="Login to Echelon to see the good stuff!">Login</a></li>
						<li class="pubbans<?php if(isPubbans()) echo ' selected'; ?>"><a href="<?php echo PATH; ?>pubbans.php" title="Public Ban List">Public Ban List</a></li>

					<?php } ?>
				</nav>
				<div id="profile">
					<div id="profileInfo" class="<?php if(!GRAVATAR) echo 'noAvatar'?>">
						<div id="profileName"><?php $mem->displayName(); ?></div>
						<?php if($mem->loggedIn()): ?>
							<div id="profileLogout">
								<a href="<?= PATH; ?>actions/logout.php" class="logout" title="Sign out"><i class="fa fa-sign-out" aria-hidden="true"></i></a>
							</div>
						<?php endif; ?>
						<?php
						if(GRAVATAR)
							echo "<div id=\"profileAvatar\">".$mem->getGravatar($mem->email)."</div>";
						?>
					</div>
				</div>
			</div>
		</div>

		<div class="container">

			<div id="content">

				<?php

				## if Site Admin check for current Echelon Version and if not equal add warning
				if($mem->reqLevel('see_update_msg') && (isSA() || isHome())) :
					$latest = getEchVer();
					if((date('N') == 1) && ECH_VER !== $latest && $latest != false) // if current version does not equal latest version show warning message
						set_warning('You are not using the lastest version of Echelon ('.$latest.'), please check the <a href="http://www.bigbrotherbot.com/forums/" title="Check the B3 Forums">B3 Forums</a> for more information.');
				endif;

				errors(); // echo out all errors/success/warnings

				if($query_normal) : // if this is a normal query page and there is a db error show message

					if($db->error)
						dbErrorShow($db->error_msg); // show db error

				endif;
