<div id="b-assets-view">
    <div class="b-assets-preview">
        <img src="<?= $assetURL(['asset' => $asset, 'action' => 'thumb', 'height' => 300]) ?>">

        <div class="b-buttons">
            <?= $button('trash-o', 'Delete', ['class' => 'b-assets-delete']) ?>
            <?= $button('download', 'Download', ['class' => 'b-assets-download']) ?>
            <?= $button('refresh', 'Replace', ['class' => 'b-assets-replace']) ?>

            <?php if ($asset->isImage()): ?>
                <?= $button('edit', Lang::get('boom::asset.openeditor'), ['class' => 'b-assets-openeditor b-button-withtext']) ?>
            <?php endif ?>
        </div>
    </div>

    <div class="boom-tabs">
        <ul>
            <li><a href="#b-assets-view-info"><?= Lang::get('Info') ?></a></li>
            <li><a href="#b-assets-view-attributes"><?= Lang::get('Attributes') ?></a></li>
            <li><a href="#b-tags"><?= Lang::get('Tags') ?></a></li>

            <?php if ($asset->hasPreviousVersions()): ?>
                <li><a href="#b-assets-view-files"><?= Lang::get('boom::asset.previous_versions') ?></a></li>
            <?php endif ?>
        </ul>

        <div id="b-assets-view-attributes">
            <form>
                <label>
                    <?= Lang::get('Title') ?>
                    <input type="text" id="title" name="title" value="<?= $asset->getTitle() ?>" />
                </label>

                <label>
                    <?= Lang::get('Description') ?>
                    <textarea id="description" name="description"><?= $asset->getDescription() ?></textarea>
                </label>

                <label>
                    <?= Lang::get('Credits') ?>
                    <textarea id="credits" name="credits"><?= $asset->getCredits() ?></textarea>
                </label>

                <?php if (!$asset->isImage()): ?>
                    <label for="thumbnail">Thumbnail
                        <input type="text" id="thumbnail" name="thumbnail_asset_id" value="<?= $asset->getThumbnailAssetId() ?>" size="4" />
                    </label>
                <?php endif ?>
            </form>

            <?= $button('save', 'save-changes', ['class' => 'b-assets-save b-button-withtext']) ?>
        </div>

        <div id="b-assets-view-info">
            <dl>
                <dt><?= Lang::get('Type') ?></dt>
                <dd><?= Lang::get('boom::asset.type.'.strtolower($asset->getType())) ?></dd>

                <dt><?= Lang::get('boom::asset.extension') ?></dt>
                <dd><?= $asset->getExtension() ?></dd>

                <dt><?= Lang::get('Filesize') ?></dt>
                <dd><span id='filesize'><?= $asset->getHumanFilesize() ?></dd>

                <?php if ($asset->isImage()): ?>
                    <dt><?= Lang::get('Dimensions') ?></dt>
                    <dd><?= $asset->getWidth() ?> x <?= $asset->getHeight() ?></dd>
                <?php endif ?>

                <?php if ($uploader = $asset->getUploadedBy()): ?>
                    <dt><?= Lang::get('Uploaded by') ?></dt>
                    <dd><?= $uploader->getName() ?></dd>
                <?php endif ?>

                <dt><?= Lang::get('Uploaded on') ?></dt>
                <dd><?= $asset->getUploadedTime()->format('d F Y h:i:s') ?></dd>

                <?php if (!$asset->isImage()): ?>
                    <dt><?= Lang::get('Downloads') ?></dt>
                    <dd><?= $asset->getDownloads() ?></dd>
                <?php endif ?>
            </dl>
        </div>

        <?= View::make('boom::assets.tags', ['tags' => $asset->getTags()]) ?>

        <?php if ($asset->hasPreviousVersions()): ?>
            <div id="b-assets-view-files">
                <p><?= Lang::get('boom::asset.previous_versions_intro') ?></p>

                <ul>
                    <?php foreach ($asset->getVersions() as $version): ?>
                        <li>
                            <div>
                                <img src="<?= URL::route('asset-version', ['id' => $version->getId(), 'width' => '200', 'height' => 0]) ?>" />
                            </div>

                            <div>
                                <h3>Edited by</h3>
                                <p><?= $version->getEditedBy()->getName() ?></p>

                                <h3>Edited at</h3>
                                <time datetime="<?= $version->getEditedAt()->format('c') ?>"><?= $version->getEditedAt()->format('d F Y H:i') ?></time>

                                <?= $button('undo', 'Revert to this version', ['class' => 'b-button-withtext b-assets-revert', 'data-version-id' => $version->getId()]) ?>
                            </div>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif  ?>
    </div>
</div>
