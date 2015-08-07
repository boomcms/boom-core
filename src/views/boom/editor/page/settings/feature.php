<div id='b-page-feature' class="b-pagesettings">
    <h1><?= Lang::get('boom::settings.feature.heading') ?></h1>

	<section>
		<p id="b-page-feature-none">This page has no feature image.</p>

		<?php if (!$featureImageId): ?>
			<img id='b-page-feature-current' src='' />
		<?php else: ?>
			<img id='b-page-feature-current' data-asset-id="<?= $featureImageId ?>" src='<?= $assetURL(['asset' => $featureImageId, 'width' => 500]) ?>' />
		<?php endif ?>

        <?= $button('image', 'Select an image from the asset manager', ['id' => 'b-page-feature-edit', 'class' => 'b-button-withtext']) ?>
        <?= $button('trash-o', 'Remove feature image', ['id' => 'b-page-feature-remove', 'class' => 'b-button-withtext']) ?>
	</section>

	<section>
		<h2><?= Lang::get('boom::settings.feature.from-page') ?></h2>

        <ul class="images-in-page"></ul>
	</section>
    
    <?= $button('times', Lang::get('boom::buttons.cancel'), ['class' => 'b-button-cancel b-button-withtext']) ?>
    <?= $button('save', Lang::get('boom::buttons.save'), ['class' => 'b-button-save b-button-withtext']) ?>
</div>
