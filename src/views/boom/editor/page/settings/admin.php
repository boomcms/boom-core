<form>
    <h1><?= Lang::get('boom::settings.admin.heading') ?></h1>

    <label>
        <p><?= Lang::get('boom::settings.admin.internal-name') ?></p>
        <input type='text' name='internal_name' value='<?= $page->getInternalName() ?>' />
    </label>

    <?php if ($auth->loggedIn('edit_disable_delete', $page)): ?>
        <label>
            <p><?= Lang::get('boom::settings.admin.disable-delete') ?></p>

            <select name="disable_delete">
                <option value="0"<?php if ($page->canBeDeleted()): ?> selected<?php endif ?>>No</option>
                <option value="1"<?php if (!$page->canBeDeleted()): ?> selected<?php endif ?>>Yes</option>
            </select>
        </label>
    <?php endif ?>

    <?= $button('times', Lang::get('boom::buttons.cancel'), ['class' => 'b-button-cancel b-button-withtext']) ?>
    <?= $button('save', Lang::get('boom::buttons.save'), ['class' => 'b-button-save b-button-withtext']) ?>
</form>
