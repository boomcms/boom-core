<div class="sledge-tabs">
	<form id="sledge-tagmanager-upload-form" action='/cms/assets/upload' method='post' enctype="multipart/form-data">
		<input type="hidden" name="upload_token" value="<?=sha1(microtime())?>" />
		<div id="upload-advanced">
			<div class="ui-widget" id="sledge-asset-upload-info">
				<div class="ui-state-highlight ui-corner-all">
					<p style="margin: .5em;">
						<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
						You may upload up to 5 files at a time.  Allowed types: <?= implode(', ', Asset::$allowed_types) ?> 
					</p>
				</div>
			</div>	
			<br />
			<p id="sledge-asset-upload-file-container">
				<input type="file" id="sledge-asset-upload-file" name='file' />
			</p>
		</div>
		
		<input type='submit' />
	</form>
</div>
