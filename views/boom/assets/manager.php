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
<div id="boom-tagmanager">
	<div class="b-items-main ui-helper-right">
		<div class="b-items-body ui-helper-clearfix">
			<div class="b-items-rightpane">
				<div class="b-items-content">
					&nbsp;
				</div>
			</div>
		</div>
	</div>

	<div class="b-items-sidebar ui-helper-left">
		<?= $filters ?>

		<div class="b-items-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
			<h3 class="ui-helper-reset">
				<?=__('Tags')?>
			</h3>
		</div>
		<div class="boom-box ui-widget ui-corner-all ui-state-default">
			<input type='text' class="b-filter-input" id="b-assets-filter-tag" placeholder="Type a tag name" />
		</div>
		
		<?/*<div id="boom-tag-tree" class="ui-state-default">
			<?= $tags ?>
		</div>*/?>
	</div>
</div>
