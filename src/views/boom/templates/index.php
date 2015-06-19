	<?= View::make('boom::header', ['title' => 'Templates']) ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menuButton() ?>
		<?= $button('accept', Lang::get('Save all'), ['id' => 'b-templates-save', 'class' => 'b-button-withtext']) ?>
	</div>

	<div id="b-templates">
		<form id="b-items-view-list">
			<table id="b-templates-table" class="tablesorter">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Description</th>
						<th>Filename</th>
						<th>Pages</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($templates as $t): ?>
						<?php $class = "" ?>
						<?php if ( ! $t->fileExists()): ?>
							<?php $class = ' b-templates-nofile' ?>
						<?php endif ?>

						<tr class="<?= $class ?>" data-id="<?= $t->getId() ?>">
							<td><input type='hidden' name='templates[]' value='<?= $t->getId() ?>' /></td>
							<td><input type='text' name='name-<?= $t->getId() ?>' value="<?= $t->getName() ?>" /></td>
							<td><input type='text' name='description-<?= $t->getId() ?>' value="<?= $t->getDescription() ?>" /></td>
							<td><input type="text" name="filename-<?= $t->getId() ?>" value="<?= $t->getFilename() ?>" /></td>
							<td>
								<?php $page_count = 'TODO' ?>
								<a href='/cms/templates/pages/<?= $t->getId() ?>' title='View the title and URL of <?= $page_count, " ", Lang::get('page', [$page_count]) ?> which use this template'><?= $page_count ?>
							</td>
							<td><?= $button('delete', "Delete the &quot;{$t->getName()}&quot; template", ['class' => 'b-templates-delete']) ?>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</form>
	</div>

    <?= $boomJS ?>
	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init();
			$('body').templateManager();
		})(jQuery);
		//]]>
	</script>
</body>
</html>
