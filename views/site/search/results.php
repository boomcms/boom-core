<?php
/**
* Sub-template to display a list of search results
*
*/
?>

<p>
	<?= $results ?> Results
</p>

<ul class="tag-archive">
	
<?
	foreach ($pages as $i => $p):
		?>
			<li class="list">
				<h3>
					<a href="<?=$p->url()?>"><?=$p->title?>&nbsp;&raquo;</a>
				</h3>
				<h4>
					<?= $p->get_slot( 'text', 'standfirst', null, false ) ?>
				</h4>
			</li>
		<?
	endforeach;
	echo "</ul>";
?>