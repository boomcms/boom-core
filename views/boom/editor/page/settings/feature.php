<? if ($auth->logged_in('view_feature_image', $page)): ?>
	<div id="s-pagesettings-featureimage" class="s-pagesettings">

		<? if ( $page->version()->feature_image_id === 0 AND $auth->logged_in('edit_feature_image', $page)): ?>
			<p id="boom-featureimage-none">This page currently has no associated feature image.</p>
			<p>
				<button class="boom-featureimage-edit boom-button">
					Add feature image
				</button>

				<img id='boom-featureimage-img' style="display: none" src='<?= Route::url('asset', array('id' => $page->version()->feature_image_id)) ?>' />
			</p>
		<? else: ?>
			<div class='boom-featureimage-edit'>
				<img id='boom-featureimage-img' src='<?= Route::url('asset', array('id' => $page->version()->feature_image_id)) ?>' />
			</div>
		<? endif; ?>
	</div>
	<form id="boom-form-pagesettings-featureimage" name="pagesettings-featureimage">
		<input type='hidden' name='feature_image_id' id='boom-featureimage-input' value='<?= $page->version()->feature_image_id ?>' />
	</form>
<? endif; ?>