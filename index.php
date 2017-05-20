<?php
$page = "home";
$page_title = "Home";
$auth_name = 'login';
$auth_user_here = true;
$b3_conn = false;
$pagination = false;
require 'inc.php';


require 'app/views/global/header.php';
?>

<div id="homePage">

	<div id="change-log" class="index-block">
		<h3>Changelog <?php echo ECH_VER; ?></h3>

		<ul>
			<li>TODO.</li>
		</ul>
	</div>

	<?php
	## External Links Section ##
	$links = $dbl->getLinks();

	$num_links = $links['num_rows'];

	if($num_links > 0) :

		echo '<div id="links-table" class="index-block">
					<h3>External Links</h3>
					<ul class="links-list">';

		foreach($links['data'] as $link) :

			echo '<li><a href="'. $link['url'] .'" class="external" title="'. $link['title'] .'">' . $link['name'] . '</a></li>';

		endforeach;

		echo '</ul></div>';

	else:
		echo 'no results';

	endif;
	## End External Links Section ##
	?>

	<p class="last-seen"><?php if($_SESSION['last_ip'] != '') { ?>You were last seen with this <?php $ip = ipLink($_SESSION['last_ip']); echo $ip; ?> IP address,<br /><?php } ?>
		<?php $mem->lastSeen('l, jS F Y (H:i)'); ?>
	</p>
</div>

<?php require 'app/views/global/footer.php'; ?>
