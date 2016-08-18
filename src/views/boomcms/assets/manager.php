<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
			<?= $button('upload', 'upload', ['id' => 'b-assets-upload']) ?>
			<?= $button('trash-o', 'delete', ['id' => 'b-button-multiaction-delete', 'disabled' => 'disabled']) ?>
			<?= $button('download', 'download', ['id' => 'b-button-multiaction-download', 'disabled' => 'disabled']) ?>
			<?= $button('tags', 'add-tags', ['id' => 'b-button-multiaction-tag', 'disabled' => 'disabled']) ?>

            <a href="#" id="b-assets-select-all"><?= trans('Select all') ?></a>
            &nbsp;:&nbsp;
            <a href="#" id="b-assets-select-none"><?= trans('Select none') ?></a>

            <?= $button('search', 'search-assets', ['id' => 'b-assets-search', 'class' => 'b-button-withtext']) ?>
            <?= view('boomcms::assets.pagination') ?>
        </div>
	</div>

	<div id="b-assets-filters">
        <?= view('boomcms::assets.search') ?>
        <?= view('boomcms::assets.search.sort') ?>
	</div>

    <div id="b-assets-content">
        <?= view('boomcms::assets.thumbs', ['assets' => []]) ?>
    </div>

	<?= view('boomcms::assets.upload') ?>
</div>

<script type="text/template" id="b-image-editor-template">
    <?= view('boomcms::assets.image-editor') ?>
</script>
