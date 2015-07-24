<div id="b-assets-view-thumbs" class="ui-helper-left">
	<?php foreach ($assets as $asset): ?>
		<div style="height: 160px; width: <?= floor(160 * $asset->getAspectRatio()) ?>px" data-aspect-ratio="<?= $asset->getAspectRatio() ?>">
			<div class="thumb" data-asset="<?= $asset->getId() ?>">
				<a href="#asset/<?= $asset->getId() ?>">
					<img src="/asset/thumb/<?= $asset->getId() ?>/400/0" />

					<section class="b-asset-details">
						<h1><?= $asset->getTitle() ?></h1>

						<p>
							<strong>Type</strong> <?= $asset->getType() ?><br />
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
