<div id="b-assets-view-thumbs">
    <p id="b-assets-none"><?= trans('boomcms::asset.none') ?></p>
</div>

<script type="text/template" id="b-asset-thumb">
    <div style="height: 160px; width: <%= Math.floor(160 * asset.getAspectRatio()) %>px">
        <div href="#asset/<%= asset.getId() %>" class="thumb">
            <img class="loading" />

            <div class="pace loading">
                <div>
                    <span><?= trans('boomcms::asset.loading') ?></span>
                    <div class="pace-activity"></div>
                </div>
            </div>

            <div class="b-asset-details">
                <h2><%= asset.getTitle() %>></h2>

                <p>
                    <?//= trans('boomcms::asset.type.'.strtolower($asset->getType())) ?><br />

                    <?php /* if ($asset->isImage()): ?>
                        <?= $asset->getWidth() ?> x <?= $asset->getHeight() ?>
                    <?php else: ?>
                        <?= Str::filesize($asset->getFilesize()) ?>
                    <?php endif */ ?>
                </p>
            </div>

            <a href="#" class="edit">
                <span class="fa fa-edit"></span>
            </a>
        </div>
    </div>
</script>
