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
					New person
				</button>
			</div>

			<div id="b-items-multiactons" class="ui-widget-content">
				<button id="b-button-multiaction-edit" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-edit">
					View/Edit
				</button>
				<button id="b-button-multiaction-delete" disabled="disabled" class="boom-button ui-button-text-icon" data-icon="ui-icon-boom-delete">
					Delete
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
						<span class="ui-icon ui-icon-wrench ui-helper-left"></span>
						Add
					</a>
					<h3 class="ui-helper-reset">
						<span class="ui-icon ui-icon-carat-1-e ui-helper-left"></span>
						Groups
					</h3>
				</div>
				<ul class="boom-tree b-tags-tree  boom-tree-noborder">
				<?
					foreach ($groups as $id => $name):
						echo "<li id='t", $id, "'><a rel='", $id, "' id='tag_" , $id , "' class='' href='#tag/", $id;
						echo "'>" , $name , "</a>\n";
					endforeach;
				?>
				</ul>
			</div>
		</div>
	</div>

	<?= HTML::script("media/boom/js/boom.helpers.js") ?>
	<?= HTML::script("media/boom/js/jquery.js") ?>
	<?= HTML::script("media/boom/js/boom.jquery.ui.js") ?>
	<?= HTML::script("media/boom/js/boom.plugins.js") ?>
	<?= HTML::script("media/boom/js/boom.config.js") ?>
	<?= HTML::script("media/boom/js/boom.core.js") ?>
	<?= HTML::script("media/boom/js/boom.tagmanager.js") ?>
	<?= HTML::script("media/boom/js/boom.items.js") ?>
	<?= HTML::script("media/boom/js/boom.people.js") ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init('people', {
				person: {
					rid: <?= $person->id?>,
					name: "<?= $person->name?>"
				}
			});

			$.boom.people.init({
				items: {
					tag: $.boom.items.group,
					person: $.boom.items.person
				},
				options: {
					sortby: 'name',
					order: 'asc',
					basetagRid: 1,
					defaultTagRid: 0,
					edition: 'cms',
					type: 'people',
					treeConfig : {
						showEdit: true,
						showRemove: true
					}
				}
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>