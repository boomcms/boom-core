<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

	<?= Menu::factory('boom')->sort('priority') ?>

	<? if ($auth->logged_in('edit_page_content', $page)): ?>
		<div id="b-page-actions">
			<button id="b-page-save" class="boom-button" disabled="disabled" title="You have no unsaved changes" data-icon="ui-icon-boom-accept">
					<?=__('Accept')?>
			</button>
			<button id="b-page-save" class="boom-button" data-icon="ui-icon-boom-cancel">
					<?=__('Cancel')?>
			</button>
		</div>

		<div>
			<button class="boom-button b-button-preview" data-icon="ui-icon-boom-preview" data-preview="preview">
				<?=__('Preview')?>
			</button>

			<div id="b-page-options-splitbutton">
				<button id="boom-page-template" class="boom-button">
					<?= __('Template') ?>
				</button>
				<button id="boom-page-embargo" class="boom-button">
					<?= __('Embargo') ?>
				</button>
				<button id="boom-page-template" class="boom-button">
					<?= __('Feature image') ?>
				</button>
				<button id="boom-page-template" class="boom-button">
					<?= __('Log') ?>
				</button>
			</div>
		</div>
	<? endif; ?>

	<? if ($auth->logged_in('edit_page', $page)): ?>
		<div>
			<button id="boom-topbar-visibility" class="boom-button" data-icon="ui-icon-boom-visibility">
				<?= __('Visibility') ?>
			</button>
			<button class="boom-button" data-icon="ui-icon-boom-settings">
				<?= __('Settings') ?>
			</button>
			<button class="boom-button" data-icon="ui-icon-boom-history">
				<?= __('History') ?>
			</button>

			<? if ($auth->logged_in('delete_page', $page) AND ! $page->mptt->is_root()): ?>
				<button class="boom-button" data-icon="ui-icon-boom-delete">
					<?= __('Delete') ?>
				</button>
			<? endif; ?>
		</div>


		<? if ($auth->logged_in('add_page', $page)): ?>
			<button id="b-page-delete" class="boom-button" data-icon="ui-icon-boom-add">
				<?=__('Add')?>
			</button>
		<? endif; ?>

		<button id="boom-topbar-editlive" class="boom-button">
			<?=__('Edit live')?>
		</button>

		<button id="boom-topbar-viewlive" class="boom-button b-button-preview" data-preview="disabled">
			<?=__('View live')?>
		</button>
	<? endif; ?>

	<?= View::factory('boom/breadcrumbs'); ?>
</div>