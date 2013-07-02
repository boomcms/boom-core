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

	<div id="b-page-edit">
		<?= $content ?>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init(null, {
				csrf: '<?= Security::token() ?>'
			});

			$( 'body' ).browser_asset({
				allowedUploadTypes:[ '<?= implode('\', \'', Boom_Asset::$allowed_extensions)?>' ]
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
