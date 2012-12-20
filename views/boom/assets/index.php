<?php
/**
* This is the main template for the asset manager.
* This template should not be included directly.
*
* Rendered by: Boom_Controller_Cms_Assets::action_index()
*/
?>
	<?= View::factory('boom/header',
		array(
			'title' =>	'Assets',
		));
	?>

	<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('boom') ?>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="s-page-actions" class="ui-helper-right">
				<button id="s-assets-upload" class="boom-button ui-button-text-icon" data-icon="ui-icon-disk">
					<?=__('Upload files')?>
				</button>
				<button id="boom-tagmanager-save-all" class="boom-button ui-button-text-icon" data-icon="ui-icon-disk">
					<?=__('Save all')?>
				</button>

				<button class="boom-button" data-icon="ui-icon-video" id="s-assets-upload-video">Upload video</button>
			</div>
		</div>
	</div>

	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="s-page-edit">
		<?= $content ?>
	</div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.tagmanager.js") ?>
	<?= HTML::script("media/boom/js/boom.items.js") ?>
	<?= HTML::script("media/boom/js/boom.assets.js") ?>
	<?= HTML::script("media/boom/js/boom.links.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init('assets', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.boom.assets.init({
				items: {
					asset: $.boom.items.asset,
					tag: $.boom.items.tag
				},
				options: {
					sortby: 'last_modified',
					order: 'desc',
					basetagRid: 1,
					defaultTagRid: 0,
					edition: 'cms',
					type: 'assets',
					allowedUploadTypes:[ '<?= implode('\', \'', Boom_Asset::$allowed_extensions)?>' ],
					treeConfig : {
						showEdit: true,
						showRemove: true,
						onEditClick: function(event){

							$.boom.items.group.edit(event);
						},
						onRemoveClick: function(event){

							$.boom.items.group.remove(event);
						}
					}
				}
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
