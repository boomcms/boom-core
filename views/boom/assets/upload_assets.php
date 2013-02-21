<div>
	<form method="POST" enctype="multipart/form-data" id="b-assets-upload-form" action="/cms/uploadify/asset">
		<input type="hidden" id="upload_token" name="upload_token" value="<?= $token ?>" />
		<div id="upload-advanced">
			<div class="ui-widget" id="boom-asset-upload-info">
				<div class="ui-corner-all">
					<p>
						<span class="ui-icon ui-icon-info"></span>
						<span class="message">You may upload up to 5 files at a time.  <?=__('Allowed file types')?>: <?= implode(', ', Boom_Asset::$allowed_extensions) ?></span>
					</p>
				</div>
			</div>
			<div id="b-upload-progress"></div>
				<input type="file" name="b-assets-upload-files[]" id="b-assets-upload-file" multiple min="1" max="5" />
				<button class="boom-button" type="button" id="b-upload-add"> Add files </button>
		</div>
	</form>
</div>