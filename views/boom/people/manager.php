	<?= View::factory('boom/header')->set('title', 'People') ?>

	<div id="b-topbar" class="b-toolbar">
		<?= \Boom\Menu\Menu::factory('boom')->sort('priority') ?>

		<?= Boom\UI::menuButton() ?>
		<?= \Boom\UI::button('add', __('New person'), array('id' => 'b-people-create')) ?>
		<?= \Boom\UI::button('delete', __('Delete'), array('id' => 'b-people-multi-delete', 'disabled' => 'disabled')) ?>

		<button id="b-people-all" class="b-button">
			<?=__('All people')?>
		</button>
	</div>

	<div id="b-people-manager">
		<div id="b-groups">
			<div id="b-groups-header">
				<?= \Boom\UI::button('add', __('Add group'), array('class' => 'b-people-group-add')) ?>
				<h2>
					<?= __('Groups') ?>
				</h2>
			</div>

			<ul id="b-groups-list">
				<? foreach ($groups as $group): ?>
					<li data-group-id="<?= $group->getId() ?>"<? if ($group->getId() == Request::current()->query('group')): ?> class='current'<? endif ?>>
						<a class='b-groups-item' href='/cms/people?group=<?= $group->getId() ?>'><?= $group->getName() ?></a>

						<a href='#' title="Delete" class="ui-icon ui-icon-close b-group-delete"></a>
						<a href='<?= Route::url('people-edit', array('controller' => 'group', 'action' => 'edit', 'id' => $group->getId())) ?>' title="Edit" class="ui-icon ui-icon-wrench"></a>
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
			$.boom.init({csrf : '<?= Security::token() ?>'});
			$('body').peopleManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>