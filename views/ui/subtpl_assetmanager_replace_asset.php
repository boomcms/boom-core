<?php
/**
* Allows replacing an existing asset with a new file.
* Template to upload the new file.
*
* Rendered by: Controller_Cms_Assets::action_replace()
*
* @see Controller_Cms_Assets::action_replace()
*/
?>
<div class="sledge-tabs">
	<form id="sledge-tagmanager-replace-form" action='/cms/assets/replace/<?= $asset->id; ?>' method='post' enctype="multipart/form-data">
		<input type="hidden" name="upload_token" value="<?=sha1(microtime())?>" />
		<div id="upload-advanced">
			<div class="ui-widget" id="sledge-asset-upload-info">
				<div class="ui-state-highlight ui-corner-all">
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						Allowed file types: <?= implode(', ', Asset::$allowed_types) ?> 
					</p>
				</div>
			</div>	
			<br />
			<p id="sledge-asset-replace-file-container">
				<input type="file" id="sledge-asset-replace-file" name='file' />
			</p>
		</div>
		
		<input type='submit' />
	</form>
</div>
