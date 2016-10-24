<?php
if($pagination && !$db->error) : // check to see if pagination is required on this page
	if(!$no_data) : // if there no recorded records ?>
		<div class="under-table">
<!--			<p class="num-rows">-->
<!--				--><?php //recordNumber($start_row, $limit_rows, $total_rows); ?>
<!--			</p>-->
			<?php if($total_rows > $limit_rows) : /* If the number of rows returned is not more than the min per page then don't show this section */ ?>
				<nav aria-label="pageNav">
					<ul class="pagination">
						<?php if( $page_no > 0 ) : ?>
							<li><a aria-label="Previous" href="<?php if( $page_no > 0 ) printf("%25s?p=%d%s", $this_page, 0, $query_string_page); ?>" title="Go to the first page">&laquo;</a></li>
						<?php else: ?>
							<li class="disabled"><span aria-label="Previous" class="disabled" title="Go to the first page">&laquo;</span></li>
						<?php endif; ?>

						<?php if($page_no - 1 > 0) { ?>
							<li class=""><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 2), $query_string_page); ?>" class="page"><?php echo $page_no - 1; ?></a></li>
						<?php } ?>

						<?php if($page_no > 0) { ?>
							<li class=""><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no - 1), $query_string_page); ?>" class="page"><?php echo $page_no; ?></a></li>
						<?php } ?>

					<li class=" active"><span ><?php echo $page_no + 1; ?></span></li>


					<?php if($page_no + 2 < $total_pages) { ?>
						<li><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 1), $query_string_page); ?>" class="page"><?php echo $page_no + 2; ?></a></li>
					<?php } ?>

					<?php if($page_no + 3 < $total_pages) { ?>
						<li><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + 2), $query_string_page); ?>" class="page"><?php echo $page_no + 3; ?></a></li>
					<?php }?>

					<?php if( $page_no < $total_pages ) : ?>
						<li><a href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" title="Go to the last page">&raquo;</a></li>
					<?php else: ?>
						<li class="disabled"><span href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" title="Go to the last page">&raquo;</span></li>
					<?php endif; ?>

					</ul>
				</nav>
			<?php endif; ?>
			<br class="clear" />
		</div>
	<?php endif; // if there is data
endif; // end if pagination is on
?>

</div><!-- close #content -->


<?php if( !isset($dontShow)): ?>
<div id="footer">
	<p>
		<span class="copy">&copy;<?php echo date("Y"); ?> <a href="http://eire32designs.com" target="_blank">Eire32</a> &amp; <a href="http://bigbrotherbot.net" target="_blank">Big Brother Bot</a> - All rights reserved</span>
	</p>
</div><!-- close #footer -->
<?php endif; ?>

</div><!-- close #page-wrap -->


<script src="<?= $path; ?>app/assets/js/jquery.js"></script>
<script src="<?= $path; ?>app/assets/bootstrap/js/bootstrap.min.js"></script>
<!-- load main site js -->
<script src="<?php echo $path; ?>app/assets/js/site.js"></script>

<?php
	if( isset($customPageScripts) )
		echo $customPageScripts;
?>

<?php
	## plugin specific js ##
	if(!$no_plugins_active)
		$plugins->getJS();
?>

</body>
</html>
