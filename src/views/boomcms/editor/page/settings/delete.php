<form id="b-page-delete-options">
    <h1><?= trans('boomcms::settings.delete.heading') ?> - <?= $page->getTitle() ?></h1>

    <?php if ($page->canBeDeleted()): ?>
        <p><?= trans('boomcms::settings.delete.intro') ?></p>

        <?php if ($children > 0): ?>
            <section>
                <h2><?= trans('boomcms::settings.delete.children.heading') ?></h2>
                <p><?= trans('boomcms::settings.delete.children.intro', ['count' => $children]) ?></p>

                <label>
                    <input type="radio" name="children" value="0" checked>
                    <span class="radio"></span>

                    <div>
                        <p><?= trans('boomcms::settings.delete.children.delete') ?></p>
                    </div>
                </label>

                <label>
                    <input type="radio" name="children" value="1">
                    <span class="radio"></span>

                    <div>
                        <p><?= trans('boomcms::settings.delete.children.reparent') ?></p>

                        <p class="target">
                            <?= trans('boomcms::settings.delete.children.reparent-target') ?>
                            <span></span>

                            (<a href="#" class="edit" data-option="reparentChildrenTo"><?= trans('boomcms::common.edit') ?></a>)
                        </p>
                    </div>
                </label>
            </section>
        <?php endif ?>

        <section>
            <h2><?= trans('boomcms::settings.delete.urls.heading') ?></h2>

            <p><?= trans('boomcms::settings.delete.urls.intro') ?></p>

            <label>
                <input type="radio" name="urls" value="0" checked>
                <span class="radio"></span>

                <div>
                    <p><?= trans('boomcms::settings.delete.urls.leave') ?></p>
                    <p class="explanation"><?= trans('boomcms::settings.delete.urls.leave-explanation') ?></p>
                </div>
            </label>

            <label>
                <input type="radio" name="urls" value="1">
                <span class="radio"></span>

                <div>
                    <p><?= trans('boomcms::settings.delete.urls.redirect') ?></p>
                    <p><?= trans('boomcms::settings.delete.urls.redirect-explanation') ?></p>

                    <p class="target">
                        <?= trans('boomcms::settings.delete.urls.redirect-target') ?>
                        <span></span>

                        (<a href="#" class="edit" data-option="redirectTo"><?= trans('boomcms::common.edit') ?></a>)
                    </p>
                </div>
            </label>
        </section>

        <?= $button('trash-o', 'Delete page', ['id' => 'b-page-delete-confirm', 'class' => 'b-button-withtext']) ?>
    <?php else: ?>
        <p><?= trans('boomcms::settings.delete.disabled') ?></p>
    <?php endif ?>
</form>
