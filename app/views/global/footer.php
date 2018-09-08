<?php
global $instance, $db, $total_rows, $total_pages;

$page_pagination_url = $_SERVER['REQUEST_URI'];
$page_pagination_url = preg_replace("/\?p=[0-9]*|&p=[0-9]*/", "", $page_pagination_url);
if( strpos($page_pagination_url, "?") !== false )
    $page_pagination_url = $page_pagination_url."&";
else
    $page_pagination_url = $page_pagination_url."?";

if(isset($pagination) && $pagination && !$db->error) : // check to see if pagination is required on this page
	if(!isset($no_data) || !$no_data) : // if there no recorded records ?>
		<div class="under-table">
			<?php if($total_rows > $instance->config['limit-rows']) : /* If the number of rows returned is not more than the min per page then don't show this section */ ?>
				<nav aria-label="pageNav">
					<ul class="pagination">
					<?php if( $page_no > 0 ) : ?>
						<li><a aria-label="Previous" href="<?php if( $page_no > 0 ) printf("%sp=%d", $page_pagination_url, 0); ?>" title="Go to the first page">&laquo;</a></li>
					<?php else: ?>
						<li class="disabled"><span aria-label="Previous" class="disabled" title="Go to the first page">&laquo;</span></li>
					<?php endif; ?>

					<?php if($page_no - 1 > 0) { ?>
						<li class=""><a href="<?php printf("%sp=%d", $page_pagination_url, max(0, $page_no - 2)); ?>" class="page"><?php echo $page_no - 1; ?></a></li>
					<?php } ?>

					<?php if($page_no > 0) { ?>
						<li class=""><a href="<?php printf("%sp=%d", $page_pagination_url, max(0, $page_no - 1)); ?>" class="page"><?php echo $page_no; ?></a></li>
					<?php } ?>

					<li class=" active"><span ><?php echo $page_no + 1; ?></span></li>


					<?php if($page_no + 2 < $total_pages) { ?>
						<li><a href="<?php printf("%sp=%d", $page_pagination_url, max(0, $page_no + 1)); ?>" class="page"><?php echo $page_no + 2; ?></a></li>
					<?php } ?>

					<?php if($page_no + 3 < $total_pages) { ?>
						<li><a href="<?php printf("%sp=%d", $page_pagination_url, max(0, $page_no + 2)); ?>" class="page"><?php echo $page_no + 3; ?></a></li>
					<?php }?>

					<?php if( $page_no < 3 && $page_no + 6 < $total_pages ):
						for( $a = 0; $a < (2-$page_no); $a ++ ): ?>
							<li><a href="<?php printf("%sp=%d", $page_pagination_url, max(0, $page_no + (4+$a))); ?>" class="page"><?php echo $page_no + (4+$a); ?></a></li>
					<?php endfor;
						endif; ?>

					<?php if( $page_no < $total_pages ) : ?>
						<li><a href="<?php printf("%sp=%d", $page_pagination_url, $total_pages); ?>" title="Go to the last page">&raquo;</a></li>
					<?php else: ?>
						<li class="disabled"><span href="<?php printf("%sp=%d", $page_pagination_url, $total_pages); ?>" title="Go to the last page">&raquo;</span></li>
					<?php endif; ?>

					</ul>
				</nav>
			<?php endif; ?>
		</div>
	<?php endif; // if there is data
endif; // end if pagination is on
?>

        </div><!-- close #content -->
    <?php if( !isset($dontShow)): ?>
        <div id="footer">
        </div>
    <?php endif; ?>
    </div>

    <script src="<?= PATH; ?>assets/js/app.min.js"></script>
    <script src="<?= PATH; ?>assets/js/site.js"></script>

    <?php
        if( isset($customPageScripts) )
            echo $customPageScripts;

        global $no_plugins_active, $plugins;
        ## plugin specific js ##
        if(!$no_plugins_active)
            $plugins->getJS();
    ?>

    </body>
</html>
