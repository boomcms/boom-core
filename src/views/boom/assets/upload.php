<div>
	<form method="post" enctype="multipart/form-data" id="b-assets-upload-form" action="/cms/assets/upload">
		<div id="b-assets-upload-container">
			<div id="b-assets-upload-info">
				<p>Drag and drop files here, or <a id="b-assets-upload-add" href="#"><label for="b-assets-upload-file">select files</label></a> to start uploading.</p>
				<p class="message"><?=Lang::get('Supported file types')?>: <?= implode(', ', \Boom\Asset\Mimetype::$allowedExtensions) ?></p>
			</div>

			<div id="b-assets-upload-progress"></div>
			<?= new \BoomCMS\Core\UI\Button('cancel', Lang::get('Cancel'), ['id' => 'b-assets-upload-cancel']) ?>

			<input type="file" name="b-assets-upload-files[]" id="b-assets-upload-file" multiple min="1" max="5" />

            <?= new \BoomCMS\Core\UI\Button('cancel', Lang::get('Close uploader'), ['id' => 'b-assets-upload-close', 'class' => 'b-button-withtext']) ?>
        </div>
	</form>
</div>
