	<?= View::factory('boom/header')->set('title', 'People') ?>

	<div id="b-topbar">
		<?= Menu::factory('boom')->sort('priority') ?>

		<?= BoomUI::button('add', __('New person'), array('id' => 'b-people-create')) ?>
		<?= BoomUI::button('delete', __('Delete'), array('id' => 'b-button-multiaction-delete', 'disabled' => 'disabled')) ?>
	</div>

	<div id="b-people-manager">
		<div id="b-groups">
			<div id="b-groups-header">
				<a href="#" class="b-people-group-add">
					<?= __('Add') ?>
				</a>
				<h3>
					<?= __('Groups') ?>
				</h3>
			</div>

			<ul id="b-groups-list">
				<? foreach ($groups as $group): ?>
					<li id='t<?= $group->id ?>'>
						<a class='b-groups-item' rel='<?= $group->id ?>' id='tag_<?= $group->id ?>' href='/cms/people?group=<?= $group->id ?>'><?= $group->name ?></a>

						<a href='<?= Route::url('people-edit', array('controller' => 'groups', 'action' => 'delete', 'id' => $group->id)) ?>' title="Delete" class="ui-icon ui-icon-close"></a>
						<a href='<?= Route::url('people-edit', array('controller' => 'groups', 'action' => 'edit', 'id' => $group->id)) ?>' title="Edit" class="ui-icon ui-icon-wrench"></a>
					</li>
				<? endforeach ?>
			</ul>
		</div>

		<div id='b-people-content'>
			<?= $content ?>
		</div>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function($){
			$.boom.init();
			$('body').peopleManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>