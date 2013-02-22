	<?= View::factory('boom/header', array('title' =>	'Assets')); ?>

	<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('boom')->sort('priority')  ?>

		<div id="boom-topbar-useractions">
			<button id="boom-page-menu" class="boom-button" data-icon="ui-icon-boom-menu">
					<?=__('Menu')?>
			</button>
			<span id="boom-page-user-menu">
				<button id="b-page-user" class="boom-button" data-icon="ui-icon-boom-person">
					<?=__('Profile')?>
				</button>
			</span>
		</div>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="b-page-actions" class="ui-helper-right">
				<span id="boom-assets-upload-menu">
					<button id="b-assets-upload" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-upload">
						<?=__('Upload files')?>
					</button>
				</span>
				<button id="boom-tagmanager-save-all" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-accept">
					<?=__('Save all')?>
				</button>
			</div>

			<div id="b-items-multiactons" class="ui-widget-content">
				<button id="b-button-multiaction-edit" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-edit">
					<?=__('View')?>/<?=__('Edit')?>
				</button>
				<button id="b-button-multiaction-delete" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-delete">
					<?=__('Delete')?>
				</button>
				<button id="b-button-multiaction-download" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-download">
					<?=__('Download')?>
				</button>
				<button id="b-button-multiaction-tag" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-tag">
					<?=__('Add Tags')?>
				</button>
				<button id="b-button-multiaction-clear" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-cancel">
					<?=__('Clear Selection')?>
				</button>
			</div>
		</div>
	</div>

	<div id="boom-dialogs">
		<div id="boom-dialog-alerts">
			<p>&nbsp;</p>
		</div>
	</div>

	<div id="boom-loader-dialog-overlay" class="ui-widget-overlay"></div>
	<div id="b-page-edit">
		<?= $content ?>
	</div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.tagmanager.js") ?>
	<?= HTML::script("media/boom/js/boom.assets.js") ?>
	<?= HTML::script("media/boom/js/boom.tags.js") ?>
	<?= HTML::script("media/boom/js/boom.links.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init('assets');
			
			$( 'body' ).browser_asset({
				allowedUploadTypes:[ '<?= implode('\', \'', Boom_Asset::$allowed_extensions)?>' ]
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
