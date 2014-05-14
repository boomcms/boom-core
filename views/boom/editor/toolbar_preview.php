<?= View::factory('boom/header', array('title' => $page->getTitle())) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-preview b-toolbar">
	<button id="b-page-editbutton" class="b-button b-button-preview" data-preview="edit">
		<?=__('Edit')?> <?=__('page')?>
	</button>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => false)) ?>