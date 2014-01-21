<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-preview">
	<button id="b-page-editbutton" class="b-button b-button-preview" data-preview="edit">
		<?=__('Edit')?> <?=__('page')?>
	</button>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => FALSE)) ?>