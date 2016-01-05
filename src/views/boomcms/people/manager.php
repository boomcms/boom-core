	<?= view('boomcms::header', ['title' => 'People']) ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menu() ?>

		<?= $menuButton() ?>
		<?= $button('plus', trans('New person'), ['id' => 'b-people-create']) ?>
		<?= $button('trash-o', trans('Delete'), ['id' => 'b-people-multi-delete', 'disabled' => 'disabled']) ?>

		<button id="b-people-all" class="b-button">
			<?= trans('All people') ?>
		</button>
	</div>

	<div id="b-people-manager">
        <h1><?= trans('boomcms::people.heading') ?></h1>

		<div id="b-groups">
			<div id="b-groups-header">
				<?= $button('plus', trans('Add group'), ['class' => 'b-people-group-add']) ?>
				<h2>
					<?= trans('Groups') ?>
				</h2>
			</div>

			<ul id="b-groups-list">
				<?php foreach ($groups as $group): ?>
					<li data-group-id="<?= $group->getId() ?>"<?php if ($group->getId() == $request->input('group')): ?> class='current'<?php endif ?>>
						<a class='b-groups-item' href='/boomcms/people?group=<?= $group->getId() ?>'><?= $group->getName() ?></a>

						<a href='<?= route('group-edit', ['id' => $group->getId()]) ?>' title="Edit" class="fa fa-edit"></a>
						<a href='#' title="Delete" class="fa fa-trash-o b-group-delete"></a>
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
