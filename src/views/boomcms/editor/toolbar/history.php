<?= view('boomcms::header', ['title' => $page->getTitle()]) ?>

<div id="b-topbar" class="b-page-toolbar b-page-toolbar-history b-toolbar">
	<?= view('boomcms::editor.toolbar.edit-button') ?>

    <button class="b-version-info">
        <span class="fa fa-info"></span>
        <span class="text"><?= trans('boomcms::page.history.info') ?></span>
    </button>

    <?php if (isset($previous)): ?>
        <button data-editor-time="<?= $previous->getEditedTime()->getTimestamp() ?>">
            <span class="fa fa-step-backward"></span>
            <span class="text"><?= trans('boomcms::page.history.prev') ?></span>
        </a>
    <?php endif ?>

    <?php if (isset($next)): ?>
        <button data-editor-time="<?= $next->getEditedTime()->getTimestamp() ?>">
            <span class="fa fa-step-forward"></span>
            <span class="text"><?= trans('boomcms::page.history.next') ?></span>
        </a>
    <?php endif ?>

    <?= view('boomcms::editor.toolbar.view-live-button') ?>
</div>

<script type="text/template" id="b-history-template">
    <dl>
        <?php if ($version->getEditedTime()->getTimestamp()): ?>
            <dt><?= trans('boomcms::page.history.edited-at') ?></dt>

            <dd>
                <time datetime="<?= $version->getEditedTime()->format('c') ?>">
                    <?= $version->getEditedTime()->format('d M Y h:i') ?>
                </time>
            </dd>
        <?php endif ?>

        <?php if ($version->getEditedBy()): ?>
            <dt><?= trans('boomcms::page.history.edited-by') ?></dt>
            <dd><?= $version->getEditedBy()->getName() ?><br /><small><?= $version->getEditedBy()->getEmail() ?></small></dd>
        <?php endif ?>

        <dt><?= trans('boomcms::page.history.status') ?>
        <dd>
            <?= trans('boomcms::page.status.'.$version->getStatus($version->getEditedTime())) ?>
        </dd>

        <?php if (isset($previous)): ?>
            <dt><?= trans('boomcms::page.history.summary') ?>
            <dd><?= $diff->compare($version, $previous) ?></dd> 
        <?php endif ?>
    </dl>
</script>

<?= view('boomcms::editor.toolbar.footer')->render() ?>
