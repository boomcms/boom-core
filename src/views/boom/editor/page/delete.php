<p>Are you sure you want to delete this page? This cannot be recovered.</p>

<p><strong>Please check the following details are what you expected to ensure you don't accidentally delete a different page</strong></p>
<dl>
    <dt>Page title</dt>
    <dd><?= $page->getTitle() ?></dd>
    <dt>Page URL</dt>
    <dd><?= $page->url() ?></dd>
</dl>

<form id="b-page-delete-form">
	<?php if ($count > 0): ?>
		<p>
			<strong>Warning:</strong>
			<br />Deleting this page will make it's <?= $count, " ", Inflector::plural("child page", $count) ?> inaccessible.
		</p>
		<div id="b-page-delete-children">
			<?= Form::checkbox('with_children', 1) ?>
			Delete <?= $count, " ", Inflector::plural("child page", $count) ?> as well.
		</div>
	<?php endif ?>
</form>

<p>Click the tick to delete, or the cross to keep the page.</p>
