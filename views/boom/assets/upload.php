<div>
	<form method="POST" enctype="multipart/form-data" id="b-assets-upload-form" action="/cms/assets/upload">
		<?= Form::hidden('csrf', Security::token()) ?>

		<div id="b-assets-upload-container">
			<div id="b-assets-upload-info">
				<p>Drag and drop files here, or <a id="b-assets-upload-add" href="#"><label for="b-assets-upload-file">select files</label></a> to start uploading.</p>
				<p class="message"><?=__('Supported file types')?>: <?= implode(', ', Boom_Asset::$allowed_extensions) ?></p>
			</div>

			<div id="b-assets-upload-progress"></div>
			<button type="button" id="b-assets-upload-cancel" class="boom-button" data-icon="ui-icon-boom-cancel"><?= __('Cancel') ?></button>

			<input type="file" name="b-assets-upload-files[]" id="b-assets-upload-file" multiple min="1" max="5" />
		</div>
	</form>
</div>