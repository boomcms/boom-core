<?= view('boomcms::header', ['title' => trans('boomcms::asset.manager')]) ?>

<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
			<?= $button('upload', 'upload', ['id' => 'b-assets-upload']) ?>
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
        <?= view('boomcms::assets.upload') ?>
        <?= view('boomcms::assets.thumbs') ?>
    </div>
</div>

<script type="text/template" id="b-assets-view-template">
    <?= view('boomcms::assets.view') ?>
</script>

<script type="text/template" id="b-assets-selection-template">
    <?= view('boomcms::assets.selection') ?>
</script>

<script type="text/javascript">
    window.onload = function() {
        new BoomCMS.AssetManager();
    };
</script>

<?= view('boomcms::footer') ?>
