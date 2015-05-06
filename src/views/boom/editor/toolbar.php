<?= View::factory('boom/header', array('title' => $page->getTitle())) ?>

<div id="b-topbar" class='b-page-toolbar b-toolbar b-toolbar-vertical'>
	<?= Form::hidden('csrf', Security::token(), array('id' => 'b-csrf')) ?>
	<?= new \Boom\UI\MenuButton() ?>
	<?= new \Boom\Menu\Menu  ?>

	<div id="b-topbar-page-buttons">
		<?php if ($page->wasCreatedBy($person) || $auth->loggedIn('edit_page_content', $page)): ?>
			<div id="b-page-actions" class="b-page-container">
				<span id="b-page-publish-menu">
					<button id="b-page-version-status" class="b-button" data-status="<?= $page->getCurrentVersion()->status() ?>">
						<?= $page->getCurrentVersion()->status() ?>
					</button>
				</span>

				<?= new \BoomCMS\Core\UI\Button('preview', Lang::get("Preview the current version of the page even if it hasn't been published"), array('id' => 'boom-page-preview', 'class' => 'b-button-preview','data-preview' => 'preview')) ?>
				<?= new \BoomCMS\Core\UI\Button('options', Lang::get("Changed the template used by the page"), array('id' => 'b-page-template')) ?>
			</div>
		<?php endif; ?>

		<?php if ($auth->loggedIn('add_page', $page)): ?>
			<div class="b-page-container">
				<?= new \BoomCMS\Core\UI\Button('add', Lang::get('Add a new page as a child of the current page'), array('id' => 'b-page-addpage')) ?>
			</div>
		<?php endif; ?>

		<div class="b-page-container">
			<?php if ($auth->loggedIn('edit_page', $page)): ?>
				<?= new \BoomCMS\Core\UI\Button('visible', Lang::get('This page is visible. The content displayed will depend on which version of the page is published'), array('id' => 'b-page-visible', 'class' => $page->isVisible() ? 'b-page-visibility ' : 'b-page-visibility ui-helper-hidden')) ?>
				<?= new \BoomCMS\Core\UI\Button('invisible', Lang::get('This page is hidden regardless of whether there is a published version'), array('id' => 'b-page-invisible', 'class' => $page->isVisible() ? 'b-page-visibility ui-helper-hidden' : 'b-page-visibility')) ?>

				<span id="b-page-settings-menu">
					<?= new \BoomCMS\Core\UI\Button('settings', Lang::get('Page settings which apply whichever version is published'), array('id' => 'boom-page-settings')) ?>
				</span>
			<?php endif ?>

			<?php if (($page->wasCreatedBy($person) || $auth->loggedIn('delete_page', $page)) && ! $page->getMptt()->is_root()) : ?>
				<?= new \BoomCMS\Core\UI\Button('delete', Lang::get('Delete this page'), array('id' => 'b-page-delete')) ?>
			<?php endif; ?>
		</div>

		<?php if ($readability): ?>
			<button id="b-page-readability" class="b-button">
				<?= $readability ?>
			</button>
		<?php endif ?>

		<div class="b-page-container">
			<?/*<button id="boom-page-editlive" class="ui-button boom-button" data-icon="ui-icon-boom-edit-live">
				<?=Lang::get('Edit live')?>
			</button>*/?>

			<?= new \BoomCMS\Core\UI\Button('view-live', Lang::get('View the page as it appears on the live site'), array('id' => 'boom-page-viewlive', 'class' => 'b-button-preview', 'data-preview' => 'disabled')) ?>
		</div>

		<div id="b-topbar-pagesettings">
			<div>
				<?= View::factory('boom/editor/page/settings/index');?>
			</div>
		</div>
	</div>

        <div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">
            <?php foreach (Boom\UI\TextEditorToolbar::getAvailableButtonSets() as $set): ?>

                <?= new Boom\UI\TextEditorToolbar($set) ?>
            <?php endforeach ?>
        </div>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => true)) ?>
