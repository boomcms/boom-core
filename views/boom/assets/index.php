<?php
/**
* This is the main template for the asset manager.
* This template should not be included directly.
*
* Rendered by: Sledge_Controller_Cms_Assets::action_index()
*/
?>
	<?= View::factory('sledge/header',
		array(
			'title' =>	'Assets',
		));
	?>

	<div id="sledge-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('sledge') ?>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="s-page-actions" class="ui-helper-right">
				<button id="s-assets-upload" class="sledge-button ui-button-text-icon" data-icon="ui-icon-disk">
					<?=__('Upload files')?>
				</button>
				<button id="sledge-tagmanager-save-all" class="sledge-button ui-button-text-icon" data-icon="ui-icon-disk">
					<?=__('Save all')?>
				</button>

				<button class="sledge-button" data-icon="ui-icon-video" id="s-assets-upload-video">Upload video</button>
			</div>
		</div>
	</div>

	<div id="sledge-dialogs">
		<div id="sledge-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="sledge-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-page-edit">
		<?= $content ?>
	</div>

	<?= HTML::script("media/sledge/js/sledge.helpers.js") ?>
	<?= HTML::script("media/sledge/js/jquery.js") ?>
	<?= HTML::script("media/sledge/js/sledge.jquery.ui.js") ?>
	<?= HTML::script("media/sledge/js/sledge.plugins.js") ?>
	<?= HTML::script("media/sledge/js/sledge.config.js") ?>
	<?= HTML::script("media/sledge/js/sledge.core.js") ?>
	<?= HTML::script("media/sledge/js/sledge.tagmanager.js") ?>
	<?= HTML::script("media/sledge/js/sledge.items.js") ?>
	<?= HTML::script("media/sledge/js/sledge.assets.js") ?>
	<?= HTML::script("media/sledge/js/sledge.links.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.sledge.init('assets', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.sledge.assets.init({
				items: {
					asset: $.sledge.items.asset,
					tag: $.sledge.items.tag
				},
				options: {
					sortby: 'last_modified',
					order: 'desc',
					basetagRid: 1,
					defaultTagRid: 0,
					edition: 'cms',
					type: 'assets',
					allowedUploadTypes:[ '<?= implode('\', \'', Sledge_Asset::$allowed_extensions)?>' ],
					treeConfig : {
						showEdit: true,
						showRemove: true,
						onEditClick: function(event){

							$.sledge.items.group.edit(event);
						},
						onRemoveClick: function(event){

							$.sledge.items.group.remove(event);
						}
					}
				}
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
