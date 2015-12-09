<div>
    <h1><?= trans('boomcms::settings.draft-status.heading') ?></h1>
    <p><?= trans('boomcms::settings.draft-status.intro') ?></p>

    <?php if ($version->isDraft() && !$version->isPendingApproval()): ?>
        <p><?= trans('boomcms::settings.draft-status.draft') ?></p>
        
        <?php if (!$auth->loggedIn('publish_page', $page)): ?>
            <?= $button('thumbs-up', 'request-approval', ['class' => 'b-button-withtext b-page-request-approval']) ?>
        <?php endif ?>
    <?php elseif ($version->isPendingApproval()): ?>
        <p><?= trans('boomcms::settings.draft-status.pending') ?></p>
    <?php elseif ($version->isEmbargoed()): ?>
        <p>
            <?= trans('boomcms::settings.draft-status.embargoed', [
                'date' => $version->getEmbargoedUntil()->format('l d F Y'),
                'time' => $version->getEmbargoedUntil()->format('H:i'),
            ]) ?>
        </p>
    <?php elseif ($version->isPublished()): ?>
        <p><?= trans('boomcms::settings.draft-status.published') ?></p>
    <?php endif ?>
        
    <p>
        <?= trans('boomcms::settings.draft-status.latest', [
            'name'  => $version->getEditedBy()->getName(),
            'email' => $version->getEditedBy()->getEmail(),
            'date'  => $version->getEditedTime()->format('l d F Y'),
            'time'  => $version->getEditedTime()->format('H:i'),
        ]) ?>
    </p>
    
    <?php if (!$version->isPublished()): ?>
        <p>
            <?= trans('boomcms::settings.draft-status.last-published', [
                'date' => $page->getLastPublishedTime()->format('l d F Y'),
                'time' => $page->getLastPublishedTime()->format('H:i'),
            ]) ?>
        </p>
        
        <?php if ($auth->loggedIn('publish_page', $page)): ?>
            <?= $button('check', 'publish', ['class' => 'b-button-withtext b-page-publish']) ?>
            
            <?php if ($version->isEmbargoed()): ?>
                <?= $button('clock-o', 'embargo-change', ['class' => 'b-button-withtext b-page-embargo']) ?>
            <?php else: ?>
                <?= $button('clock-o', 'embargo', ['class' => 'b-button-withtext b-page-embargo']) ?>
            <?php endif ?>

            <?= $button('undo', 'page-revert', ['class' => 'b-button-withtext b-page-revert']) ?>
        <?php endif ?>
    <?php endif ?>
        
    <?php if (!$version->isPublished() || !$page->isVisible()): ?>
        <div>
            <?= $button('', 'preview', [
                'class'    => 'b-button-textonly b-page-preview',
                'data-url' => (string) $page->url(),
            ]) ?>
        </div>
    <?php endif ?>
</div>
