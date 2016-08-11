<div id="b-page-history">
    <h1><?= trans('boomcms::settings.history.heading') ?></h1>

    <ol>
        <?php foreach ($versions as $version): ?>
            <li data-status="<?= $version->getStatus() ?>">
                <div>
                    <a href="#" data-timestamp="<?= $version->getEditedTime()->getTimestamp() ?>">
                        <time datetime="<?= $version->getEditedTime()->format('c') ?>">
                            <?= $version->getEditedTime()->format('d M Y h:i') ?>
                        </time>
                    </a>

                    <span class="status"><?= trans('boomcms::page.status.'.$version->getStatus()) ?></span>
                </div>

                <?php if ($version->getEditedBy()): ?>
                    <p>
                        <?= $version->getEditedBy()->getName() ?>&nbsp;
                        <small><?= $version->getEditedBy()->getEmail() ?></small>                 
                    </p>
                <?php endif ?>
            </li>
        <?php endforeach ?>
    </ol>
</div>
