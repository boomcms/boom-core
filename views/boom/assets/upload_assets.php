<?php
/**
* Allows uploading new assets.
*
* Rendered by: Controller_Cms_Assets::action_upload()
*
*/
?>
<div>
	<iframe name="uploadiframe" class="hidden" style="display:none" src="javascript:false"></iframe>
	<form method="POST" enctype="multipart/form-data" id="s-assets-upload-form" action="/cms/uploadify/asset" target="uploadiframe">
		<input type="hidden" id="upload_token" name="upload_token" value="<?= $token ?>" />
		<div id="upload-advanced">
			<div class="ui-widget" id="sledge-asset-upload-info">
				<div class="ui-state-highlight ui-corner-all">
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						<span class="message">You may upload up to 5 files at a time.  <?=__('Allowed file types')?>: <?= implode(', ', Sledge_Asset::$allowed_extensions) ?></span> 
					</p>
				</div>
			</div>	
			<br />
				<input type="file" name="s-assets-upload-files[]" id="s-assets-upload-file" multiple min=1 max=5 />
				<input type="submit" class="sledge-button" value="upload">
		</div>
	</form>
</div>
