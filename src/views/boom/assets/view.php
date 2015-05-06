<div id="b-assets-view">
    <div class="b-assets-preview">
        <img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->getId(), 'width' => 0, 'height' => 300, 'quality' => 85, 'crop' => 0)) ?>">

        <div class="ui-dialog-buttonpane">
            <?= new \Boom\UI\Button('delete', Lang::get('Delete'), array('class' => 'b-assets-delete')) ?>
            <?= new \Boom\UI\Button('download', Lang::get('Download'), array('class' => 'b-assets-download')) ?>
            <?//= \Boom\UI\Button('replace', Lang::get('Replace'), array('class' => 'b-assets-replace')) ?>
        </div>
    </div>

    <div class="boom-tabs">
        <ul>
            <li><a href="#b-assets-view-info"><?=Lang::get('Info')?></a></li>
            <li><a href="#b-assets-view-attributes"><?=Lang::get('Attributes')?></a></li>
            <li><a href="#b-tags"><?=Lang::get('Tags')?></a></li>

            <? if (count($asset->getOldFiles()) > 0): ?>
                <li><a href="#b-assets-view-files"><?=Lang::get('Previous Files')?></a></li>
            <? endif ?>
        </ul>

        <div id="b-assets-view-attributes">
            <form>
                <label>
                    <?=Lang::get('Title')?>
                    <input type="text" id="title" name="title" value="<?= $asset->getTitle() ?>" />
                </label>

                <label>
                    <?=Lang::get('Description')?>
                    <textarea id="description" name="description"><?= $asset->getDescription() ?></textarea>
                </label>

                <label>
                    <?=Lang::get('Credits')?>
                    <textarea id="credits" name="credits"><?= $asset->getCredits() ?></textarea>
                </label>

                <label>
                    <?= Lang::get('Visible from') ?>
                    <input type="text" id="visible_from" name="visible_from" class="boom-datepicker" value="<?= $asset->getVisibleFrom()->format('d F Y h:m') ?>" />
                </label>

                <? if ( ! $asset instanceof \Boom\Asset\Type\Image): ?>
                    <label for="thumbnail">Thumbnail
                        <input type="text" id="thumbnail" name="thumbnail_asset_id" value="<?= $asset->getThumbnailAssetId() ?>" size="4" />
                    </label>
                <? endif ?>
            </form>
        </div>

        <div id="b-assets-view-info">
            <dl>
                <dt><?=Lang::get('Type')?></dt>
                <dd><?= $asset->getType() ?></dd>

                <dt><?=Lang::get('Filesize')?></dt>
                <dd><span id='filesize'><?= Text::bytes($asset->getFilesize()) ?></dd>

                <? if ($asset instanceof \Boom\Asset\Type\Image): ?>
                    <dt><?=Lang::get('Dimensions')?></dt>
                    <dd><?=$asset->getWidth()?> x <?=$asset->getHeight()?></dd>
                <? endif; ?>

                <? if ($uploader = $asset->getUploadedBy()): ?>
                    <dt><?=Lang::get('Uploaded by')?></dt>
                    <dd><?= $uploader->name ?></dd>
                <? endif; ?>

                <dt><?=Lang::get('Uploaded on')?></dt>
                <dd><?= $asset->getUploadedTime()->format('d F Y h:i:s') ?></dd>

                <? if ( ! $asset instanceof \Boom\Asset\Type\Image): ?>
                    <dt><?=Lang::get('Downloads')?></dt>
                    <dd><?= Num::format($asset->getDownloads(), 0) ?></dd>
                <? endif ?>
            </dl>
        </div>

        <?= new View('boom/assets/tags', ['tags' => $asset->getTags()]) ?>

        <? if (count($asset->getOldFiles()) > 0): ?>
            <div id="b-assets-view-files">
                <p>
                    These files were previously assigned to this asset but were replaced.
                </p>
                <ul>
                    <? foreach ($asset->getOldFiles() as $timestamp => $filename): ?>
                        <li>
                            <a href="/cms/assets/restore/<?= $asset->getId() ?>?timestamp=<?= $timestamp ?>">
                                <img src="<?= Route::url('asset', array('action' => 'thumb', 'id' => $asset->getId(), 'width' => 160, 'height' => 160, 'quality' => 85, 'crop' => 1)) ?><? if ($timestamp): ?>?timestamp=<?= $timestamp ?><? endif; ?>" />
                            </a>
                            <?=date("d F Y H:i", $timestamp);?>
                        </li>
                    <? endforeach ?>
                </ul>
            </div>
        <? endif ?>
    </div>
</div>