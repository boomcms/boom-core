<div id="b-page-urls-move">
    <h1><?= trans('boomcms::page.urls.move.heading') ?> - /<?= ltrim($url->getLocation(), '/') ?></h1>

    <?php if ($url->isPrimary() && !$current->isDeleted()): ?>
        <p><strong><?= trans('boomcms::page.urls.move.primary') ?></strong></p>
    <?php endif ?>

    <?php if ($current->isDeleted()): ?>
        <p>
            <strong><?= trans('boomcms::page.urls.move.deleted-warning') ?></strong>
        </p>
    <?php endif ?>

    <table>
        <tr>
            <th>
                <?= trans('boomcms::page.urls.move.current') ?><?php if ($current->isDeleted()): ?> <?= trans('boomcms::page.urls.move.deleted') ?><?php endif ?>
            </th>

            <th>
                <?= trans('boomcms::page.urls.move.new') ?>
            </th>
        </tr>

        <tr>
            <td>
                <?= $current->getTitle() ?>
            </td>

            <td>
                <?= $page->getTitle() ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= trans('boomcms::page.'.(($current->isVisible()) ? 'visible' : 'invisible')) ?>
            </td>

            <td>
                <?= trans('boomcms::page.'.(($page->isVisible()) ? 'visible' : 'invisible')) ?>
            </td>
        </tr>

        <tr>
            <td>
                <?= ucfirst($current->getCurrentVersion()->getStatus()) ?>
            </td>

            <td>
                <?= ucfirst($page->getCurrentVersion()->getStatus()) ?>
            </td>
        </tr>
    </table>
</div>
