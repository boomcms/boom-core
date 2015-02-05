<div id="b-assets-picker">
    <?= new View('boom/assets/thumbs', array('assets' => [])) ?>

    <section id="b-assets-picker-sidebar">
        <section id="b-assets-picker-current">
            <h1>Current Asset</h1>
            <img src="" />

            <?= new \Boom\UI\Button('delete', 'Remove current asset', array('id' => 'b-assets-picker-current-remove', 'class' => 'b-button-withtext')) ?>
        </section>

        <section id="b-assets-picker-upload">
            <h1>Upload Asset</h1>
            <?= new View('boom/assets/upload') ?>
        </section>

        <section id="b-assets-picker-filter" class="ui-front">
            <h1>Filter Assets</h1>

            <?= new \Boom\UI\Button('accept', 'All assets', array('id' => 'b-assets-picker-all', 'class' => 'b-button-textonly')) ?>

            <div>
                <h2>Search by asset name</h2>
                <input type='text' id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />
            </div>

            <div>
                <h2>Filter by asset type</h2>
                <?= Form::select('types', array_merge(array('0' => 'Filter by type'), \Boom\Asset\Type::whichExist()), null, array('id' => 'b-assets-types')) ?>
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

        <?= new Boom\UI\Button('cancel', 'Close asset picker', array('class' => 'b-button-withtext', 'id' => 'b-assets-picker-close')) ?>
    </section>
</div>