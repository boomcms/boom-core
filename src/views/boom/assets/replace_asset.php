<?php
/**
 * Allows replacing an existing asset with a new file.
 * Template to upload the new file.
 *
 * Rendered by: Controller_Cms_Assets::action_replace()
 */
?>
<div class="boom-tabs">
	<form id="boom-tagmanager-replace-form" action='/cms/assets/replace/<?= $asset->getId() ?>' method='post' enctype="multipart/form-data">
		<input type="hidden" name="upload_token" value="<?=sha1(microtime()) ?>" />
		<div id="b-assets-upload-container">
			<div class="ui-widget" id="b-assets-upload-info">
				<div class="ui-state-highlight ui-corner-all">
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						<?= Lang::get('Allowed file types') ?>: <?= implode(', ', array_keys(\Boom\Asset\Type::$allowed_types)) ?>
					</p>
				</div>
			</div>
			<br />
			<p id="boom-asset-replace-file-container">
				<input type="file" id="boom-asset-replace-file" name='file' />
			</p>
		</div>

		<input type='submit' />
	</form>
</div>
