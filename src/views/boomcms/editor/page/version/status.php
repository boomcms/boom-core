<div id="b-page-draft-status">
    <section>
        <h1><?= trans('boomcms::settings.draft.heading') ?></h1>
        <p><?= trans('boomcms::settings.draft.intro') ?></p>

        <?php if ($version->isDraft() && !$version->isPendingApproval()): ?>
            <p><?= trans('boomcms::settings.draft.draft') ?></p>
        <?php elseif ($version->isPendingApproval()): ?>
            <p><?= trans('boomcms::settings.draft.pending') ?></p>
        <?php elseif ($version->isEmbargoed()): ?>
            <p>
                <?= trans('boomcms::settings.draft.embargoed', [
                    'date' => $version->getEmbargoedUntil()->format('l d F Y'),
                    'time' => $version->getEmbargoedUntil()->format('H:i'),
                ]) ?>
            </p>
        <?php elseif ($version->isPublished()): ?>
            <p><?= trans('boomcms::settings.draft.published') ?></p>
        <?php endif ?>

        <?php if ($editedBy = $version->getEditedBy()): ?>
            <p>
                <?= trans('boomcms::settings.draft.latest', [
                    'name'  => $editedBy->getName(),
                    'email' => $editedBy->getEmail(),
                    'date'  => $version->getEditedTime()->format('l d F Y'),
                    'time'  => $version->getEditedTime()->format('H:i'),
                ]) ?>
            </p>
        <?php endif ?>
    </section>

    <?php if (!$version->isPublished() || !$page->isVisible()): ?>
        <section>
            <h1><?= trans('boomcms::settings.draft.preview.heading') ?></h1>

            <?php if (!$version->isPublished()): ?>
                <p><?= trans('boomcms::settings.draft.preview.description-draft') ?></p>
            <?php else: ?>
                <p><?= trans('boomcms::settings.draft.preview.description-invisible') ?></p>
            <?php endif ?>

            <?= $button('search', 'preview', [
                'class'    => 'b-button-withtext b-page-preview',
                'data-url' => (string) $page->url(),
            ]) ?>
        </section>
    <?php endif ?>

    <?php if (!$version->isPublished()): ?>
        <section>
            <h1><?= trans('boomcms::settings.draft.options.heading') ?></h1>
            <p>
                <?php $lastPublished = $page->getLastPublished() ?>

                <?= trans('boomcms::settings.draft.last-published', [
                    'date' => $lastPublished->getEditedTime()->format('l d F Y'),
                    'time' => $lastPublished->getEditedTime()->format('H:i'),
                ]) ?>
            </p>

            <?php if (Gate::allows('publish', $page)): ?>
                <?= $button('thumbs-up', 'publish', ['class' => 'b-button-withtext b-page-publish']) ?>

                <?php if ($version->isEmbargoed()): ?>
                    <?= $button('clock-o', 'embargo-change', ['class' => 'b-button-withtext b-page-embargo']) ?>
                <?php else: ?>
                    <?= $button('clock-o', 'embargo', ['class' => 'b-button-withtext b-page-embargo']) ?>
                <?php endif ?>

                <?= $button('undo', 'page-revert', [
                    'class'           => 'b-button-withtext b-page-revert',
                    'data-version-id' => $lastPublished->getId(),
                ]) ?>
            <?php else: ?>
                <?= $button('thumbs-up', 'request-approval', ['class' => 'b-button-withtext b-page-request-approval']) ?>
            <?php endif ?>
        </section>
    <?php endif ?>
</div>
