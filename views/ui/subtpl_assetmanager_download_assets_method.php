<?php
/**
* Used when downloading multiple assets.
* Allows the user to chose a compression format.
*
* Rendered by: Currently not implemented.
*
*/
?>
<div class="ui-widget" id="sledge-asset-upload-info">
	<div class="ui-state-highlight ui-corner-all">
		<p style="margin: .5em;">
			<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
			The selected files will be downloaded as an archive. Please choose your preferred archive format below.
		</p>
	</div>
</div>	
<br />
<p>
	<label for="sledge-tagmanager-archive-method">
		Archive format:
	</label>
	<select id="sledge-tagmanager-archive-method">
		<option value-"zip">zip</option>
		<option value="tgz">tgz</option>
		<option value="tbz2">tbz2</option>
	</select>
</p>
