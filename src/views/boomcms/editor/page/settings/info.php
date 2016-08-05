<div>
    <h1><?= trans('boomcms::settings.info.heading') ?></h1>

    <dl>
        <?php if ($page->getCreatedTime()->getTimesstamp()): ?>
            <dt><?= trans('boomcms::settings.info.created-at') ?></dt>
            <dd><?= $page->getCreatedTime()->format('d M Y H:i') ?></dd>
        <?php endif ?>

        <dt><?= trans('boomcms::settings.info.created-by') ?></dt>
        <dd><?= $page->getCreatedBy()->getName() ?>&nbsp;<small><?= $page->getCreatedBy()->getEmail() ?></small></dd>
    </dl>
</div>
