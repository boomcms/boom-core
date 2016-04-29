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
        <h2><?= trans('boomcms::people.groups') ?></h2>

        <?php if (!count($groups)): ?>
            <p><?= trans('boomcms::people.nogroups', ['name' => $person->getName()]) ?></p>
        <?php else: ?>
            <p><?= trans('boomcms::people.hasgroups', ['name' => $person->getName()]) ?></p>

            <ul id='b-person-groups-list'>
                 <?php foreach ($groups as $group): ?>
                     <li data-group-id='<?= $group->getId() ?>'>
                         <?= $group->getName() ?>&nbsp;<a title='Remove user from group' class='b-person-group-delete' href='#'>x</a>
                     </li>
                 <?php endforeach ?>
            </ul>
        <?php endif ?>

        <?= $button('plus', 'add-group', ['class' => 'b-person-addgroups', 'rel' => $person->getId()]) ?>
    </section>
</div>
