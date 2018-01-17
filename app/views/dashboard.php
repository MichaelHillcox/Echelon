<?php $page = "home";
$page_title = "Home";
$auth_name = 'login';
$auth_user_here = true;
$b3_conn = false;
$pagination = false;
require_once 'app/bootstrap.php';
require 'app/views/global/header.php';
?>

<div id="homePage">

    <div id="change-log" class="index-block">
        <h3>Changelog <?php echo ECH_VER; ?></h3>

        <ul>
            <li>TODO.</li>
        </ul>
    </div>

    <p class="last-seen"><?php if($_SESSION['last_ip'] != '') { ?>You were last seen with this <?php $ip = ipLink($_SESSION['last_ip']); echo $ip; ?> IP address,<br /><?php } ?>
        <?php $mem->lastSeen('l, jS F Y (H:i)'); ?>
    </p>
</div>

<?php
require 'app/views/global/footer.php';
?>
