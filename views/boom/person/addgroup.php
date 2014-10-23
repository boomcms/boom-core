<div id="b-people-addgroup">
	<form onsubmit='return false;'>
		<p><?= Kohana::message('boom-people', 'addgroup1') ?></p>
		<p><?= Kohana::message('boom-people', 'addgroup2') ?></p>
		<?= Form::select('groups[]', $groups, null, array('multiple' => 'multiple')); ?>
	</form>
</div>