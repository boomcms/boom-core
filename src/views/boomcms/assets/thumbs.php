<div id="b-assets-view-thumbs">
	<?php foreach ($assets as $asset): ?>
		<div style="height: 160px; width: <?= floor(160 * $asset->getAspectRatio()) ?>px" data-aspect-ratio="<?= $asset->getAspectRatio() ?>">
			<a href="#" class="thumb" data-asset="<?= $asset->getId() ?>">
                <img />

                <div class="b-asset-details">
                    <h2><?= $asset->getTitle() ?></h2>

                    <p>
                        <?= Lang::get('boomcms::asset.type.'.strtolower($asset->getType())) ?><br />
                    
                        <?php if ($asset->isImage()): ?>
                            <?= $asset->getWidth() ?> x <?= $asset->getHeight() ?>
                        <?php else: ?>
                            <?= $asset->getHumanFilesize() ?>
                        <?php endif ?>
                    </p>
                </div>
			</a>
		</div>
	<?php endforeach ?>
</div>
