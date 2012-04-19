<?php
/**
* Sub-template to display a list of search results
*
*/
?>
<?
	if (isset( $pagination )):
		echo "<p class='pagination corner-5 clearfix'>", $pagination, "</p>";
	endif;
?>

<? if ($total > 0): ?>
	<p>
		Showing <?= $returned ?> of <?= Text::plural( $total, 'result' ) ?>.
	</p>

	<ol class="search-results">
	
	<?
		foreach ($pages as $page):
			?>
				<li>
					<h3>
						<a href="<?=$page->url()?>"><?=$page->title?>&nbsp;&raquo;</a>
					</h3>
					<p>
						<?= strip_tags($page->get_slot( 'text', 'standfirst', null, false )) ?>
					</p>
				</li>
			<?
		endforeach;
		echo "</ol>";

		if (isset( $pagination )):
			echo "<p class='pagination corner-5 clearfix'>", $pagination, "</p>";
		endif;
	?>
<? else: ?>
	<p>
		Your search returned no results.
	</p>
<? endif; ?>