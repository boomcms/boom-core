<?= view('boomcms::header', ['title' => $page->getTitle()]) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-preview b-toolbar">
	<?= view('boomcms::editor.toolbar.edit-button') ?>
</div>

<?= view('boomcms::editor.toolbar.footer')->render() ?>
