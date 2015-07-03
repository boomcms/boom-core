<div id="b-assets-view">
    <div class="b-assets-preview">
        <img src="<?= $assetURL(['asset' => $asset->getId(), 'action' => 'thumb', 'height' => 300]) ?>">

        <div class="ui-dialog-buttonpane">
            <?= $button('trash-o', Lang::get('Delete'), ['class' => 'b-assets-delete']) ?>
            <?= $button('download', Lang::get('Download'), ['class' => 'b-assets-download']) ?>
        </div>
    </div>

    <div class="boom-tabs">
        <ul>
            <li><a href="#b-assets-view-info"><?= Lang::get('Info') ?></a></li>
            <li><a href="#b-assets-view-attributes"><?= Lang::get('Attributes') ?></a></li>
            <li><a href="#b-tags"><?= Lang::get('Tags') ?></a></li>

            <?php if (count($asset->getOldFiles()) > 0): ?>
                <li><a href="#b-assets-view-files"><?= Lang::get('Previous Files') ?></a></li>
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

                <?php if ( ! $asset->isImage()): ?>
                    <label for="thumbnail">Thumbnail
                        <input type="text" id="thumbnail" name="thumbnail_asset_id" value="<?= $asset->getThumbnailAssetId() ?>" size="4" />
                    </label>
                <?php endif ?>
            </form>
        </div>

        <div id="b-assets-view-info">
            <dl>
                <dt><?= Lang::get('Type') ?></dt>
                <dd><?= $asset->getType() ?></dd>

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

                <?php if ( ! $asset->isImage()): ?>
                    <dt><?= Lang::get('Downloads') ?></dt>
                    <dd><?= $asset->getDownloads() ?></dd>
                <?php endif ?>
            </dl>
        </div>

        <?= View::make('boom::assets.tags', ['tags' => $asset->getTags()]) ?>

        <?php if (count($asset->getOldFiles()) > 0): ?>
            <div id="b-assets-view-files">
                <p>
                    These files were previously assigned to this asset but were replaced.
                </p>
                <ul>
                    <?php foreach ($asset->getOldFiles() as $timestamp => $filename): ?>
                        <li>
                            <a href="/cms/assets/restore/<?= $asset->getId() ?>?timestamp=<?= $timestamp ?>">
                                <img src="<?= $assetURL(['asset' => $asset->getId(), 'action' => 'crop', 'width' => 160, 'height' => 160]) ?><?php if ($timestamp): ?>?timestamp=<?= $timestamp ?><?php endif ?>" />
                            </a>
                            <?= date("d F Y H:i", $timestamp) ?>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>
    </div>
</div>
