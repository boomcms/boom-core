<?php
/**
* Delete page confirmation.
*
*/
?>
<p>Are you sure you want to delete this page? This cannot be recovered.</p>

<form id="b-page-delete-form">
	<?= Form::hidden('csrf', Security::token()) ?>
	<? if ($count > 0): ?>
		<p>
			<strong>Warning:</strong>
			<br />Deleting this page will make it's <?= $count, " ", Inflector::plural("child page", $count); ?> inaccessible.
		</p>
		<div id="b-page-delete-children">
			<?= Form::checkbox('with_children', 1); ?>
			Delete <?= $count, " ", Inflector::plural("child page", $count); ?> as well.
		</div>
	<? endif; ?>
</form>

<p>Click 'Okay' to delete, or 'Cancel' to keep the page.</p>