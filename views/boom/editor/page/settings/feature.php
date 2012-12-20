<? if ($auth->logged_in('view_feature_image', $page)): ?>
	<div id="s-pagesettings-featureimage" class="s-pagesettings">

		<? if ( $page->version()->feature_image_id === 0 AND $auth->logged_in('edit_feature_image', $page)): ?>
			<p id="sledge-featureimage-none">This page currently has no associated feature image.</p>
			<p>
				<button class="sledge-featureimage-edit sledge-button">
					Add feature image
				</button>

				<img id='sledge-featureimage-img' style="display: none" src='<?= Route::url('asset', array('id' => $page->version()->feature_image_id)) ?>' />
			</p>
		<? else: ?>
			<div class='sledge-featureimage-edit'>
				<img id='sledge-featureimage-img' src='<?= Route::url('asset', array('id' => $page->version()->feature_image_id)) ?>' />
			</div>
		<? endif; ?>
	</div>
	<form id="sledge-form-pagesettings-featureimage" name="pagesettings-featureimage">
		<input type='hidden' name='feature_image_id' id='sledge-featureimage-input' value='<?= $page->version()->feature_image_id ?>' />
	</form>
<? endif; ?>