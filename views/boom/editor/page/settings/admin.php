<form name="pagesettings-adminsettings" id="boom-form-pagesettings-adminsettings">
	<?= Form::hidden('csrf', Security::token()) ?>
	<div id="b-pagesettings" class="boom-tabs b-page-settings">
		<label for="internal_name"><?=__('Internal name')?>

		<?= Form::input('internal_name', $page->internal_name, array('class' => 'boom-input', 'id' => 'internal_name')); ?>
		</label>
	</div>
</form>