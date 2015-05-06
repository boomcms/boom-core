	<?= View::factory('boom/header')->set('title', 'People') ?>

	<div id="b-topbar" class="b-toolbar">
		<?= new \Boom\Menu\Menu  ?>

		<?= new \Boom\UI\MenuButton() ?>
		<?= new \BoomCMS\Core\UI\Button('add', Lang::get('New person'), array('id' => 'b-people-create')) ?>
		<?= new \BoomCMS\Core\UI\Button('delete', Lang::get('Delete'), array('id' => 'b-people-multi-delete', 'disabled' => 'disabled')) ?>

		<button id="b-people-all" class="b-button">
			<?=Lang::get('All people')?>
		</button>
	</div>

	<div id="b-people-manager">
		<div id="b-groups">
			<div id="b-groups-header">
				<?= new \BoomCMS\Core\UI\Button('add', Lang::get('Add group'), array('class' => 'b-people-group-add')) ?>
				<h2>
					<?= Lang::get('Groups') ?>
				</h2>
			</div>

			<ul id="b-groups-list">
				<?php foreach ($groups as $group): ?>
					<li data-group-id="<?= $group->getId() ?>"<?php if ($group->getId() == Request::current()->query('group')): ?> class='current'<?php endif ?>>
						<a class='b-groups-item' href='/cms/people?group=<?= $group->getId() ?>'><?= $group->getName() ?></a>

						<a href='#' title="Delete" class="ui-icon ui-icon-close b-group-delete"></a>
						<a href='<?= Route::url('people-edit', array('controller' => 'group', 'action' => 'edit', 'id' => $group->getId())) ?>' title="Edit" class="ui-icon ui-icon-wrench"></a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>

		<div id='b-people-content'>
			<?= $content ?>
		</div>
	</div>

	<?= Boom::include_js() ?>

	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init({csrf : '<?= Security::token() ?>'});
			$('body').peopleManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
