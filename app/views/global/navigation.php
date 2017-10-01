<?php

global $game_id, $mem;

// Get the games for later
$games_list = $dbl->getActiveGamesList();
$count = count($games_list);
$hasGames = $count > 0 ? true : false;
$this_cur_page = basename($_SERVER['SCRIPT_NAME']);
$this_cur_page .= ( is_string(strstr($this_cur_page, '?')) ? '&' : '?' );

 if($mem->loggedIn()) : ?>
     <ul class="nav navbar-nav"> <?php
        if($count > 0) : ?>
            <li class="dropdown">
                <a href="" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Games <span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <?php foreach ( $games_list as $game ):
                        echo $game_id == $game['id'] ? '<li class="active">' : "<li>";
                        echo '<a href="'.PATH . $this_cur_page .'game='.$game['id'].'" title="Switch to this game">'.$game['name'].'</a></li>';
                    endforeach; ?>
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