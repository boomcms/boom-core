<form>
    <h1><?= trans('boomcms::settings.admin.heading') ?></h1>

    <label>
        <p><?= trans('boomcms::settings.admin.internal-name') ?></p>
        <input type='text' name='internal_name' value='<?= $page->getInternalName() ?>' />
    </label>

    <?php if ($auth->check('editDeletable', $page)): ?>
        <label>
            <p><?= trans('boomcms::settings.admin.disable-delete') ?></p>

            <select name="disable_delete">
                <option value="1"<?php if (!$page->canBeDeleted()): ?> selected<?php endif ?>>Yes</option>
                <option value="0"<?php if ($page->canBeDeleted()): ?> selected<?php endif ?>>No</option>
            </select>
        </label>
    <?php endif ?>

    <?= $button('refresh', 'reset', ['class' => 'b-button-cancel b-button-withtext']) ?>
    <?= $button('save', 'save', ['class' => 'b-button-save b-button-withtext']) ?>
</form>
