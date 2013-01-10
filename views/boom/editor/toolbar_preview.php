<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<button id="b-page-editbutton" class="boom-button b-button-preview ui-button-text-icon ui-button" data-preview="edit">
	<?=__('Edit')?> <?=__('page')?>
</button>

<?= View::factory('boom/editor/footer', array('register_page' => FALSE)) ?>