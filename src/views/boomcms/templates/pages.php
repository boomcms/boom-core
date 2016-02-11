<?= view('boomcms::header', ['title' => 'Templates']) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
    <?= $button('download', trans('Download as CSV'), ['id' => 'b-template-pages-download', 'class' => 'b-button-withtext']) ?>
</div>

<main id="b-container">
    <div id="b-templates">
        <h1><?= trans('boomcms::templates.pages') ?> <?= $template->getName() ?></h1>

        <table class="b-table tablesorter">
			<thead>
				<tr>
					<th>Page title</th>
					<th>URL</th>
					<th>Visible?</th>
					<th>Last Edited</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach ($pages as $p): ?>
					<tr>
						<td><?= $p->getTitle() ?></td>
						<td><a href='<?= $p->url() ?>'><?= $p->url()->getLocation() ?></a></td>
						<td><?= $p->isVisible() ? 'Yes' : 'No' ?></td>
						<td><?= $p->getLastModified()->format('Y-m-d H:i:s') ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
        </table>
    </div>
</main>

<script type="text/javascript">
    window.onload = function() {
        $('body').templateManager();
    };
</script>

<?= view('boomcms::footer') ?>
