<div id="b-assets-view-thumbs">
	<?php foreach ($assets as $asset): ?>
		<div style="height: 160px; width: <?= floor(160 * $asset->getAspectRatio()) ?>px" data-aspect-ratio="<?= $asset->getAspectRatio() ?>">
			<div class="thumb" data-asset="<?= $asset->getId() ?>">
				<a href="#asset/<?= $asset->getId() ?>">
					<img />

					<section class="b-asset-details">
						<h2><?= $asset->getTitle() ?></h2>

						<p>
							<strong>Type</strong> <?= Lang::get('boomcms::asset.type.'.strtolower($asset->getType())) ?><br />
							<strong>Description</strong> <?= ($asset->getDescription()) ? $asset->getDescription() : 'None set' ?><br />

							<?php if ($asset->getDownloads()) : ?>
								<strong>Downloads</strong> <?= $asset->getDownloads() ?><br />
							<?php endif ?>

							<?php if ($asset->isImage() && $asset->getWidth() && $asset->getHeight()): ?>
								<strong>Dimensions</strong> <?= $asset->getWidth() ?> x <?= $asset->getHeight() ?><br />
							<?php endif ?>
						</p>
					</section>
				</a>
			</div>
		</div>
	<?php endforeach ?>
</div>
