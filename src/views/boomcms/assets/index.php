<?= view('boomcms::header', ['title' => trans('boomcms::asset.manager')]) ?>

<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
        <div id="tab-controls">
            <?= $button('th', 'view-assets', ['data-view' => '']) ?>

            <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
                <?= $button('upload', 'upload', ['data-view' => 'upload']) ?>
            <?php endif ?>

            <?= $button('search', 'search-assets', ['data-view' => 'search']) ?>
        </div>

        <div id="selection-controls">
            <?= $button('trash-o', 'delete', ['class' => 'b-assets-multi', 'id' => 'b-assets-selection-delete', 'disabled' => 'disabled']) ?>
            <?= $button('download', 'download', ['class' => 'b-assets-multi', 'id' => 'b-assets-selection-download', 'disabled' => 'disabled']) ?>
            <?= $button('book', 'albums', ['class' => 'b-assets-multi', 'id' => 'b-assets-selection-albums', 'disabled' => 'disabled']) ?>

            <a href="#" id="b-assets-select-all"><?= trans('boomcms::asset.select.all') ?></a>
            &nbsp;:&nbsp;
            <a href="#" id="b-assets-select-none"><?= trans('boomcms::asset.select.none') ?></a>
        </div>

        <?= view('boomcms::assets.pagination') ?>
	</div>

    <div id="b-assets-tabs">
        <div id="b-assets-filters">
            <?= view('boomcms::assets.search') ?>
            <?= view('boomcms::assets.search.sort') ?>
        </div>

        <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
            <?= view('boomcms::assets.upload') ?>
        <?php endif ?>
        
        <div id="b-assets-all-albums-container"></div>

        <div id="b-assets-content">
            <div id="b-assets-view-asset-container"></div>
            <div id="b-assets-view-selection-container"></div>
            <div id="b-assets-view-album-container"></div>
            <div id="b-assets-filmroll"></div>

            <?= view('boomcms::assets.thumbs') ?>
        </div>
    </div>
</div>

<script type="text/template" id="b-assets-view-template">
    <?= view('boomcms::assets.view') ?>
</script>

<script type="text/template" id="b-assets-selection-template">
    <?= view('boomcms::assets.selection') ?>
</script>

<script type="text/template" id="b-assets-all-albums-template">
    <div id='b-assets-all-albums'>
        <ul>
            <% for (var i = 0; i < albums.models.length; i++) { %>
                <% var album = albums.models[i] %>

                <li>
                    <a href='#albums/<%= album.getId() %>'>
                        <div>
                            <h3><%= album.getName() %></h3>
                            <p><%= album.getAssetCount() %> asset<% if (album.getAssetCount() !== 1) { %>s<% } %>
                        </div>
                    </a>
                </li>
            <% } %>
        </ul>
    </div>
</script>

<script type='text/template' id='b-assets-view-album-template'>
    <div id='b-assets-view-album'>
        <h2><%= album.getName() %>
    </div>
</script>

<script defer type="text/javascript" src="/vendor/boomcms/boom-core/js/asset-manager.js"></script>

<script type="text/javascript">
    window.onload = function() {
        new BoomCMS.AssetManager({
            albums: new BoomCMS.Collections.Albums(<?= Album::all() ?>)
        });
    };
</script>

<?= view('boomcms::footer') ?>
