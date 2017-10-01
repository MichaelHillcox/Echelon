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
            // Open Graphs
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        ?>
        <meta property="og:url"                content="<?= $actual_link ?>" />
        <meta property="og:type"               content="website" />
        <meta property="og:title"              content=<?= $site_name ?> Echelon - <?= $page_title; ?> />
        <meta property="og:description"        content="Echelon, a simple over watch tool made for keeping track of b3 players." />
        
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
					<?php
                        include __DIR__."/navigation.php";
                    ?>
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
