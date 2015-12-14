<?= view('boomcms::header', ['title' => $page->getTitle()]) ?>

<div id="b-topbar" class='b-page-toolbar b-toolbar b-toolbar-vertical'>
	<?= $menuButton() ?>
	<?= $menu() ?>

	<div id="b-topbar-page-buttons">
		<?php if ($auth->loggedIn('add_page', $page)): ?>
			<?= $button('plus', trans('Add a new page as a child of the current page'), ['id' => 'b-page-addpage']) ?>
		<?php endif ?>

		<?php if ($auth->loggedIn('edit_page', $page)): ?>
			<div id="b-page-settings-menu">
				<?= $button('cog', trans('Page settings which apply whichever version is published'), ['id' => 'b-page-settings']) ?>
			</div>
		<?php endif ?>

		<?php if ($page->wasCreatedBy($person) || $auth->loggedIn('edit_page_content', $page)): ?>
			<div id="b-page-publish-menu">
				<button id="b-page-version-status" class="b-button" data-status="<?= $page->getCurrentVersion()->status() ?>">
					<?= $page->getCurrentVersion()->status() ?>
				</button>
			</div>
		<?php endif ?>

		<?php if ($auth->loggedIn('edit_page', $page)): ?>
			<?= $button('eye', trans('This page is visible. The content displayed will depend on which version of the page is published'), ['id' => 'b-page-visible', 'class' => $page->isVisible() ? 'b-page-visibility ' : 'b-page-visibility ui-helper-hidden']) ?>
			<?= $button('eye-slash', trans('This page is hidden regardless of whether there is a published version'), ['id' => 'b-page-invisible', 'class' => $page->isVisible() ? 'b-page-visibility ui-helper-hidden' : 'b-page-visibility']) ?>
		<?php endif ?>

		<?php if ($auth->canDelete($page)) : ?>
            <?php if ($page->canBeDeleted()): ?>
    			<?= $button('trash-o', trans('Delete this page'), ['id' => 'b-page-delete']) ?>
            <?php else: ?>
                <?= $button('trash-o', trans('Page delete has been disabled for this page'), ['id' => 'b-page-delete', 'disabled' => 'disabled']) ?>
            <?php endif ?>
		<?php endif ?>

		<div class="b-page-container">
			<?= $button('view-live', trans('View the page as it appears on the live site'), ['id' => 'b-page-viewlive', 'class' => 'b-button-preview', 'data-preview' => 'disabled']) ?>
		</div>
	</div>

    <div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">
        <?php foreach (BoomCMS\UI\TextEditorToolbar::getAvailableButtonSets() as $set): ?>
            <?= new BoomCMS\UI\TextEditorToolbar($set) ?>
        <?php endforeach ?>
    </div>
</div>

<div id="b-page-settings-toolbar">
    <?= view('boomcms::editor.page.settings.index') ?>
</div>

<?= view('boomcms::editor.footer') ?>
