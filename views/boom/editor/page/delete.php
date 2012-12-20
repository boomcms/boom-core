<?php
/**
* Delete page confirmation.
*
*/
?>
<p>Are you sure you want to delete this page? This cannot be recovered.</p>

<? if ($count > 0):	?>
	<p>
		<strong>Warning:</strong>
		<br />Deleting this page will make it's <?= $count, " ", Inflector::plural("child page", $count); ?> inaccessible:
	</p>
	<div id="b-page-delete-children">
		<ul>
			<? foreach ($titles as $title): ?>
			 	<li>
					<?= $title ?>
				</li>
			<? endforeach; ?>

		</ul>

		<form id="b-page-delete-form">
			<?= Form::checkbox('with_children', 1); ?>
			Delete <?= $count, " ", Inflector::plural("child page", $count); ?> as well.
		</form>
	</div>
<? endif; ?>

<p>Click 'Okay' to delete, or 'Cancel' to keep the page.</p>