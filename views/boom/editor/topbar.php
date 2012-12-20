<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

	<?= Menu::factory('boom')->sort('priority') ?>

	<div id="b-page-actions">
		<? if ($auth->logged_in('edit_page', $page)): ?>
			<button id="b-page-save" class="boom-button" disabled="disabled" title="You have no unsaved changes" data-icon="ui-icon-disk">
					<?=__('Save')?>
			</button>
		<? endif; ?>

		<? if ($auth->logged_in('delete_page', $page) AND ! $page->mptt->is_root()): ?>
			<button id="b-page-delete" class="boom-button" data-icon="ui-icon-trash">
					<?=__('Delete')?>
			</button>
		<? endif; ?>

		<? if ($auth->logged_in('publish_page', $page)): ?>
			<button id="b-page-publish" class="boom-button"<? if ($page->is_published()): ?> style='display: none'<? endif; ?> data-icon="ui-icon-check">
					<?=__('Publish')?>
			</button>
		<? endif; ?>
	</div>

	<div id="b-page-metadata" class="ui-helper-right">
		<button id="boom-topbar-editors" class="boom-button" style="display: none" data-icon="ui-icon-person">
			<?=__('Another editor is viewing this page.')?>
		</button>

		<? if ($auth->logged_in('edit_page', $page)): ?>
			<div id="b-page-settings-menu">
				<button id="b-page-settings" class="boom-button" data-icon="ui-icon-wrench" data-icon-secondary="ui-icon-triangle-1-s">
						<?=__('Settings')?>
				</button>
			</div>
		<? endif; ?>

		<? if ($auth->logged_in('add_page', $page)): ?>
			<button id="b-page-addpage" class="boom-button" data-icon="ui-icon-circle-plus">
					<?=__('Add')?> <?=__('page')?>
			</button>
		<? endif; ?>

		<? if ($auth->logged_in('edit_page', $page)): ?>
			<div id="b-page-preview-splitbutton">
				<button id="b-page-preview" class="boom-button" data-icon="ui-icon-search"
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

	<div id="boom-topbar-pagesettings" class="ui-helper-clearfix">
		<div class="ui-helper-center">
			<?= View::factory('boom/editor/page/settings/index');?>
		</div>
	</div>

	<div id="boom-topbar-revisions" class="ui-helper-clearfix">
		This page is
		<? if ($auth->logged_in('edit', $page)): ?>
			<a href="#" id="boom-topbar-visibility">
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

	<?= View::factory('boom/breadcrumbs'); ?>
</div>
