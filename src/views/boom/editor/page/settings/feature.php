<div id='b-page-feature' class="b-pagesettings">
    <h1><?= Lang::get('boom::settings.feature.heading') ?></h1>

	<section>
		<h2>Current feature image</h2>
		<p id="b-page-feature-none">This page has no feature image.</p>

		<?php if ( ! $featureImageId): ?>
			<img id='b-page-feature-current' src='' />
		<?php else: ?>
			<img id='b-page-feature-current' data-asset-id="<?= $featureImageId ?>" src='<?= $assetURL(['asset' => $featureImageId, 'width' => 500]) ?>' />
		<?php endif ?>

		<div id='b-page-feature-buttons'>
			<?= $button('image', 'Select an image from the asset manager', ['id' => 'b-page-feature-edit', 'class' => 'b-button-withtext']) ?>
			<?= $button('trash-o', 'Remove feature image', ['id' => 'b-page-feature-remove', 'class' => 'b-button-withtext']) ?>
		</div>
	</section>

	<section>
		<h2>Images used in page</h2>
		<p>The images which are used in this page are shown below. Click on an image to make it the feature image for the page.</p>

        <ul class="images-in-page"></ul>
	</section>
</div>
