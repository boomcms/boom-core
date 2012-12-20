<?php
/**
* The CMS asset manager.
* Viewed by accessing /cms/assets/manager
* This can be displayed as part of the main asset manager or for selecting an asset in the editor.
*
* Rendered by:	Controller_Cms_Assets::action_manager()
*
*/
?>
<div id="sledge-tagmanager">
	<div class="s-items-main ui-helper-right">
		<div class="s-items-body ui-helper-clearfix">
			<div class="s-items-rightpane">
				<div class="s-items-content">
					&nbsp;
				</div>
			</div>
		</div>
	</div>

	<div class="s-items-sidebar ui-helper-left">		
		<?= $filters ?>
		
		<div class="s-items-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
			<a href="#" class="s-tags-add ui-helper-right">
				<span class="ui-icon ui-icon-wrench ui-helper-left"></span>
				<?=__('Add')?>
			</a>
			<h3 class="ui-helper-reset">
				<span class="ui-icon ui-icon-carat-1-e ui-helper-left"></span>
				<?=__('Tags')?>
			</h3>	
		</div>
		<div id="sledge-tag-tree">
			<?= $tags ?>
		</div>
	</div>
</div>
