<div id="b-assets-picker">
    <?= view('boomcms::assets.thumbs', ['assets' => []]) ?>

    <section id="b-assets-picker-sidebar">
        <section id="b-assets-picker-current">
            <h1><?= trans('boomcms::asset.picker.current') ?></h1>
            <img src="" />

            <?= $button('trash-o', 'Remove current asset', ['id' => 'b-assets-picker-current-remove', 'class' => 'b-button-withtext']) ?>
        </section>

        <?php if (Gate::allows('manageAssets', $request)): ?>
            <section id="b-assets-picker-upload">
                <h1><?= trans('boomcms::asset.picker.upload') ?></h1>
                <?= view('boomcms::assets.upload') ?>
            </section>
        <?php endif ?>

        <section id="b-assets-picker-filter" class="ui-front">
            <h1><?= trans('boomcms::asset.picker.filter') ?></h1>

            <?= $button('accept', 'All assets', ['id' => 'b-assets-picker-all', 'class' => 'b-button-textonly']) ?>

            <div>
                <h2><?= trans('boomcms::asset.search.title') ?></h2>
                <?= view('boomcms::assets.search.title') ?>
            </div>

            <div>
                <h2><?= trans('boomcms::asset.search.type') ?></h2>

                <?= view('boomcms::assets.search.type') ?>
            </div>

            <div>
                <h2><?= trans('boomcms::asset.search.tag') ?></h2>
                <?= view('boomcms::assets.search.tag') ?>
            </div>
        </section>

        <?= view('boomcms::assets.pagination') ?>
        <?= $button('times', 'Close asset picker', ['class' => 'b-button-withtext', 'id' => 'b-assets-picker-close']) ?>
    </section>
</div>
