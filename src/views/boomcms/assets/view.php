<div id="b-assets-view">
    <div class="b-assets-preview">
        <img src="<?= $assetURL(['asset' => $asset, 'action' => 'thumb', 'height' => 300]) ?>">

        <div class="b-buttons">
            <?= $button('trash-o', 'Delete', ['class' => 'b-assets-delete']) ?>
            <?= $button('download', 'Download', ['class' => 'b-assets-download']) ?>
            <?= $button('refresh', 'Replace', ['class' => 'b-assets-replace']) ?>

            <?php if ($asset->isImage()): ?>
                <?= $button('edit', trans('boomcms::asset.openeditor'), ['class' => 'b-assets-openeditor b-button-withtext']) ?>
            <?php endif ?>
        </div>
    </div>

    <div class="boom-tabs">
        <ul>
            <li>
                <a href="#b-assets-view-info">
                    <?= trans('boomcms::asset.info') ?>
                </a>
            </li>

            <li>
                <a href="#b-assets-view-attributes">
                    <?= trans('boomcms::asset.attributes') ?>
                </a>
            </li>

            <li>
                <a href="#b-tags">
                    <?= trans('boomcms::asset.tags') ?>
                </a>
            </li>

            <?php if ($asset->hasMetadata()): ?>
                <li><a href="#b-assets-metadata"><?= trans('boomcms::asset.metadata') ?></a></li>
            <?php endif ?>

            <?php if ($asset->hasPreviousVersions()): ?>
                <li><a href="#b-assets-view-files"><?= trans('boomcms::asset.previous_versions') ?></a></li>
            <?php endif ?>
        </ul>

        <div id="b-assets-view-attributes">
            <form>
                <label>
                    <?= trans('boomcms::asset.title') ?>
                    <input type="text" id="title" name="title" value="<?= $asset->getTitle() ?>" />
                </label>

                <label>
                    <?= trans('boomcms::asset.description') ?>
                    <textarea id="description" name="description"><?= $asset->getDescription() ?></textarea>
                </label>

                <label>
                    <?= trans('boomcms::asset.credits') ?>
                    <textarea id="credits" name="credits"><?= $asset->getCredits() ?></textarea>
                </label>

                <?php if (!$asset->isImage()): ?>
                    <label for="thumbnail">
                        <?= trans('boomcms::asset.thumbnail') ?>
                        <input type="text" id="thumbnail" name="thumbnail_asset_id" value="<?= $asset->getThumbnailAssetId() ?>" size="4" />
                    </label>
                <?php endif ?>
            </form>

            <?= $button('save', 'save-changes', ['class' => 'b-assets-save b-button-withtext']) ?>
        </div>

        <div id="b-assets-view-info">
            <dl>
                <dt><?= trans('boomcms::asset.type-heading') ?></dt>
                <dd><?= trans('boomcms::asset.type.'.strtolower($asset->getType())) ?></dd>

                <dt><?= trans('boomcms::asset.extension') ?></dt>
                <dd><?= $asset->getExtension() ?></dd>

                <dt><?= trans('boomcms::asset.filesize') ?></dt>
                <dd><span id='filesize'><?= Str::filesize($asset->getFilesize()) ?></dd>

                <?php if ($asset->isImage()): ?>
                    <dt><?= trans('boomcms::asset.dimensions') ?></dt>
                    <dd><?= $asset->getWidth() ?> x <?= $asset->getHeight() ?></dd>
                <?php endif ?>

                <?php if ($uploader = $asset->getUploadedBy()): ?>
                    <dt><?= trans('boomcms::asset.uploaded-by') ?></dt>
                    <dd><?= $uploader->getName() ?></dd>
                <?php endif ?>

                <dt><?= trans('boomcms::asset.uploaded-on') ?></dt>
                <dd><?= $asset->getUploadedTime()->format('d F Y h:i:s') ?></dd>

                <?php if (!$asset->isImage()): ?>
                    <dt><?= trans('boomcms::asset.downloads') ?></dt>
                    <dd><?= $asset->getDownloads() ?></dd>
                <?php endif ?>
            </dl>
        </div>

        <?= view('boomcms::assets.tags', ['tags' => $asset->getTags()]) ?>

        <?php if ($asset->hasMetadata()): ?>
            <div id="b-assets-metadata">
                <dl>
                    <?php foreach ($asset->getMetadata() as $key => $value): ?>
                        <dt><?= $key ?></dt>
                        <dd><?= $value ?></dd>
                    <?php endforeach ?>
                </dl>
            </div>
        <?php endif ?>

        <?php if ($asset->hasPreviousVersions()): ?>
            <div id="b-assets-view-files">
                <p><?= trans('boomcms::asset.previous_versions_intro') ?></p>

                <ul>
                    <?php foreach ($asset->getVersions() as $version): ?>
                        <li>
                            <div>
                                <img src="<?= URL::route('asset-version', ['id' => $version->getId(), 'width' => '200', 'height' => 0]) ?>" />
                            </div>

                            <div>
                                <h3><?= trans('boomcms::asset.edited-by') ?></h3>
                                <p><?= $version->getEditedBy()->getName() ?></p>

                                <h3><?= trans('boomcms::asset.edited-at') ?></h3>
                                <time datetime="<?= $version->getEditedAt()->format('c') ?>"><?= $version->getEditedAt()->format('d F Y H:i') ?></time>

                                <?= $button('undo', 'asset-revert', ['class' => 'b-button-withtext b-assets-revert', 'data-version-id' => $version->getId()]) ?>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif  ?>
    </div>
</div>
