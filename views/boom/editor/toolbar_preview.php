<?= View::factory('boom/header', array('title' => $page->version()->title)) ?>

<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

	<?= Menu::factory('boom') ?>

	<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">

		<div id="b-page-actions">

			<div class="boom-buttonset">
				<button id="b-page-preview-published" class="boom-button ui-button-text-icon ui-button">
					<?=__('Published changes')?>
				</button>
				<button id="b-page-preview-all" class="boom-button ui-button-text-icon ui-button">
					<?=__('All changes')?>
				</button>
			</div>

		</div>


		<div class="ui-helper-right">
			<button id="b-page-editbutton" class="boom-button b-button-preview ui-button-text-icon ui-button" data-preview="edit">
				<?=__('Edit')?> <?=__('page')?>
			</button>
		</div>
	</div>

</div>

<?= View::factory('boom/editor/footer', array('register_page' => FALSE)) ?>