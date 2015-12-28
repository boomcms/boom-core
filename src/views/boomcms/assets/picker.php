<div id="b-assets-picker">
    <?= view('boomcms::assets.thumbs', ['assets' => []]) ?>

    <section id="b-assets-picker-sidebar">
        <section id="b-assets-picker-current">
            <h1>Current Asset</h1>
            <img src="" />

            <?= $button('trash-o', 'Remove current asset', ['id' => 'b-assets-picker-current-remove', 'class' => 'b-button-withtext']) ?>
        </section>

        <?php if ($auth->check('manage_assets', $request)): ?>
            <section id="b-assets-picker-upload">
                <h1>Upload Asset</h1>
                <?= view('boomcms::assets.upload') ?>
            </section>
        <?php endif ?>

        <section id="b-assets-picker-filter" class="ui-front">
            <h1>Filter Assets</h1>

            <?= $button('accept', 'All assets', ['id' => 'b-assets-picker-all', 'class' => 'b-button-textonly']) ?>

            <div>
                <h2>Search by asset name</h2>
                <input type='text' id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />
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
