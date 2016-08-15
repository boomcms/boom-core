<div id="b-page-history">
    <h1><?= trans('boomcms::settings.history.heading') ?></h1>

    <ol>
        <?php foreach ($versions as $i => $version): ?>
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

                <?php if (isset($versions[$i + 1])): ?>
                    <p class="description">
                        <?= $diff->compare($version, $versions[$i + 1]) ?>
                    </p>
                <?php endif ?>
            </li>

            <?php if ($page->isVisibleAtTime($version->getEditedTime()) &&
                (!isset($versions[$i + 1]) ||
                !$page->isVisibleAtTime($versions[$i + 1]->getEditedTime()))): ?>

                <li class="visibility">
                    <div>
                        <a href="#" data-timestamp="<?= $page->getVisibleFrom()->getTimestamp() ?>">
                            <time datetime="<?= $page->getVisibleFrom()->format('c') ?>">
                                <?=$page->getVisibleFrom()->format('d M Y h:i') ?>
                            </time>
                        </a>

                        <span class="status">
                            <?= trans('boomcms::settings.history.visible') ?>
                        </span>
                    </div>
                    
                    <small><?= trans('boomcms::settings.history.visible-note') ?></small>
                </li>
            <?php endif ?>
        <?php endforeach ?>
    </ol>
</div>
