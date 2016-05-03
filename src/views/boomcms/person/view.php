<div class="b-person-view" data-person-id='<?= $person->getId() ?>'>
    <section>
        <h2><?= trans('boomcms::people.details') ?></h2>

        <form>
            <label>
                <?= trans('boomcms::people.name') ?>

                <input type="text" name="name" value="<?= $person->getName() ?>" />
            </label>

            <label for="person-email">
                <?= trans('boomcms::people.email') ?>

                <input type="text" name="email" disabled="disabled" value="<?= $person->getEmail() ?>" />
            </label>

            <label for='person-status'>
                <?= trans('boomcms::people.status') ?>

                <select name="enabled" id="person-status">
                    <option value=""<?php if (!$person->isEnabled()): ?> selected="selected"<?php endif ?>>Disabled</option>
                    <option value="1"<?php if ($person->isEnabled()): ?> selected="selected"<?php endif ?>>Enabled</option>
                </select>
            </label>

            <?php if (Gate::allows('editSuperuser', $person)): ?>
                <label for='person-superuser'>
                    <?= trans('boomcms::people.superuser') ?>

                    <select name="superuser" id="person-superuser">
                        <option value=""<?php if (!$person->isSuperuser()): ?> selected="selected"<?php endif ?>>No</option>
                        <option value="1"<?php if ($person->isSuperuser()): ?> selected="selected"<?php endif ?>>Yes</option>
                    </select>
                </label>
            <?php endif ?>

            <div>
                <?= $button('save', 'save', ['id' => 'b-person-save', 'class' => 'b-people-save']) ?>
                <?= $button('trash-o', 'delete', ['id' => 'b-person-delete']) ?>
            </div>
        </form>
    </section>

    <section>
        <h2><?= trans('boomcms::people.groups-heading') ?></h2>
        <p><?= trans('boomcms::people.groups', ['name' => $person->getName()]) ?></p>

        <select class='b-person-groups' multiple>
            <?php foreach ($groups as $group): ?>
                <option value='<?= $group->getId() ?>'<?php if ($hasGroups->contains($group)): ?> selected<?php endif ?>>
                    <?= $group->getName() ?>
                </option>
            <?php endforeach ?>
        </select>
    </section>

    <?php if (Gate::allows('manageSites', Router::getActiveSite())): ?>
        <section>
            <h2><?= trans('boomcms::people.sites-heading') ?></h2>
            <p><?= trans('boomcms::people.sites', ['name' => $person->getName()]) ?></p>

            <select class='b-person-sites' multiple>
                <?php foreach ($sites as $site): ?>
                    <option value='<?= $site->getId() ?>'<?php if ($hasSites->contains($site)): ?> selected<?php endif ?>>
                        <?= $site->getName() ?>
                    </option>
                <?php endforeach ?>
            </select>
        </section>
    <?php endif ?>
</div>
