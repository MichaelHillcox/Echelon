<?php

global $game_id, $mem;

// Get the games for later
$games_list = $dbl->getActiveGamesList();
$count = !$games_list ? 0 : count($games_list);
$hasGames = $count > 0 ? true : false;
$this_cur_page = (strpos($_SERVER['REQUEST_URI'], '?') !== false) ? $_SERVER['REQUEST_URI']."&" : $_SERVER['REQUEST_URI']."?";

 if($mem->loggedIn()) : ?>
     <ul class="navbar-nav mr-auto"> <?php
        if($count > 0) : ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Games
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <h6 class="dropdown-header">Select a Game</h6>
                    <?php foreach ( $games_list as $game ):
                        echo '<a class="dropdown-item" href="'.$this_cur_page .'game='.$game['id'].'" title="Switch to this game">'.$game['name'].'</a>';
                    endforeach; ?>
                </div>
            </li>
            <?php if($mem->reqLevel('clients')) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Clients
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <h6 class="dropdown-header">Clients</h6>
                        <a class="dropdown-item <?php if(isClients()) echo ' active'; ?>" href="<?= PATH; ?>clients" title="Clients Listing">Clients</a>
                        <a class="dropdown-item <?php if($page == 'regular') echo ' active'; ?>" href="<?= PATH; ?>regulars" title="Regular non admin visitors to your servers">Regular Visitors</a>
                        <a class="dropdown-item <?php if($page == 'admins') echo ' active'; ?>" href="<?= PATH; ?>admins" title="A list of all admins">Admin Listing</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Admins</h6>
                        <a class="dropdown-item <?php if($page == 'active') echo ' active'; ?>" href="<?= PATH; ?>active" title="In-active admins">In-active Admins</a>
                        <a class="dropdown-item <?php if(isMap()) echo ' active'; ?>" href="<?= PATH; ?>map" title="Player map">World Player Map</a>
                    </div>
                </li>
            <?php endif;
            if($mem->reqLevel('penalties')) : ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Penalties
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <h6 class="dropdown-header">In Game</h6>
                        <a class="dropdown-item <?php if($page == 'adminkicks') echo ' active'; ?>" href="<?= PATH; ?>kicks?t=a">Kicks</a>
                        <a class="dropdown-item <?php if($page == 'adminbans') echo ' active'; ?>" href="<?= PATH; ?>bans?t=a">Bans</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">B3</h6>
                        <a class="dropdown-item <?php if($page == 'b3kicks') echo ' active'; ?>" href="<?= PATH; ?>kicks?t=b" title="All kicks added automatically by B3">Kicks</a>
                        <a class="dropdown-item <?php if($page == 'b3bans') echo ' active'; ?>" href="<?= PATH; ?>bans?t=b" title="All bans added automatically by B3">Bans</a>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">All</h6>
                        <a class="dropdown-item <?php if(isPubbans()) echo ' active'; ?>" href="<?= PATH; ?>public-bans" title="A public list of bans in the database">Public Ban List</a>
                    </div>
                </li>
            <?php endif; ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Other
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <h6 class="dropdown-header">Misc</h6>
                    <a class="dropdown-item <?php if($page == 'notices') echo ' active'; ?>" href="<?= PATH; ?>notice" title="In-game Notices">Notices</a>
                    <div class="dropdown-divider"></div>
                    <?php
                    global $no_plugins_active, $plugins;
                    if(!$no_plugins_active) {
                        echo "<h6 class=\"dropdown-header\">Plugins</h6>";
                        $plugins->displayNav();
                    }
                    ?>
                </div>
            </li>
        <?php endif; ?>
        <?php if($mem->reqLevel('manage_settings') || $mem->reqLevel('siteadmin') ) : ?>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Echelon
                </a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <?php if($mem->reqLevel('manage_settings')) : ?>
                        <h6 class="dropdown-header">Settings</h6>
                        <a class="dropdown-item <?php if(isSettings()) echo 'active'; ?>" href="<?= PATH; ?>settings">Site Settings</a></a>

                        <a class="dropdown-item <?php if(isSettingsGame()) echo 'active'; ?>" href="<?= PATH; ?>game-settings" title="Game Settings">Game Settings</a>
                        <a class="dropdown-item <?php if(isSettingsServer()) echo 'active'; ?>"  href="<?= PATH; ?>server-settings" title="Server Settings">Server Settings</a>
                    <?php endif; ?>

                    <?php if($mem->reqLevel('siteadmin')) : ?>
                        <div class="dropdown-divider"></div>
                        <h6 class="dropdown-header">Management</h6>
                        <a class="dropdown-item <?php if(isSA()) echo ' active'; ?>" href="<?= PATH; ?>site-admins" title="Site Administration">Site Admin</a>
                        <a class="dropdown-item <?php if(isPerms()) echo ' active'; ?>" href="<?= PATH; ?>site-admins?t=perms" title="User Permissions Management">Permissions</a>
                    <?php endif; ?>
                </div>
            </li>
        <?php endif; ?>
    </ul>

    <ul class="nav navbar-nav navbar-right">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><div id="profileAvatar"><?= $mem->getGravatar($mem->email) ?></div><span id="profileName"><?= $mem->getCleanName(); ?></span> </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <h6 class="dropdown-header">Profile</h6>
                <a class="dropdown-item" href="<?= PATH ?>me">My Profile</a>
                <h6 class="dropdown-header">Games</h6>
                <a class="dropdown-item disabled" onclick="return false;" href="javascript:void(0)">B3 Profiles</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= PATH ?>login?logout">Logout</a>
            </div>
        </li>
    </ul>
<?php else: ?>
     <ul class="navbar-nav mr-auto">
        <a class="nav-link" <?php if(isHome()) echo ' class="active"'; ?> href="<?= PATH ?>">Home<span class="sr-only">(current)</span></a></a>
        <a class="nav-link" href="public-bans">Public Ban List</a></a>
    </ul>
    <ul class="nav navbar-nav navbar-right">
        <a href="login" class="navbar-btn btn btn-outline-primary">Login</a>
    </ul>
<?php endif; ?>