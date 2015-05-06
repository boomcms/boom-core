<div id="b-assets-manager">
	<?= new \BoomCMS\Core\UI\MenuButton() ?>
	<?= (new \BoomCMS\Core\Menu\Menu($auth))->render()  ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
			<?= new \BoomCMS\Core\UI\Button('upload', Lang::get('Upload files'), ['id' => 'b-assets-upload']) ?>
			<?= new \BoomCMS\Core\UI\Button('edit', Lang::get('View').' / '.Lang::get('Edit'), ['id' => 'b-button-multiaction-edit', 'disabled' => 'disabled']) ?>
			<?= new \BoomCMS\Core\UI\Button('delete', Lang::get('Delete'), ['id' => 'b-button-multiaction-delete', 'disabled' => 'disabled']) ?>
			<?= new \BoomCMS\Core\UI\Button('download', Lang::get('Download'), ['id' => 'b-button-multiaction-download', 'disabled' => 'disabled']) ?>
			<?= new \BoomCMS\Core\UI\Button('tag', Lang::get('Add Tags'), ['id' => 'b-button-multiaction-tag', 'disabled' => 'disabled']) ?>
			<?= new \BoomCMS\Core\UI\Button('cancel', Lang::get('Clear Selection'), ['id' => 'b-button-multiaction-clear', 'disabled' => 'disabled']) ?>
		</div>
	</div>

	<div id="b-assets-filters">
		<span>
			<button id="b-assets-all" class="b-button">
				<?=Lang::get('All assets')?>
			</button>
		</span>

		<input type='text' class="b-filter-input" id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />

		<?= Form::select('types', array_merge(['0' => 'Filter by type'], \Boom\Asset\Type::whichExist()), null, ['id' => 'b-assets-types']) ?>

		<div id='b-tags-search'>
			<input type='text' class="b-filter-input" placeholder="Type a tag name" value="Type a tag name" />
			<ul class="b-tags-list">
			</ul>
		</div>

		<?= Form::select('', [
            'last_modified-desc' => 'Most recent',
            'last_modified-asc' => 'Oldest',
            'title-asc' => 'Title A - Z',
            'title-desc' => 'Title Z - A',
            'filesize-asc' => 'Size (smallest)',
            'filesize-desc' => 'Size (largest)',
            'downloads-desc' => 'Most downloaded'
            ], 'last_modified-desc', ['id' => 'b-assets-sortby'])
        ?>

        <div id="b-assets-pagination" class="pagination"></div>
	</div>

	<div id="b-assets-content">
		<div id="b-assets-view-thumbs"></div>
	</div>

	<?= new View('boom/assets/upload') ?>
</div>
