<div id="b-page-history">
    <h1><?= trans('boomcms::settings.history.heading') ?></h1>

    <ol>
        <?php foreach ($versions as $i => $version): ?>
            <?php if (isset($versions[$i + 1])): ?>
                <?php $compare = $diff->compare($version, $versions[$i + 1]) ?>

                <?php
                    if (isset($restoreTo) && $version->getId() === $restoreTo):
                        unset($restoreTo);
                    endif
                ?>

                <li<?php if (isset($restoreTo)): ?> class="reverted"<?php endif ?> data-status="<?= $version->getStatus() ?>">
                    <div class="summary">
                        <?php if ($compare): ?>
                            <span class="fa fa-<?= $compare->getIcon() ?>"></span>
                        <?php endif ?>

                        <p>
                            <?= $compare ?>
                        </p>
                    </div>

                    <?php if ($i > 0): ?>
                        <a href="#" data-restore="<?= $version->getId() ?>">
                            <span class="fa fa-undo"></span>
                            <span><?= trans('boomcms::settings.history.restore') ?></span>
                        </a>
                    <?php else: ?>
                        <div data-restore="">&nbsp;</div>
                    <?php endif ?>

                    <div class="main">
                        <div>
                            <a href="#" data-timestamp="<?= $version->getEditedTime()->getTimestamp() ?>">
                                <time datetime="<?= $version->getEditedTime()->format('c') ?>">
                                    <?= $version->getEditedTime()->format('d M Y h:i') ?>
                                </time>

                                <span class="fa fa-angle-double-right"></span>
                            </a>

                            <span class="status">
                                <?= trans('boomcms::page.status.'.$version->getStatus($version->getEditedTime())) ?>
                            </span>
                        </div>

                        <?php if ($version->getEditedBy()): ?>
                            <p>
                                <?= $version->getEditedBy()->getName() ?>&nbsp;
                                <small><?= $version->getEditedBy()->getEmail() ?></small>                 
                            </p>
                        <?php endif ?>
                    </div>
                </li>
            <?php endif ?>

            <?php if ($page->isVisibleAtTime($version->getEditedTime()) &&
                (!isset($versions[$i + 1]) ||
                !$page->isVisibleAtTime($versions[$i + 1]->getEditedTime()))): ?>

                <li class="visibility">
                    <div class="summary">
                        <span class='fa fa-eye'></span>
                    </div>

                    <div class="main">
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

                        <p>
                            <small>
                                <?= trans('boomcms::settings.history.visible-note') ?>
                            </small>
                        </p>
                    </div>
                </li>
            <?php endif ?>

            <?php
                if (!isset($restoreTo) && $version->getRestoredVersionId()):
                    $restoreTo = $version->getRestoredVersionId();
                endif;
            ?>
        <?php endforeach ?>

        <li data-status="created">
            <div class="summary">
                <span class="fa fa-plus"></span>
            </div>

            <div data-restore="">&nbsp;</div>

            <div class="main">
                <div>
                    <?php if ($page->getCreatedTime() && $page->getCreatedTime()->getTimestamp() > 0): ?>
                        <time datetime="<?= $page->getCreatedTime()->format('c') ?>">
                            <?= $page->getCreatedTime()->format('d M Y h:i') ?>
                        </time>
                    <?php endif ?>

                    <span class="status">
                        <?= trans('boomcms::settings.history.created') ?>
                    </span>
                </div>

                <?php if ($page->getCreatedBy()): ?>
                    <p>
                        <?= $page->getCreatedBy()->getName() ?>&nbsp;
                        <small><?= $page->getCreatedBy()->getEmail() ?></small>                 
                    </p>
                <?php endif ?>
            </div>
        </li>
    </ol>
</div>
