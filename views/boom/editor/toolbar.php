<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="b-topbar" class='b-page-toolbar'>
	<?= Form::hidden('csrf', Security::token(), array('id' => 'b-csrf')) ?>
	<?= Menu::factory('boom')->sort('priority') ?>

	<? if ($page->was_created_by($person) OR $auth->logged_in('edit_page_content', $page)): ?>
		<div id="b-page-actions" class="b-page-container">
			<span id="b-page-publish-menu">
				<button id="b-page-version-status" class="b-button" data-status="<?= $page->version()->status() ?>">
					<?= $page->version()->status() ?>
				</button>
			</span>

			<?= BoomUI::button('preview', __("Preview the current version of the page even if it hasn't been published"), array('id' => 'boom-page-preview', 'data-preview' => 'preview')) ?>

			<span id="b-page-version-menu">
				<?= BoomUI::button('options', __('Settings for the current version of the page'), array('id' => 'boom-page-template-settings')) ?>
			</span>
		</div>
	<? endif; ?>

	<? if ($auth->logged_in('add_page', $page)): ?>
		<div class="b-page-container">
			<?= BoomUI::button('add', __('Add a new page as a child of the current page'), array('id' => 'b-page-addpage')) ?>
		</div>
	<? endif; ?>

	<div class="b-page-container">
		<? if ($auth->logged_in('edit_page', $page)): ?>
			<?= BoomUI::button('visible', __('This page is visible. The content displayed will depend on which version of the page is published'), array('id' => 'b-page-visible', 'class' => $page->is_visible()? 'b-page-visibility ' : 'b-page-visibility ui-helper-hidden')) ?>
			<?= BoomUI::button('invisible', __('This page is hidden regardless of whether there is a published version'), array('id' => 'b-page-invisible', 'class' => $page->is_visible()? 'b-page-visibility ui-helper-hidden' : 'b-page-visibility')) ?>

			<span id="b-page-settings-menu">
				<?= BoomUI::button('settings', __('Page settings which apply whichever version is published'), array('id' => 'boom-page-settings')) ?>
			</span>
		<? endif ?>

		<? if (($page->was_created_by($person) OR $auth->logged_in('delete_page', $page)) AND ! $page->mptt->is_root()): ?>
			<?= BoomUI::button('delete', __('Delete this page'), array('id' => 'b-page-delete')) ?>
		<? endif; ?>
	</div>

	<? if ($readability): ?>
		<button id="b-page-readability" class="b-button">
			<?= $readability ?>
		</button>
	<? endif ?>

	<div class="b-page-container">
		<?/*<button id="boom-page-editlive" class="ui-button boom-button" data-icon="ui-icon-boom-edit-live">
			<?=__('Edit live')?>
		</button>*/?>

		<?= BoomUI::button('view-live', __('View the page as it appears on the live site'), array('id' => 'boom-page-viewlive')) ?>
	</div>

	<div id="b-topbar-pagesettings">
		<div>
			<?= View::factory('boom/editor/page/settings/index');?>
		</div>
	</div>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => TRUE)) ?>