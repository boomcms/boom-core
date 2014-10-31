<div id="b-assets-content">
	<?= new View('boom/assets/thumbs', get_defined_vars()) ?>
</div>
<div id="b-assets-pagination" class="pagination">
	<?php if ($pages > 1): ?>
		<a href="#" class="first" data-action="first">&laquo;</a>
		<a href="#" class="previous" data-action="previous">&lsaquo;</a>
		<input type="text" readonly="readonly" data-max-page="<?= $pages ?>" data-current-page="<?= $page ?>" />
		<a href="#" class="next" data-action="next">&rsaquo;</a>
		<a href="#" class="last" data-action="last">&raquo;</a>
	<?php endif ?>
</div>