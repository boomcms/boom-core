<div id="b-assets-view-thumbs">
    <?php if (count($assets)): ?>
        <?php foreach ($assets as $asset): ?>
            <div style="height: 160px; width: <?= floor(160 * $asset->getAspectRatio()) ?>px" data-aspect-ratio="<?= $asset->getAspectRatio() ?>">
                <div class="thumb" data-asset="<?= $asset->getId() ?>" tabindex="0" class="loading">
                    <img />

                    <div class="pace progress">
                        <div>
                            <span><?= trans('boomcms::asset.loading') ?></span>
                            <div class="pace-activity"></div>
                        </div>
                    </div>

                    <div class="failed">
                        <div>
                            <span class="fa fa-frown-o"></span>
                            <p><?= trans('boomcms::asset.failed') ?></p>
                        </div>
                    </div>

                    <div class="b-asset-details">
                        <h2><?= $asset->getTitle() ?></h2>

                        <p>
                            <?= trans('boomcms::asset.type.'.strtolower($asset->getType())) ?><br />

                            <?php if ($asset->isImage()): ?>
                                <?= $asset->getWidth() ?> x <?= $asset->getHeight() ?>
                            <?php else: ?>
                                <?= Str::filesize($asset->getFilesize()) ?>
                            <?php endif ?>
                        </p>
                    </div>

                    <a href="#" class="edit">
                        <span class="fa fa-edit"></span>
                    </a>
                </div>
            </div>
        <?php endforeach ?>
    <?php else: ?>
        <p id="b-assets-none"><?= trans('boomcms::asset.none') ?></p>
    <?php endif ?>
</div>
