<?= View::make('boom::header', ['title' => $page->getTitle()]) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-preview b-toolbar">
	<button id="b-page-editbutton" class="b-button b-button-preview" data-preview="edit">
		<?= Lang::get('Edit page') ?>
	</button>
</div>

<?= View::make('boom::editor.footer')->render() ?>
