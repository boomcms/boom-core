	<?= View::make('boom::header', ['title' => 'People']) ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menu() ?>

		<?= $menuButton() ?>
		<?= $button('plus', Lang::get('New person'), ['id' => 'b-people-create']) ?>
		<?= $button('trash-o', Lang::get('Delete'), ['id' => 'b-people-multi-delete', 'disabled' => 'disabled']) ?>

		<button id="b-people-all" class="b-button">
			<?= Lang::get('All people') ?>
		</button>
	</div>

	<div id="b-people-manager">
		<div id="b-groups">
			<div id="b-groups-header">
				<?= $button('plus', Lang::get('Add group'), ['class' => 'b-people-group-add']) ?>
				<h2>
					<?= Lang::get('Groups') ?>
				</h2>
			</div>

			<ul id="b-groups-list">
				<?php foreach ($groups as $group): ?>
					<li data-group-id="<?= $group->getId() ?>"<?php if ($group->getId() == $request->input('group')): ?> class='current'<?php endif ?>>
						<a class='b-groups-item' href='/cms/people?group=<?= $group->getId() ?>'><?= $group->getName() ?></a>

						<a href='#' title="Delete" class="fa fa-trash-o b-group-delete"></a>
						<a href='<?= route('group-edit', ['id' => $group->getId()]) ?>' title="Edit" class="fa fa-edit"></a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>

		<div id='b-people-content'>
			<?= $content ?>
		</div>
	</div>

	<?= $boomJS ?>
	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();
			$(document.body).peopleManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
