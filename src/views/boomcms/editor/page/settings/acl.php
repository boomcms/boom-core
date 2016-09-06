<div id="b-page-acl">
    <h1><?= trans('boomcms::settings.acl.heading') ?></h1>
    <p><?=trans('boomcms::settings.acl.description') ?></p>

    <section>
        <h2><?= trans('boomcms::settings.acl.is-enabled') ?></h2>

        <select name="b-page-acl-toggle">
            <option value="1" <?php if ($page->aclEnabled()): ?> selected<?php endif ?>>
                <?= trans('boomcms::settings.acl.enabled') ?>
            </option>

            <option value="0" <?php if (!$page->aclEnabled()): ?> selected<?php endif ?>>
                <?= trans('boomcms::settings.acl.disabled') ?>
            </option>
        </select>
    </section>

    <section id="b-page-acl-groups">
        <h2><?= trans('boomcms::settings.acl.select-groups') ?></h2>
        <p><?= trans('boomcms::settings.acl.select-groups2') ?></p>
        <p><?= trans('boomcms::settings.acl.select-groups3') ?></p>
        <p><?= trans('boomcms::settings.acl.select-groups4') ?></p>
        
        <select name="groups[]" multiple>
            <?php foreach ($allGroups as $group): ?>
                <option value="<?= $group->getId() ?>"<?= in_array($group->getId(), $groupIds) ? ' selected' : '' ?>><?= $group->getName() ?></option>
            <?php endforeach ?>>
        </select>
    </section>
</div>
