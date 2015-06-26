<?= View::make('boom::header', ['title' => $page->getTitle()]) ?>

<div id="b-topbar" class='b-page-toolbar b-toolbar b-toolbar-vertical'>
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar-page-buttons">
		<?php if ($page->wasCreatedBy($person) || $auth->loggedIn('edit_page_content', $page)): ?>
			<div id="b-page-actions" class="b-page-container">
				<span id="b-page-publish-menu">
					<button id="b-page-version-status" class="b-button" data-status="<?= $page->getCurrentVersion()->status() ?>">
						<?= $page->getCurrentVersion()->status() ?>
					</button>
				</span>

				<?= $button('preview', Lang::get("Preview the current version of the page even if it hasn't been published"), ['id' => 'boom-page-preview', 'class' => 'b-button-preview','data-preview' => 'preview']) ?>
				<?= $button('options', Lang::get("Changed the template used by the page"), ['id' => 'b-page-template']) ?>
			</div>
		<?php endif ?>

		<?php if ($auth->loggedIn('add_page', $page)): ?>
			<div class="b-page-container">
				<?= $button('add', Lang::get('Add a new page as a child of the current page'), ['id' => 'b-page-addpage']) ?>
			</div>
		<?php endif ?>

		<div class="b-page-container">
			<?php if ($auth->loggedIn('edit_page', $page)): ?>
				<?= $button('visible', Lang::get('This page is visible. The content displayed will depend on which version of the page is published'), ['id' => 'b-page-visible', 'class' => $page->isVisible() ? 'b-page-visibility ' : 'b-page-visibility ui-helper-hidden']) ?>
				<?= $button('invisible', Lang::get('This page is hidden regardless of whether there is a published version'), ['id' => 'b-page-invisible', 'class' => $page->isVisible() ? 'b-page-visibility ui-helper-hidden' : 'b-page-visibility']) ?>

				<span id="b-page-settings-menu">
					<?= $button('settings', Lang::get('Page settings which apply whichever version is published'), ['id' => 'boom-page-settings']) ?>
				</span>
			<?php endif ?>

			<?php if (($page->wasCreatedBy($person) || $auth->loggedIn('delete_page', $page)) && ! $page->isRoot()) : ?>
				<?= $button('delete', Lang::get('Delete this page'), ['id' => 'b-page-delete']) ?>
			<?php endif ?>
		</div>

		<div class="b-page-container">
			<?= $button('view-live', Lang::get('View the page as it appears on the live site'), ['id' => 'boom-page-viewlive', 'class' => 'b-button-preview', 'data-preview' => 'disabled']) ?>
		</div>

		<div id="b-topbar-pagesettings">
			<div>
				<?= View::make('boom::editor.page.settings.index') ?>
			</div>
		</div>
	</div>

        <div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">
            <?php foreach (BoomCMS\Core\UI\TextEditorToolbar::getAvailableButtonSets() as $set): ?>

                <?= new BoomCMS\Core\UI\TextEditorToolbar($set) ?>
            <?php endforeach ?>
        </div>
</div>

<?= View::make('boom::editor.linkPicker') ?>
<?= View::make('boom::editor.footer') ?>
