<div id="b-assets-manager">
	<?= \Boom\UI::menuButton() ?>
	<?= new \Boom\Menu\Menu  ?>

	<div id="b-topbar" class="b-asset-manager b-toolbar">
		<div id="b-assets-buttons">
			<?= new \Boom\UI\Button('upload', __('Upload files'), array('id' => 'b-assets-upload')) ?>
			<?= new \Boom\UI\Button('edit', __('View').' / '.__('Edit'), array('id' => 'b-button-multiaction-edit', 'disabled' => 'disabled')) ?>
			<?= new \Boom\UI\Button('delete', __('Delete'), array('id' => 'b-button-multiaction-delete', 'disabled' => 'disabled')) ?>
			<?= new \Boom\UI\Button('download', __('Download'), array('id' => 'b-button-multiaction-download', 'disabled' => 'disabled')) ?>
			<?= new \Boom\UI\Button('tag', __('Add Tags'), array('id' => 'b-button-multiaction-tag', 'disabled' => 'disabled')) ?>
			<?= new \Boom\UI\Button('cancel', __('Clear Selection'), array('id' => 'b-button-multiaction-clear', 'disabled' => 'disabled')) ?>
		</div>
	</div>

	<div id="b-assets-filters">
		<span>
			<button id="b-assets-all" class="b-button">
				<?=__('All assets')?>
			</button>
		</span>

		<input type='text' class="b-filter-input" id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />

		<?= Form::select('types', array_merge(array('0' => 'Filter by type'), \Boom\Asset\Type::whichExist()), null, array('id' => 'b-assets-types')) ?>

		<div id='b-tags-search'>
			<input type='text' class="b-filter-input" placeholder="Type a tag name" value="Type a tag name" />
			<ul class="b-tags-list">
			</ul>
		</div>

		<?= Form::select('', array(
			'last_modified-desc' => 'Most recent',
			'last_modified-asc' => 'Oldest',
			'title-asc' => 'Title A - Z',
			'title-desc' => 'Title Z - A',
			'filesize-asc' => 'Size (smallest)',
			'filesize-desc' => 'Size (largest)',
			'downloads-desc' => 'Most downloaded'
			), 'last_modified-desc', array('id' => 'b-assets-sortby'))
		?>

        <div id="b-assets-pagination" class="pagination"></div>
	</div>

	<div id="b-assets-content">
		<div id="b-assets-view-thumbs"></div>
	</div>

	<?= new View('boom/assets/upload') ?>
</div>