<div class="b-assets-view">
    <div class="b-settings">
        <div class="b-settings-menu">
            <ul>
                <li class="b-settings-close">
                    <a href="#">
                        <span class="fa fa-close"></span>
                        <?= trans('boomcms::asset.close') ?>
                    </a>
                </li>

                <li>
                    <a href="#b-asset-selection-tags"<% if (section === 'tags') { %> class="selected"<% } %> data-section='tags'>
                        <span class="fa fa-tags"></span>
                        <?= trans('boomcms::asset.tags') ?>
                    </a>
                </li>

                <li class='group'>
                    <a href="#b-assets-selection-download">
                        <span class="fa fa-download"></span>
                        <?= trans('boomcms::asset.selection.download.heading') ?>
                    </a>
                </li>

                <li class="b-setting-delete">
                    <a href="#b-assets-selection-delete"<% if (section === 'delete') { %> class="selected"<% } %> data-section='delete'>
                        <span class="fa fa-trash-o"></span>
                        <?= trans('boomcms::asset.selection.delete.heading') ?>
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
            <div id="b-asset-selection-tags"<% if (section === 'tags') { %> class="selected"<% } %>>
                <h1><?= trans('boomcms::asset.tags') ?></h1>

                <ul class="b-tags">
                </ul>

                <form class="b-tags-add">
                    <input type="text" value="" class="b-tags-add-name" />
                    <?= $button('plus', 'add-tag') ?>
                </form>
            </div>

            <div id="b-assets-selection-download"<% if (section === 'download') { %> class="selected"<% } %>>
                <h1><?= trans('boomcms::asset.selection.download.heading') ?></h1>

                <form id="b-assets-download-filename">
                    <label>
                        <p><?= trans('boomcms::asset.selection.download.filename') ?></p>
                        <input type="text" name="filename" value="<?= trans('boomcms::asset.selection.download.default') ?>" />
                    </label>
                </form>
            </div>

            <div id="b-assets-selection-delete"<% if (section === 'delete') { %> class="selected"<% } %>>
                <h1><?= trans('boomcms::asset.selection.delete.heading') ?></h1>
                <p><?= trans('boomcms::asset.selection.delete.confirm') ?></p>

                <?= $button('trash-o', 'delete-asset', [
                    'class' => 'b-button-withtext b-assets-delete',
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?= view('boomcms::tags.tag') ?>