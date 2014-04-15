<div id="b-assets-manager">
	<? if ( ! Request::current()->is_ajax()): ?>
		<div id="b-topbar" class="b-asset-manager b-toolbar">
			<?= Menu::factory('boom')->sort('priority')  ?>

			<div id="b-assets-buttons">
				<?= BoomUI::button('upload', __('Upload files'), array('id' => 'b-assets-upload')) ?>
				<?= BoomUI::button('edit', __('View').' / '.__('Edit'), array('id' => 'b-button-multiaction-edit', 'disabled' => 'disabled')) ?>
				<?= BoomUI::button('delete', __('Delete'), array('id' => 'b-button-multiaction-delete', 'disabled' => 'disabled')) ?>
				<?= BoomUI::button('download', __('Download'), array('id' => 'b-button-multiaction-download', 'disabled' => 'disabled')) ?>
				<?= BoomUI::button('tag', __('Add Tags'), array('id' => 'b-button-multiaction-tag', 'disabled' => 'disabled')) ?>
				<?= BoomUI::button('cancel', __('Clear Selection'), array('id' => 'b-button-multiaction-clear', 'disabled' => 'disabled')) ?>
			</div>

			<div id="b-assets-pagination"></div>
			<div id="b-assets-stats"></div>
		</div>
	<? endif ?>

	<div id="b-assets-filters">
		<span>
			<button id="b-assets-all" class="b-button">
				<?=__('All assets')?>
			</button>
		</span>

		<input type='text' class="b-filter-input" id="b-assets-filter-title" placeholder="Search by asset name" value="Search by asset name" />

		<?= Form::select('types', array_merge(array('0' => 'Filter by type'), ||M::factory('Asset')->types()), null, array('id' => 'b-assets-types')) ?>

		<div id='b-tags-search'>
			<span class="ui-icon ui-icon-boom-tag"></span>
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
	</div>
	<div id="b-assets-content"></div>
</div>