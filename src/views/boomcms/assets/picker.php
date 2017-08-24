<div id="b-assets-picker" class="<?= Gate::allows('manageAssets', Router::getActiveSite()) ? 'show-edit' : '' ?>">
    <div id="b-assets-picker-content">
        <ul class="b-assets-album-list"></ul>

        <?= view('boomcms::assets.thumbs') ?>
    </div>
   

    <section id="b-assets-picker-sidebar">
        <section id="b-assets-picker-selected">
            <h1><?= trans('boomcms::asset.picker.selected') ?></h1>

            <ul></ul>

            <?= $button('check', 'assets-use', ['id' => 'b-assets-picker-use-selection']) ?>
        </section>

        <section id="b-assets-picker-current">
            <h1><?= trans('boomcms::asset.picker.current') ?></h1>
            <img src="" />

            <?= $button('trash-o', 'Remove current asset', ['id' => 'b-assets-picker-current-remove', 'class' => 'b-button-withtext']) ?>
        </section>

        <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
            <section id="b-assets-picker-upload">
                <h1><?= trans('boomcms::asset.picker.upload') ?></h1>
                <?= view('boomcms::assets.upload') ?>
            </section>
        <?php endif ?>

        <section id="b-assets-search" class="ui-front">
            <h1><?= trans('boomcms::asset.picker.filter') ?></h1>

            <?= view('boomcms::assets.search') ?>
        </section>

        <?= view('boomcms::assets.pagination') ?>

        <?= $button('book', 'albums', ['class' => 'b-button-withtext', 'id' => 'b-assets-picker-albums']) ?>
        <?= $button('times', 'close-asset-picker', ['class' => 'b-button-withtext', 'id' => 'b-assets-picker-close']) ?>
    </section>
</div>

<?= view('boomcms::assets.templates') ?>
