<form id="b-form-pageversion-template" name="pageversion-template">
	<?= Form::hidden('csrf', Security::token()) ?>
	<div class="b-template">

		<label for="template_id">Template:</label>

		<?= Form::select('template_id', $templates, $template_id) ?>

	</div>
</form>