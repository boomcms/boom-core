<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="b-topbar" class='b-page-toolbar'>
	<?= Form::hidden('csrf', Security::token(), array('id' => 'b-csrf')) ?>
	<?= Menu::factory('boom')->sort('priority') ?>

	<? if ($page->was_created_by($person) OR $auth->logged_in('edit_page_content', $page)): ?>
		<div id="b-page-actions" class="b-page-container">
			<span id="b-page-publish-menu">
				<button id="b-page-version-status" class="ui-button boom-button" data-status="<?= $page->version()->status() ?>">
					<?= __($page->version()->status()) ?>
				</button>
			</span>

			<button id="boom-page-preview" class="boom-button b-button-preview" data-icon="ui-icon-boom-preview" data-preview="preview">
				<?=__("Preview the current version of the page even if it hasn't been published")?>
			</button>

			<span id="b-page-version-menu">
				<button id="boom-page-template-settings" class="ui-button boom-button" data-icon="ui-icon-boom-options">
					<?= __('Settings for the current version of the page') ?>
				</button>
			</span>
		</div>
	<? endif; ?>

	<? if ($auth->logged_in('add_page', $page)): ?>
		<div class="b-page-container">
			<button id="b-page-addpage" class="ui-button boom-button" data-icon="ui-icon-boom-add">
				<?=__('Add a new page as a child of the current page')?>
			</button>
		</div>
	<? endif; ?>

	<div class="b-page-container">
		<? if ($auth->logged_in('edit_page', $page)): ?>
			<button id='b-page-visible' class="b-page-visibility ui-button boom-button<? if ( ! $page->is_visible()): ?> ui-helper-hidden<? endif ?>" data-icon="ui-icon-boom-visible">
				<?= __('This page is visible. The content displayed will depend on which version of the page is published') ?>
			</button>
			<button id='b-page-invisible' class="b-page-visibility ui-button boom-button<? if ($page->is_visible()): ?> ui-helper-hidden<? endif ?>" data-icon="ui-icon-boom-invisible">
				<?= __('This page is hidden regardless of whether there is a published version') ?>
			</button>
			<span id="b-page-settings-menu">
				<button id="boom-page-settings" class="ui-button boom-button" data-icon="ui-icon-boom-settings">
					<?= __('Page settings menu') ?>
				</button>
			</span>
		<? endif ?>
		<? if (($page->was_created_by($person) OR $auth->logged_in('delete_page', $page)) AND ! $page->mptt->is_root()): ?>
			<button class="ui-button boom-button" id="b-page-delete" data-icon="ui-icon-boom-delete">
				<?= __('Delete this page') ?>
			</button>
		<? endif; ?>
	</div>

	<? if ($readability): ?>
		<button id="b-page-readability" class="ui-button boom-button">
			<?= $readability ?>
		</button>
	<? endif ?>

	<div class="b-page-container">
		<?/*<button id="boom-page-editlive" class="ui-button boom-button" data-icon="ui-icon-boom-edit-live">
			<?=__('Edit live')?>
		</button>*/?>

		<button id="boom-page-viewlive" class="boom-button b-button-preview" data-icon="ui-icon-boom-view-live" data-preview="disabled">
			<?=__('View the page as it appears on the live site')?>
		</button>
	</div>

	<div id="b-topbar-pagesettings">
		<div>
			<?= View::factory('boom/editor/page/settings/index');?>
		</div>
	</div>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => TRUE)) ?>