<form name="pagesettings-adminsettings" id="boom-form-pagesettings-adminsettings">
	<div id="b-pagesettings" class="boom-tabs s-pagesettings">
		<label for="internal_name"><?=__('Internal name')?></label>
		
		<?= Form::input('internal_name', $page->internal_name, array('class' => 'boom-input', 'id' => 'internal_name')); ?>
	</div>
</form>