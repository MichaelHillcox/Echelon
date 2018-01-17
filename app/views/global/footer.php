<?php
if($pagination && !$db->error) : // check to see if pagination is required on this page
	if(!$no_data) : // if there no recorded records ?>
		<div class="under-table">
<!--			<p class="num-rows">-->
<!--                --><?//= 'Records: '.($start_row + 1).'&nbsp;to&nbsp;'.min($start_row + $instance->config['limit-rows'], $total_rows).'&nbsp;of&nbsp;'.$total_rows;  ?>
<!--			</p>-->
			<?php if($total_rows > $instance->config['limit-rows']) : /* If the number of rows returned is not more than the min per page then don't show this section */ ?>
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

					<?php if( $page_no < 3 && $page_no + 6 < $total_pages ):
						for( $a = 0; $a < (2-$page_no); $a ++ ): ?>
							<li><a href="<?php printf("%25s?p=%d%s", $this_page, max(0, $page_no + (4+$a)), $query_string_page); ?>" class="page"><?php echo $page_no + (4+$a); ?></a></li>
					<?php endfor;
						endif; ?>

					<?php if( $page_no < $total_pages ) : ?>
						<li><a href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" title="Go to the last page">&raquo;</a></li>
					<?php else: ?>
						<li class="disabled"><span href="<?php printf("%25s?p=%d%s", $this_page, $total_pages, $query_string_page); ?>" title="Go to the last page">&raquo;</span></li>
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


<script src="<?= $path; ?>app/assets/js/jquery.js"></script>
<script src="<?= $path; ?>app/assets/bootstrap/js/bootstrap.min.js"></script>
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
