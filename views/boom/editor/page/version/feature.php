<div id="b-pagesettings-featureimage" class="b-pagesettings">
	<p id="boom-featureimage-none">This page currently has no associated feature image.</p>
	<div class='boom-featureimage-edit'>
	<? if ( ! $feature_image_id): ?>
		<img id='boom-featureimage-img' style="display: none" />
	<? else: ?>
		<img id='boom-featureimage-img' src='<?= Route::url('asset', array('id' => $feature_image_id, 'width' => 250, 'height' => 80)) ?>' />
	<? endif; ?>
	</div>
</div>
<form id="boom-form-pagesettings-featureimage" name="pagesettings-featureimage">
	<input type='hidden' name='feature_image_id' id='boom-featureimage-input' value='<?= $feature_image_id ?>' />
</form>