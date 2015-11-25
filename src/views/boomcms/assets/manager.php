<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
			<?= $button('upload', 'upload', ['id' => 'b-assets-upload']) ?>
			<?= $button('edit', 'view-edit', ['id' => 'b-button-multiaction-edit', 'disabled' => 'disabled']) ?>
			<?= $button('trash-o', 'delete', ['id' => 'b-button-multiaction-delete', 'disabled' => 'disabled']) ?>
			<?= $button('download', 'download', ['id' => 'b-button-multiaction-download', 'disabled' => 'disabled']) ?>
			<?= $button('tags', 'add-tags', ['id' => 'b-button-multiaction-tag', 'disabled' => 'disabled']) ?>
		
            <a href="#" id="b-assets-select-all"><?= Lang::get('Select all') ?></a>
            &nbsp;:&nbsp;
            <a href="#" id="b-assets-select-none"><?= Lang::get('Select none') ?></a>
        </div>
	</div>

	<div id="b-assets-filters">
		<span>
			<button id="b-assets-all" class="b-button">
				<?= Lang::get('All assets') ?>
			</button>
		</span>

		<input type='text' class="b-filter-input" id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />

        <?= View::make('boomcms::assets.search.type') ?>
        <?= View::make('boomcms::assets.search.tag') ?>
        <?= View::make('boomcms::assets.search.sort') ?>

        <div id="b-assets-pagination" class="b-pagination"></div>
	</div>

	<div id="b-assets-content">
		<div id="b-assets-view-thumbs"></div>
	</div>

	<?= View::make('boomcms::assets.upload') ?>
</div>
