<div id="b-assets-picker">
    <?= View::make('boom/assets/thumbs', ['assets' => []]) ?>

    <section id="b-assets-picker-sidebar">
        <section id="b-assets-picker-current">
            <h1>Current Asset</h1>
            <img src="" />

            <?= new \BoomCMS\Core\UI\Button('delete', 'Remove current asset', ['id' => 'b-assets-picker-current-remove', 'class' => 'b-button-withtext']) ?>
        </section>

        <section id="b-assets-picker-upload">
            <h1>Upload Asset</h1>
            <?= View::make('boom::assets.upload') ?>
        </section>

        <section id="b-assets-picker-filter" class="ui-front">
            <h1>Filter Assets</h1>

            <?= new \BoomCMS\Core\UI\Button('accept', 'All assets', ['id' => 'b-assets-picker-all', 'class' => 'b-button-textonly']) ?>

            <div>
                <h2>Search by asset name</h2>
                <input type='text' id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />
            </div>

            <div>
                <h2>Filter by asset type</h2>

                <select name="types" id="b-assets-types">
                    <option value="0">Filter by type</option>

                    <?php foreach (\BoomCMS\Core\Asset\Type::whichExist() as $key => $type): ?>
                        <option value="<?= $key ?>"><?= $type ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div>
                <h2>Filter by tag</h2>
                <div id='b-tags-search'>
                    <input type='text' class="b-filter-input" placeholder="Type a tag name" value="Type a tag name" />
                    <ul class="b-tags-list">
                    </ul>
                </div>
            </div>
        </section>

        <section id="b-assets-pagination" class="pagination">
            <a href="#" class="first" data-action="first">&laquo;</a>
            <a href="#" class="previous" data-action="previous">&lsaquo;</a>
            <input type="text" readonly="readonly" data-max-page="" data-current-page="" />
            <a href="#" class="next" data-action="next">&rsaquo;</a>
            <a href="#" class="last" data-action="last">&raquo;</a>
        </section>

        <?= new BoomCMS\Core\UI\Button('cancel', 'Close asset picker', ['class' => 'b-button-withtext', 'id' => 'b-assets-picker-close']) ?>
    </section>
</div>
