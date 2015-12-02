<form id="b-page-delete-options">
    <h1><?= Lang::get('boomcms::settings.delete.heading') ?> - <?= $page->getTitle() ?></h1>

    <?php if ($page->canBeDeleted()): ?>
        <p><?= Lang::get('boomcms::settings.delete.intro') ?></p>

        <?php if ($children > 0): ?>
            <section>
                <h2><?= Lang::get('boomcms::settings.delete.children.heading') ?></h2>

                <p><?= Lang::get('boomcms::settings.delete.children.intro', ['count' => $children]) ?></p>
            </section>

            <label>
                <input type="radio" name="children" value="0" checked>
                <span class="radio"></span>

                <div>
                    <p><?= Lang::get('boomcms::settings.delete.children.delete') ?></p>
                </div>
            </label>

            <label>
                <input type="radio" name="children" value="1">
                <span class="radio"></span>

                <div>
                    <p><?= Lang::get('boomcms::settings.delete.children.reparent') ?></p>
                    
                    <p class="target">
                        <?= Lang::get('boomcms::settings.delete.children.reparent-target') ?>
                        <span></span>

                        (<a href="#" class="edit" data-option="reparentChildrenTo"><?= Lang::get('boomcms::common.edit') ?></a>)
                    </p>
                </div>
            </label>
        <?php endif ?>

        <section>
            <h2><?= Lang::get('boomcms::settings.delete.urls.heading') ?></h2>

            <p><?= Lang::get('boomcms::settings.delete.urls.intro') ?></p>

            <label>
                <input type="radio" name="urls" value="0" checked>
                <span class="radio"></span>

                <div>
                    <p><?= Lang::get('boomcms::settings.delete.urls.leave') ?></p>
                    <p class="explanation"><?= Lang::get('boomcms::settings.delete.urls.leave-explanation') ?></p>
                </div>
            </label>

            <label>
                <input type="radio" name="urls" value="1">
                <span class="radio"></span>

                <div>
                    <p><?= Lang::get('boomcms::settings.delete.urls.redirect') ?></p>
                    <p><?= Lang::get('boomcms::settings.delete.urls.redirect-explanation') ?></p>
                    
                    <p class="target">
                        <?= Lang::get('boomcms::settings.delete.urls.redirect-target') ?>
                        <span></span>

                        (<a href="#" class="edit" data-option="redirectTo"><?= Lang::get('boomcms::common.edit') ?></a>)
                    </p>
                </div>
            </label>
        </section>

        <?= $button('trash-o', 'Delete page', ['id' => 'b-page-delete-confirm', 'class' => 'b-button-withtext']) ?>
    <?php else: ?>
        <p><?= Lang::get('boomcms::settings.delete.disabled') ?></p>
    <?php endif ?>
</form>