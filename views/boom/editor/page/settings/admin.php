<form class="b-form-settings">
	<?= Form::hidden('csrf', Security::token()) ?>
	<div>
		<p>
			<label for="internal_name"><?=__('Internal name')?></label>
			<?= Form::input('internal_name', $page->internal_name, array('id' => 'internal_name')); ?>
		</p>
	</div>
</form>