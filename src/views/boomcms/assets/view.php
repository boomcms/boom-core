<div id="b-assets-view">
    <div class="b-settings">
        <div class="b-settings-menu">
            <ul>
                <li class="b-settings-close">
                    <a href="#">
                        <span class="fa fa-close"></span>
                        <?= trans('boomcms::asset.close') ?>
                    </a>
                </li>

                <li class="selected">
                    <a href="#b-assets-view-info">
                        <span class="fa fa-info"></span>
                        <?= trans('boomcms::asset.info') ?>
                    </a>
                </li>

                <li>
                    <a href="#b-assets-view-attributes" data-section='attributes'>
                        <span class="fa fa-cogs"></span>
                        <?= trans('boomcms::asset.attributes') ?>
                    </a>
                </li>

                <li>
                    <a href="#b-tags" data-section='tags'>
                        <span class="fa fa-tags"></span>
                        <?= trans('boomcms::asset.tags') ?>
                    </a>
                </li>

                <?php if ($asset->hasMetadata()): ?>
                    <li>
                        <a href="#b-assets-metadata" data-section='metadata'>
                        <span class="fa fa-asterisk"></span>
                            <?= trans('boomcms::asset.metadata') ?>
                        </a>
                    </li>
                <?php endif ?>

                <?php if ($asset->hasPreviousVersions()): ?>
                    <li>
                        <a href="#b-assets-view-files" data-section='history'>
                        <span class="fa fa-history"></span>
                            <?= trans('boomcms::asset.history') ?>
                        </a>
                    </li>
                <?php endif ?>

                <li class='group'>
                    <a href="<?= $assetURL(['asset' => $asset, 'action' => 'download']) ?>">
                        <span class="fa fa-download"></span>
                        <?= trans('boomcms::asset.download') ?>
                    </a>
                </li>

                <li class="b-setting-delete">
                    <a href="#b-assets-delete" data-section='delete'>
                        <span class="fa fa-trash-o"></span>
                        <?= trans('boomcms::asset.delete.heading') ?>
                    </a>
                </li>
            </ul>

            <a href="#" class="toggle">
                <span class="fa fa-caret-right"></span>
                <span class="fa fa-caret-left"></span>
                <span class="text">Toggle menu</span>
            </a>
        </div>

        <div class="b-settings-content">
            <div id="b-assets-view-attributes">
                <h1><?= trans('boomcms::asset.attributes') ?></h1>

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

            <div id="b-assets-view-info" class="selected">
                <h1><?= $asset->getTitle() ?></h1>

                <?php if ($asset->isVideo()): ?>
                    <video
                        src="<?= $assetURL(['asset' => $asset]) ?>"
                        controls
                        poster="<?= $assetURL(['asset' => $asset, 'action' => 'thumb']) ?>"
                    ></video>
                <?php elseif ($asset->isImage()): ?>
                    <div class="b-asset-imageeditor">
                        <img id="b-imageeditor-original" src="<?= $assetURL(['asset' => $asset]) ?>" />

                        <div class="image-container">
                            <img id="b-imageeditor-image" src="<?= $assetURL(['asset' => $asset]) ?>" />
                        </div>

                        <div id="b-imageeditor-toolbar">
                            <?= $button('rotate-left', 'rotate-left', ['id' => 'b-imageeditor-rotate-left']) ?>
                            <?= $button('rotate-right', 'rotate-right', ['id' => 'b-imageeditor-rotate-right']) ?>
                            <?= $button('crop', 'crop', ['id' => 'b-imageeditor-crop']) ?>
                            <?= $button('save', 'save-changes', ['class' => 'b-imageeditor-save b-button-withtext']) ?>
                            <?= $button('refresh', 'image-revert', ['id' => 'b-imageeditor-revert', 'class' => 'b-button-withtext']) ?>

                            <div class="crop-tools">
                                <label class="aspect-ratio">
                                    <p><?= trans('boomcms::asset.aspect-ratio') ?></p>

                                    <select>
                                        <option value="">Fluid</option>
                                        <option value="1">1/1</option>
                                        <option value="1.33333">4/3</option>
                                        <option value="0.5">1/2</option>
                                        <option value="0.75">3/4</option>
                                        <option value="1.77778">16/9</option>
                                    </select>
                                </label>

                                <?= $button('check', 'accept-crop', ['id' => 'b-imageeditor-crop-accept', 'class' => 'b-button-withtext']) ?>
                                <?= $button('times', 'cancel', ['id' => 'b-imageeditor-crop-cancel', 'class' => 'b-button-withtext']) ?>
                             </div>
                        </div>
                    </div>
                <?php else: ?>
                    <img src="<?= $assetURL(['asset' => $asset, 'action' => 'thumb', 'height' => 500]) ?>">
                <?php endif ?>

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
                    <dd>
                        <time datetime="<?= $asset->getUploadedTime()->format('c') ?>"></time>
                    </dd>

                    <?php if (!$asset->isImage()): ?>
                        <dt><?= trans('boomcms::asset.downloads') ?></dt>
                        <dd><?= $asset->getDownloads() ?></dd>
                    <?php endif ?>
                </dl>
            </div>

            <?= view('boomcms::assets.tags', ['tags' => $asset->getTags()]) ?>

            <?php if ($asset->hasMetadata()): ?>
                <div id="b-assets-metadata">
                    <h1><?= trans('boomcms::asset.metadata') ?></h1>

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
                    <h1><?= trans('boomcms::asset.history') ?></h1>
                    <p><?= trans('boomcms::asset.history-intro') ?></p>

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

            <div id="b-assets-delete">
                <h1><?= trans('boomcms::asset.delete.heading') ?></h1>
                <p><?= trans('boomcms::asset.delete.confirm') ?></p>

                <?= $button('trash-o', 'delete-asset', [
                    'class' => 'b-button-withtext b-assets-delete'
                ]) ?>
            </div>
        </div>
    </div>
</div>
