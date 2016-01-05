	<?= view('boomcms::header', ['title' => 'Templates']) ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menuButton() ?>
		<?= $button('check', trans('Save all'), ['id' => 'b-templates-save', 'class' => 'b-button-withtext']) ?>
	</div>

	<form id="b-templates">
        <h1><?= trans('boomcms::templates.heading') ?></h1>

        <table id="b-templates-table" class="b-table tablesorter">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Theme</th>
                    <th>Filename</th>
                    <th>Pages</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($templates as $t): ?>
                    <?php $class = '' ?>
                    <?php if (!$t->fileExists()): ?>
                        <?php $class = ' b-templates-nofile' ?>
                    <?php endif ?>

                    <tr class="<?= $class ?>" data-id="<?= $t->getId() ?>">
                        <td><input type='hidden' name='templates[]' value='<?= $t->getId() ?>' /></td>
                        <td><input type='text' name='name-<?= $t->getId() ?>' value="<?= $t->getName() ?>" /></td>
                        <td><input type='text' name='description-<?= $t->getId() ?>' value="<?= $t->getDescription() ?>" /></td>
                        <td><?= $t->getTheme() ?></td>
                        <td><input type="text" name="filename-<?= $t->getId() ?>" value="<?= $t->getFilename() ?>" /></td>
                        <td>
                            <?php $pageCount = $countPages(['template' => $t]) ?>
                            <a href='/boomcms/templates/pages/<?= $t->getId() ?>' title='View the title and URL of <?= $pageCount, ' ', trans('page', [$pageCount]) ?> which use this template'><?= $pageCount ?>
                        </td>
                        <td><?= $button('trash', "Delete the &quot;{$t->getName()}&quot; template", ['class' => 'b-templates-delete']) ?>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </form>

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
