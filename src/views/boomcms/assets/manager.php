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
        </div>
	</div>

	<div id="b-assets-filters">
		<span>
			<button id="b-assets-all" class="b-button">
				<?= trans('All assets') ?>
			</button>
		</span>

        <?= view('boomcms::assets.search.title') ?>
        <?= view('boomcms::assets.search.type') ?>
        <?= view('boomcms::assets.search.tag') ?>
        <?= view('boomcms::assets.search.sort') ?>
        <?= view('boomcms::assets.pagination') ?>
	</div>

    <div id="b-assets-content">
        <?= view('boomcms::assets.thumbs', ['assets' => []]) ?>
    </div>

	<?= view('boomcms::assets.upload') ?>
</div>
