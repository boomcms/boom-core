<?= view('boomcms::header', ['title' => trans('boomcms::asset.manager')]) ?>

<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
            <?= $button('', 'view-assets', ['class' => 'b-button-textonly', 'data-view' => '']) ?>
            <?= $button('', 'view-albums', ['class' => 'b-button-textonly', 'data-view' => 'albums']) ?>

            <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
    			<?= $button('upload', 'upload', ['id' => 'b-assets-upload']) ?>
            <?php endif ?>

			<?= $button('trash-o', 'delete', ['class' => 'b-assets-multi', 'id' => 'b-assets-selection-delete', 'disabled' => 'disabled']) ?>
			<?= $button('download', 'download', ['class' => 'b-assets-multi', 'id' => 'b-assets-selection-download', 'disabled' => 'disabled']) ?>
			<?= $button('tags', 'add-tags', ['class' => 'b-assets-multi', 'id' => 'b-assets-selection-tag', 'disabled' => 'disabled']) ?>

            <a href="#" id="b-assets-select-all"><?= trans('boomcms::asset.select.all') ?></a>
            &nbsp;:&nbsp;
            <a href="#" id="b-assets-select-none"><?= trans('boomcms::asset.select.none') ?></a>

            <?= $button('search', 'search-assets', ['id' => 'b-assets-search', 'class' => 'b-button-withtext']) ?>
            <?= view('boomcms::assets.pagination') ?>
        </div>
	</div>

	<div id="b-assets-filters">
		<?= view('boomcms::assets.search') ?>
        <?= view('boomcms::assets.search.sort') ?>
	</div>

    <div id="b-assets-content">
        <?php if (Gate::allows('uploadAssets', Router::getActiveSite())): ?>
            <?= view('boomcms::assets.upload') ?>
        <?php endif ?>

        <div class="pace">
            <div class="pace-activity"></div>
        </div>

        <div id="b-assets-view-asset-container" class='section'></div>
        <div id="b-assets-view-selection-container" class='section'></div>
        <div id="b-assets-all-albums-container" class='section'></div>
        <div id="b-assets-view-album-container" class='section'></div>
        <div id="b-assets-filmroll"></div>

        <?= view('boomcms::assets.thumbs') ?>
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
