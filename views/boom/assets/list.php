<div id="b-assets-content">
	<?= new View('boom/assets/thumbs', get_defined_vars()) ?>
</div>
<div id="b-assets-pagination">
	<?
		if (isset($pagination)):
			echo "<div class='boom-pagination ui-helper-left'>", $pagination, "</div>";
		endif;
	?>
</div>
<div id="b-assets-stats">
	<?= Num::format($total, 0) ?> <?= Inflector::plural('file', $total) ?> / <?= Text::bytes($total_size) ?>
</div>