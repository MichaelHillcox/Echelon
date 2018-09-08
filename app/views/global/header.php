<?php
global $instance, $page_title, $map_js, $no_plugins_active, $plugins, $mem, $no_game, $db_error, $db;
## if the page has the normal query process & there is a connectionn to the B3 DB
if(isset($query_normal) && $query_normal && (!$db_error)) :
	$results = $db->query($query_limit);

	$num_rows = $results['num_rows']; // the the num_rows
	$data_set = $results['data']; // seperate out the return data set
endif;

## Pagination for pages with tables ## 
if(isset($pagination) && $pagination && (!$db_error)) : // if pagination is needed on the page
	## Find total rows ##
	$total_num_rows = $db->query($query, false); // do not fetch the data
	$total_rows = $total_num_rows['num_rows'];

	// create query_string
	if($total_rows > 0) {

		$total_pages = ceil($total_rows/$instance->config['limit-rows'])-1;
		
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
		<title><?= $instance->config['name'] ?> Echelon - <?= $page_title; ?></title>

		<meta charset="utf-8">
		<link rel="icon" type="image/png" href="<?= PATH ?>assets/images/logo-dark.png" />

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link href="<?= PATH; ?>assets/styles/master.min.css" rel="stylesheet" media="screen" type="text/css" />

        <?php
            // Open Graphs
            $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        ?>
        <meta property="og:url"                content="<?= $actual_link ?>" />
        <meta property="og:type"               content="website" />
        <meta property="og:title"              content=<?= $instance->config['name'] ?> Echelon - <?= $page_title; ?> />
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

		<nav  class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container">
                <a id="logo" class="navbar-brand" href="<?= PATH ?>"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#masterNav" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

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

					<?php if(!$no_games) : ?><a href="clients" class="btn btn-info" title="Enter the repositorty and start exploring Echelon">View Clients</a><?php endif; ?>
					<a href="<?php echo PATH; ?>login?logout" class="btn btn-danger" title="Sign out of Echelon">Log Out</a>
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

				if(isset($query_normal) && $query_normal) : // if this is a normal query page and there is a db error show message

					if($db->error)
                        echo '<h3>Database Error!</h3><p>'. $db->error_msg .'</p>';

				endif;
