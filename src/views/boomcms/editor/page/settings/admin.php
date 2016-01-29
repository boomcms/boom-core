<form>
    <h1><?= trans('boomcms::settings.admin.heading') ?></h1>

    <label>
        <p><?= trans('boomcms::settings.admin.internal-name') ?></p>
        <input type='text' name='internal_name' value='<?= $page->getInternalName() ?>' />
    </label>

    <?php if (Gate::allows('editDeletable', $page)): ?>
        <label>
            <p><?= trans('boomcms::settings.admin.disable-delete') ?></p>

            <select name="disable_delete">
                <option value="1"<?php if (!$page->canBeDeleted()): ?> selected<?php endif ?>>Yes</option>
                <option value=""<?php if ($page->canBeDeleted()): ?> selected<?php endif ?>>No</option>
            </select>
        </label>
    <?php endif ?>

    <section>
        <h2><?= trans('boomcms::settings.admin.add-behaviour-heading') ?></h2>
        <p><?= trans('boomcms::settings.admin.add-behaviour-desc') ?></p>

        <label>
            <p><?= trans('boomcms::settings.admin.add-behaviour') ?></p>

            <select name="add_behaviour">
                <?php foreach (Lang::get('boomcms::page.add-behaviour') as $value => $desc): ?>
                  <option value="<?= $value ?>"<?php if ($page->getAddPageBehaviour() === $value): ?> selected<?php endif ?>>
                    <?= $desc ?>
                  </option>
                <?php endforeach ?>
            </select>
        </label>

        <label>
            <p><?= trans('boomcms::settings.admin.child-add-behaviour') ?></p>

            <select name="child_add_behaviour">
                <?php foreach (Lang::get('boomcms::page.add-behaviour') as $value => $desc): ?>
                  <option value="<?= $value ?>"<?php if ($page->getChildAddPageBehaviour() === $value): ?> selected<?php endif ?>>
                    <?= $desc ?>
                  </option>
                <?php endforeach ?>
            </select>
        </label>
    </section>

    <?= $button('refresh', 'reset', ['class' => 'b-button-cancel b-button-withtext']) ?>
    <?= $button('save', 'save', ['class' => 'b-button-save b-button-withtext']) ?>
</form>
