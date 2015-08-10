<div>
    <h1><?= Lang::get('boom::settings.draft-status.heading') ?></h1>
    <p><?= Lang::get('boom::settings.draft-status.intro') ?></p>

    <?php if ($version->isDraft()): ?>
        <p><?= Lang::get('boom::settings.draft-status.draft') ?></p>
        
        <?php if (!$auth->loggedIn('publish_page', $page)): ?>
            <?= $button('thumbs-up', Lang::get('boom::buttons.request-approval'), ['class' => 'b-button-withtext b-page-request-approval']) ?>
        <?php endif ?>
    <?php elseif ($version->isPendingApproval()): ?>
        <p><?= Lang::get('boom::settings.draft-status.pending') ?></p>
    <?php elseif ($version->isEmbargoed()): ?>
        <p>
            <?= Lang::get('boom::settings.draft-status.embargoed', [
                'date' => $version->getEmbargoedUntil()->format('l d F Y'),
                'time' => $version->getEmbargoedUntil()->format('H:i'),
            ]) ?>
        </p>
    <?php elseif ($version->isPublished()): ?>
        <p><?= Lang::get('boom::settings.draft-status.published') ?></p>
    <?php endif ?>
        
    <p>
        <?= Lang::get('boom::settings.draft-status.latest', [
            'name'  => $version->getEditedBy()->getName(),
            'email' => $version->getEditedBy()->getEmail(),
            'date'  => $version->getEditedTime()->format('l d F Y'),
            'time'  => $version->getEditedTime()->format('H:i')
        ]) ?>
    </p>
    
    <?php if (!$version->isPublished()): ?>
        <p>
            <?= Lang::get('boom::settings.draft-status.last-published', [
                'date' => $page->getLastPublishedTime()->format('l d F Y'),
                'time' => $page->getLastPublishedTime()->format('H:i'),
            ]) ?>
        </p>
        
        <?php if ($auth->loggedIn('publish_page', $page)): ?>
            <?= $button('check', Lang::get('boom::buttons.publish'), ['class' => 'b-button-withtext b-page-publish']) ?>
            
            <?php if ($version->isEmbargoed()): ?>
                <?= $button('clock-o', Lang::get('boom::buttons.embargo-change'), ['class' => 'b-button-withtext b-page-embargo']) ?>
            <?php else: ?>
                <?= $button('clock-o', Lang::get('boom::buttons.embargo'), ['class' => 'b-button-withtext b-page-embargo']) ?>
            <?php endif ?>

            <?= $button('undo', Lang::get('boom::buttons.page-revert'), ['class' => 'b-button-withtext b-page-revert']) ?>
        <?php endif ?>
    <?php endif ?>
</div>
