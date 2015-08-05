<div>
    <h1><?= Lang::get('boom::settings.draft-status.heading') ?></h1>
    <p><?= Lang::get('boom::settings.draft-status.intro') ?></p>

    <?php if ($version->isDraft()): ?>
        <p><?= Lang::get('boom::settings.draft-status.draft') ?></p>
        
        <?php if ($auth->loggedIn('publish', $page)): ?>
            <?php /* Publish, embargo, and revert to published buttons. */ ?>
        <?php else: ?>
            <?php /* request approval button. */ ?>
        <?php endif ?>
    <?php elseif ($version->isPendingApproval()): ?>
        <p><?= Lang::get('boom::settings.draft-status.pending') ?></p>
        
        <?php if ($auth->loggedIn('publish', $page)): ?>
            <?php /* Publish, embargo, and revert to published buttons. */ ?>
        <?php else: ?>
            <?php /* request approval button. */ ?>
        <?php endif ?>
    <?php elseif ($version->isEmbargoed()): ?>
        <p>
            <?= Lang::get('boom::settings.draft-status.embargoed', [
                'date' => $version->getEmbargoedUntil()->format('l d F Y H:i'),
            ]) ?>
        </p>
        
        <?php if ($auth->loggedIn('publish', $page)): ?>
            <?php /* Publish now , change embargo, and revert to published buttons. */ ?>
        <?php endif ?>
    <?php elseif ($version->isPublished()): ?>
        <p><?= Lang::get('boom::settings.draft-status.published') ?></p>
    <?php endif ?>
        
    <p>
        <?= Lang::get('boom::settings.draft-status.latest', [
            'name' => $version->getEditedBy()->getName(),
            'email' => $version->getEditedBy()->getEmail(),
            'date' => $version->getEditedTime()->format('l d F Y H:i'),
        ]) ?>
        
        <?php if ($version->isPublished()): ?>
            <?= Lang::get('boom::settings.draft-status.published-since', [
                'date' => $version->getEmbargoedUntil()->format('l d F Y H:i')
            ]) ?>
        <?php endif ?>
    </p>
    
    <?php if ( !$version->isPublished()): ?>
        <p>
            <?= Lang::get('boom::settings.draft-status.last-published', [
                'date' => $page->getLastPublishedTime()->format('l d F Y H:i')
            ]) ?>
        </p>
    <?php endif ?>
</div>
