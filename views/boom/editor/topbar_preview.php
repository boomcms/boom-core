<div id="sledge-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all"> 
	
	<?= Menu::factory('sledge') ?>

	<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">

		<div id="s-page-actions">
			
			<div class="sledge-buttonset">
				<button id="s-page-preview-published" class="sledge-button ui-button-text-icon ui-button">
					<?=__('Published changes')?>
				</button>
				<button id="s-page-preview-all" class="sledge-button ui-button-text-icon ui-button">
					<?=__('All changes')?>
				</button>
			</div>
			
		</div>


		<div class="ui-helper-right">
			<button id="s-page-editbutton" class="sledge-button ui-button-text-icon ui-button">
				<?=__('Edit')?> <?=__('page')?>
			</button>
		</div>
	</div>
	
</div>
