	<?= view('boomcms::header', ['title' => trans('boomcms::sites.heading')]) ?>
	<?= $menu() ?>

	<div id="b-topbar" class="b-toolbar">
		<?= $menuButton() ?>

        <?= $button('add', 'add-site', ['id' => 'b-sites-add']) ?>
	</div>

    <main id="b-container">
        <div id="b-sites">
            <h1><?= trans('boomcms::sites.heading') ?></h1>

            <table id="b-sites-table" class="b-table tablesorter">
                <thead>
                    <tr>
                        <th><?= trans('boomcms::sites.name') ?></th>
                        <th><?= trans('boomcms::sites.hostname') ?></th>
                        <th><?= trans('boomcms::sites.default.heading') ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($sites as $site): ?>
                        <tr data-id="<?= $site->getId() ?>">
                            <td><?= $site->getName() ?></td>
                            <td><?= $site->getHostname() ?></td>
                            <td><?= trans('boomcms::sites.default.' .(int) $site->isDefault()) ?></td>

                            <td class="buttons">
                                <?= $button('edit', 'edit', ['class' => 'b-sites-edit']) ?>
                                <?= $button('trash-o', 'delete', ['class' => 'b-sites-delete']) ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </main>

	<?= $boomJS ?>
	<script type="text/javascript">
		//<![CDATA[
		window.onload = function () {
			$.boom.init();

			$('body').siteManager();
		};
		//]]>
	</script>
</body>
</html>
