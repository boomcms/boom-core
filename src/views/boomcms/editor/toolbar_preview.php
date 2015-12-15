<?= view('boomcms::header', ['title' => $page->getTitle()]) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-preview b-toolbar">
	<button id="b-page-editbutton" class="b-button b-button-preview" data-preview="edit">
		<?= trans('Edit page') ?>
	</button>
</div>

<?= view('boomcms::editor.footer')->render() ?>
