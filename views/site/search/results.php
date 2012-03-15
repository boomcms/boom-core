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

<ul class="tag-archive">
	
<?
	foreach ($pages as $page):
		?>
			<li class="list">
				<h3>
					<a href="<?=$page->url()?>"><?=$page->title?>&nbsp;&raquo;</a>
				</h3>
				<h4>
					<?= $page->get_slot( 'text', 'standfirst', null, false ) ?>
				</h4>
			</li>
		<?
	endforeach;
	echo "</ul>";

	if (isset( $pagination )):
		echo "<p class='pagination corner-5 clearfix'>", $pagination, "</p>";
	endif;
?>