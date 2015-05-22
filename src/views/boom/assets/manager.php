<div id="b-assets-manager">
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
			<?= $button('upload', Lang::get('Upload files'), ['id' => 'b-assets-upload']) ?>
			<?= $button('edit', Lang::get('View').' / '.Lang::get('Edit'), ['id' => 'b-button-multiaction-edit', 'disabled' => 'disabled']) ?>
			<?= $button('delete', Lang::get('Delete'), ['id' => 'b-button-multiaction-delete', 'disabled' => 'disabled']) ?>
			<?= $button('download', Lang::get('Download'), ['id' => 'b-button-multiaction-download', 'disabled' => 'disabled']) ?>
			<?= $button('tag', Lang::get('Add Tags'), ['id' => 'b-button-multiaction-tag', 'disabled' => 'disabled']) ?>
			<?= $button('cancel', Lang::get('Clear Selection'), ['id' => 'b-button-multiaction-clear', 'disabled' => 'disabled']) ?>
		</div>
	</div>

	<div id="b-assets-filters">
		<span>
			<button id="b-assets-all" class="b-button">
				<?= Lang::get('All assets') ?>
			</button>
		</span>

		<input type='text' class="b-filter-input" id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />

		<?php//= Form::select('types', array_merge(['0' => 'Filter by type'], \Boom\Asset\Type::whichExist()), null, ['id' => 'b-assets-types']) ?>

		<div id='b-tags-search'>
			<input type='text' class="b-filter-input" placeholder="Type a tag name" value="Type a tag name" />
			<ul class="b-tags-list">
			</ul>
		</div>

        <select id="b-assets-sortby">
            <option value="last_modified-desc" selected="selected">Most recent</option>
            <option value="last_modified-asc">Oldest</option>
            <option value="title-asc">Title A - Z</option>
            <option value="title-desc">Title Z - A</option>
            <option value="filesize-asc">Size (smallest)</option>
            <option value="filesize-desc">Size (largest)</option>
            <option value="downloads-desc">Most downloaded</option>
        </select>

        <div id="b-assets-pagination" class="pagination"></div>
	</div>

	<div id="b-assets-content">
		<div id="b-assets-view-thumbs"></div>
	</div>

	<?= View::make('boom::assets.upload') ?>
</div>
