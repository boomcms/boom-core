<?= view('boomcms::header', ['title' => trans('boomcms::template-manager.title')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">

<h2><?= $album->getName() ?></h2>

<table id="album-asset-list" class="display b-table">
        <thead>
            <tr>
                <th width="50%">Filename</th>
                <th width="10%">Type</th>
                <th width="15%">Size (bytes)</th>
                <th width="15%">No of view(s)</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($assets as $asset) { ?>
            <tr>
                <td><a href="/boomcms/asset-manager/albums/<?= $album->getSlug() ?>/asset/<?= $asset->getId() ?>/info"><?= $asset->getTitle() ?></a></td>
                <td><?= $asset->getType() ?></td>
                <td><?= $asset->getFilesize() ?></td>
                <td><?= $asset->getNoOfUsage() ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</main>

<script defer type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
    $('#album-asset-list').DataTable();
} );
</script>

<?= view('boomcms::footer') ?>
