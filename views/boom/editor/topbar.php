<div id="sledge-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

	<?= Menu::factory('sledge')->sort('priority') ?>

	<div id="s-page-actions">
		<? if ($auth->logged_in('edit_page', $page)): ?>
			<button id="s-page-save" class="sledge-button" disabled="disabled" title="You have no unsaved changes" data-icon="ui-icon-disk">
					<?=__('Save')?>
			</button>
		<? endif; ?>

		<? if ($auth->logged_in('delete_page', $page) AND ! $page->mptt->is_root()): ?>
			<button id="s-page-delete" class="sledge-button" data-icon="ui-icon-trash">
					<?=__('Delete')?>
			</button>
		<? endif; ?>

		<? if ($auth->logged_in('publish_page', $page)): ?>
			<button id="s-page-publish" class="sledge-button"<? if ($page->is_published()): ?> style='display: none'<? endif; ?> data-icon="ui-icon-check">
					<?=__('Publish')?>
			</button>
		<? endif; ?>
	</div>

	<div id="s-page-metadata" class="ui-helper-right">
		<button id="sledge-topbar-editors" class="sledge-button" style="display: none" data-icon="ui-icon-person">
			<?=__('Another editor is viewing this page.')?>
		</button>

		<? if ($auth->logged_in('edit_page', $page)): ?>
			<div id="s-page-settings-menu">
				<button id="s-page-settings" class="sledge-button" data-icon="ui-icon-wrench" data-icon-secondary="ui-icon-triangle-1-s">
						<?=__('Settings')?>
				</button>
			</div>
		<? endif; ?>

		<? if ($auth->logged_in('add_page', $page)): ?>
			<button id="s-page-addpage" class="sledge-button" data-icon="ui-icon-circle-plus">
					<?=__('Add')?> <?=__('page')?>
			</button>
		<? endif; ?>

		<? if ($auth->logged_in('edit_page', $page)): ?>
			<div id="s-page-preview-splitbutton">
				<button id="s-page-preview" class="sledge-button" data-icon="ui-icon-search"
					<? if ( ! $page->is_visible() AND ! $page->has_published_version()):?>
						disabled='disabled' title="A preview of this page is not available because it's invisible and unpublished"
					<? endif; ?>
				>
					<?=__('Preview')?>
				</button>
				<button class="ui-button" data-icon="ui-icon-triangle-1-s"
					<? if ( ! $page->is_visible() AND ! $page->has_published_version()):?>
						disabled='disabled' title="A preview of this page is not available because it's invisible and unpublished"
					<? endif; ?>
				>
					<?=__('Select an action')?>
				</button>
			</div>
		<? endif; ?>
	</div>

	<div id="sledge-topbar-pagesettings" class="ui-helper-clearfix">
		<div class="ui-helper-center">
			<?= View::factory('sledge/editor/page/settings/index');?>
		</div>
	</div>

	<div id="sledge-topbar-revisions" class="ui-helper-clearfix">
		This page is
		<? if ($auth->logged_in('edit', $page)): ?>
			<a href="#" id="sledge-topbar-visibility">
				<strong><?= $page->is_visible()? 'visible' : 'invisible'; ?></strong>
			</a>
		<? else: ?>
			<strong><?= $page->is_visible()? 'visible' : 'invisible'; ?></strong>
		<?endif; ?>
		 and this version is <strong>

		<? if ($page->is_published()): ?>
			published</strong>
		<? else: ?>
			not published</strong>

			<? if ($page->has_published_version()): ?>
				but a <a href='<?= $page->link() ?>?version=<?= $page->published_vid ?>'>published version</a> exists.
			<? endif; ?>
		<? endif; ?>
	</div>

	<?= View::factory('sledge/breadcrumbs'); ?>
</div>
