<div id="b-page-delete-options">
    <h1><?= Lang::get('boomcms::settings.delete.heading') ?> - <?= $page->getTitle() ?></h1>

    <?php if ($page->canBeDeleted()): ?>
        <p><?= Lang::get('boomcms::settings.delete.intro') ?></p>

        <?php if ($children > 0): ?>
            <p>
                <strong>Warning:</strong>
                <br />Deleting this page will make it's <?= $children ?> child <?= Lang::choice('boom.page', $children) ?> inaccessible.
            </p>

            <div id="b-page-delete-children">
                <input type="checkbox" name="with_children" value="1" />
                Delete <?= $children ?> child <?= Lang::choice('boom.page', $children) ?> as well.
            </div>
        <?php endif ?>

        <?= $button('trash-o', 'Delete page', ['id' => 'b-page-delete-confirm', 'class' => 'b-button-withtext']) ?>
    <?php else: ?>
        <p><?= Lang::get('boomcms::settings.delete.disabled') ?></p>
    <?php endif ?>
</div>