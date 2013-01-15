<div id="boom-person-addgroup">
	<form onsubmit='return false;'>
		Select some groups to add:

		<?= Form::select('groups[]', $groups, NULL, array('multiple' => 'multiple')); ?>
	</form>
</div>