<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-preview">
	<button id="b-page-editbutton" class="boom-button b-button-preview ui-button-text-icon ui-button" data-preview="edit">
		<?=__('Edit')?> <?=__('page')?>
	</button>
</div>

<?= View::factory('boom/editor/footer', array('register_page' => FALSE)) ?>