<form>
    <label>
        Internal name
        <input type='text' name='internal_name' value='<?= $page->getInternalName() ?>' />
    </label>

    <?php if ($auth->loggedIn('edit_disable_delete', $page)): ?>
        <label>
            <?= Lang::get('boom::settings.admin.disable_delete') ?>

            <select name="disable_delete">
                <option value="0"<?php if ($page->canBeDeleted()): ?> selected<?php endif ?>>No</option>
                <option value="1"<?php if ( !$page->canBeDeleted()): ?> selected<?php endif ?>>Yes</option>
            </select>
        </label>
    <?php endif ?>
</form>
