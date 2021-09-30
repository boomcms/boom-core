<?= view('boomcms::header', ['title' => trans('boomcms::template-manager.title')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">

<table id="b-templates" class="b-table tablesorter">
        <thead>
            <tr>
                <th><a href="?sort=filename&order=asc"><?= trans('boomcms::asset-metrics.filename') ?></a></th>
                <th><a href="?sort=extension&order=asc"><?= trans('boomcms::asset-metrics.ext') ?></a></th>
                <th><a href="?sort=uploaded&order=desc"><?= trans('boomcms::asset-metrics.uploaded-on') ?></a></th>
                <th><a href="?sort=downloads&order=desc"><?= trans('boomcms::asset-metrics.downloads') ?></a></th>
            </tr>
        </thead>

        <tbody>
            <?php foreach($assets as $asset) { ?>
                <tr>
                <td><?= $asset->filename ?></td>
                <td><?= $asset->extension ?></td>
                <td><?= date('d M Y', $asset->created_at) ?></td>
                <td><?= $asset->downloads ?></td>
            </tr>
                <?php } ?>
        </tbody>

        <tfoot>
            <tr>
                <th colspan="2"></th>
                <th><?= trans('boomcms::asset-metrics.total') ?></th>
                <th><?= trans('boomcms::asset-metrics.downloads') ?></th>
            </tr>
        </tfoot>
    </table>

</main>

</div>

<?= view('boomcms::footer') ?>
