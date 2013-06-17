	<?= View::factory('boom/header')->set('title', 'People') ?>

	<div id="boom-topbar" class="ui-helper-clearfix ui-tabs ui-widget ui-widget-content ui-corner-all">

		<?= Menu::factory('boom')->sort('priority') ?>

		<div id="boom-topbar-useractions">
			<span id="boom-page-user-menu">
				<button id="b-page-user" class="boom-button" data-icon="ui-icon-boom-person">
					<?=__('Profile')?>
				</button>
			</span>
		</div>

		<div class="ui-helper-clearfix ui-tabs-panel ui-widget-content ui-corner-bottom">
			<div id="b-page-actions" class="ui-helper-right">
				<button id="boom-page-menu" class="boom-button" data-icon="ui-icon-boom-menu">
						<?=__('Menu')?>
				</button>
				<button id="boom-tagmanager-create-person" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-add">
					<?= __('New person') ?>
				</button>
			</div>

			<div id="b-items-multiactons" class="ui-widget-content">
				<button id="b-button-multiaction-edit" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-edit">
					<?= __('View') ?>/<?= __('Edit') ?>
				</button>
				<button id="b-button-multiaction-delete" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-delete">
					<?= __('Delete') ?>
				</button>
			</div>
		</div>

	</div>

	<div id="b-page-edit">
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
				<div class="b-items-box-header ui-helper-reset ui-widget-header ui-panel-header ui-corner-all">
					<a href="#" class="b-people-group-add ui-helper-right">
						<?= __('Add') ?>
					</a>
					<h3 class="ui-helper-reset">
						<?= __('Groups') ?>
					</h3>
				</div>
				<div class="boom-box ui-widget ui-corner-all ui-state-default">
					<ul class="boom-tree b-tags-tree  boom-tree-noborder">
					<?
						foreach ($groups as $id => $name):
							echo "<li id='t", $id, "'><a rel='", $id, "' id='tag_" , $id , "' class='' href='#group/", $id;
							echo "'>" , $name , "</a>\n";
						endforeach;
					?>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init('people');
			$( 'body' ).browser_people();
		})(jQuery);
		//]]>
	</script>
</body>
</html>