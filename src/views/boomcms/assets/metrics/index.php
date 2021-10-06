<?= view('boomcms::header', ['title' => trans('boomcms::template-manager.title')]) ?>
<?= $menu() ?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
</div>

<main id="b-container">
    <div class="b-asset-metrics">
        <h1><?= trans('boomcms::asset.metrics.heading') ?></h1>

        <div class="b-asset-metric-form">
            <div class="form-left">
                <form action="/boomcms/asset-manager/metrics" method="post">
                    <span class="form-title">DATE RANGE</span>
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>" />
                    FROM <input class="boom-datepicker" type="text" name="from" value="<?= $request->get('from') ?: $request->old('from') ?>" />
                    TO <input class="boom-datepicker" type="text" name="to" value="<?= $request->get('to') ?: $request->old('to') ?>" />
                    <button type="submit">SUBMIT</button>
                    <a href="/boomcms/asset-manager/metrics?clear=1">CLEAR RANGE</a>
                </form>
            </div>
            <div class="form-right">
                <form action="/boomcms/asset-manager/metrics/csv" method="post">
                    <input type="hidden" name="_token" value="<?= csrf_token() ?>" />
                    <button type="submit">EXPORT TO CSV</button>
                </form>
            </div>
        </div>

        <ul>
            <?php if (count($errors->get('from'))) { ?>
                <li class="error"><?= $errors->first('from') ?></li>
            <?php }
            if (count($errors->get('to'))) { ?>
                <li class="error"><?= $errors->first('to') ?></li>
            <?php } ?>
        </ul>

        <?php 
        $from = trim($request->get('from')) !== '' ? trim($request->get('from')) : trim(session('from_date'));
        $to = trim($request->get('to')) !== '' ? trim($request->get('to')) : trim(session('to_date'));

        if ($from !== '' && $to !== '') { ?>
            <h3>Data from <?= $from ?> to <?= $to ?></h3>
        <?php } ?>

        <table id="b-templates" class="b-table tablesorter">
            <thead>
                <tr>
                    <th><a href="?sort=filename"><?= trans('boomcms::asset.metrics.filename') ?></a></th>
                    <th><a href="?sort=extension"><?= trans('boomcms::asset.metrics.ext') ?></a></th>
                    <th><a href="?sort=uploaded"><?= trans('boomcms::asset.metrics.uploaded-on') ?></a></th>
                    <th><a href="?sort=downloads"><?= trans('boomcms::asset.metrics.downloads') ?></a></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_downloads = 0;
                foreach ($assets as $asset) { ?>
                    <tr>
                        <td><a href="/boomcms/asset-manager/asset/<?= $asset->id ?>/info"><?= $asset->filename ?></a></td>
                        <td><?= $asset->extension ?></td>
                        <td><?= date('d M Y', $asset->created_at) ?></td>
                        <td><a href="/boomcms/asset-manager/metrics/<?= $asset->id ?>/details"><?= $asset->downloads ?></a></td>
                    </tr>
                <?php
                    $total_downloads += $asset->downloads;
                } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2"></th>
                    <th><?= trans('boomcms::asset.metrics.total') ?></th>
                    <th><?= $total_downloads ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
</main>

<?= view('boomcms::footer') ?>