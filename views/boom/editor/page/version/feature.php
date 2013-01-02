<div id="b-pagesettings-featureimage" class="b-pagesettings">

	<? if ($feature_image_id === 0): ?>
		<p id="boom-featureimage-none">This page currently has no associated feature image.</p>
		<p>
			<button class="boom-featureimage-edit boom-button">
				Add feature image
			</button>

			<img id='boom-featureimage-img' style="display: none" src='<?= Route::url('asset', array('id' => $feature_image_id)) ?>' />
		</p>
	<? else: ?>
		<div class='boom-featureimage-edit'>
			<img id='boom-featureimage-img' src='<?= Route::url('asset', array('id' => $feature_image_id)) ?>' />
		</div>
	<? endif; ?>
</div>
<form id="boom-form-pagesettings-featureimage" name="pagesettings-featureimage">
	<input type='hidden' name='feature_image_id' id='boom-featureimage-input' value='<?= $feature_image_id ?>' />
</form>