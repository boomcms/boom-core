<div id="b-assets-picker">
    <?= View::make('boomcms::assets.thumbs', ['assets' => []]) ?>

    <section id="b-assets-picker-sidebar">
        <section id="b-assets-picker-current">
            <h1>Current Asset</h1>
            <img src="" />

            <?= $button('trash-o', 'Remove current asset', ['id' => 'b-assets-picker-current-remove', 'class' => 'b-button-withtext']) ?>
        </section>

        <?php if ($auth->loggedIn('manage_assets')): ?>
            <section id="b-assets-picker-upload">
                <h1>Upload Asset</h1>
                <?= View::make('boomcms::assets.upload') ?>
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
                <h2><?= Lang::get('boomcms::asset.search.type') ?></h2>

                <?= View::make('boomcms::assets.search.type') ?>
            </div>

            <div>
                <h2><?= Lang::get('boomcms::asset.search.tag') ?></h2>
                <?= View::make('boomcms::assets.search.tag') ?>
            </div>
        </section>

        <section id="b-assets-pagination" class="b-pagination">
            <a href="#" class="first" data-action="first">&laquo;</a>
            <a href="#" class="previous" data-action="previous">&lsaquo;</a>
            <input type="text" readonly="readonly" data-max-page="" data-current-page="" />
            <a href="#" class="next" data-action="next">&rsaquo;</a>
            <a href="#" class="last" data-action="last">&raquo;</a>
        </section>

        <?= $button('times', 'Close asset picker', ['class' => 'b-button-withtext', 'id' => 'b-assets-picker-close']) ?>
    </section>
</div>
