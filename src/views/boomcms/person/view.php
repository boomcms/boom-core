<div class="b-person-view" data-person-id='<?= $person->getId() ?>'>
    <section>
        <h2>User details</h2>

        <form>
            <label>
                Name
                <input type="text" name="name" value="<?= $person->getName() ?>" />
            </label>

            <label for="person-email">
                Email
                <input type="text" name="email" disabled="disabled" value="<?= $person->getEmail() ?>" />
            </label>

            <label for='person-status'>
                Status

                <select name="enabled" id="person-status">
                    <option value=""<?php if (!$person->isEnabled()): ?> selected="selected"<?php endif ?>>Disabled</option>
                    <option value="1"<?php if ($person->isEnabled()): ?> selected="selected"<?php endif ?>>Enabled</option>
                </select>
            </label>

            <?php if (Gate::allows('editSuperuser', $person)): ?>
                <label for='person-superuser'>
                    Superuser

                    <select name="superuser" id="person-superuser">
                        <option value=""<?php if (!$person->isSuperuser()): ?> selected="selected"<?php endif ?>>No</option>
                        <option value="1"<?php if ($person->isSuperuser()): ?> selected="selected"<?php endif ?>>Yes</option>
                    </select>
                </label>
            <?php endif ?>

            <div>
                <?= $button('save', trans('Save'), ['id' => 'b-person-save', 'class' => 'b-people-save']) ?>
                <?= $button('trash-o', trans('Delete'), ['id' => 'b-person-delete']) ?>
            </div>
        </form>
    </section>

    <section>
        <h2>Groups</h2>

        <p><?= $person->getName() ?>

        <?php if (count($groups) == 0): ?>
            is not a member of any groups.</p>
        <?php else: ?>
            is a member of these groups:</p>

            <ul id='b-person-groups-list'>
                 <?php foreach ($groups as $group): ?>
                     <li data-group-id='<?= $group->getId() ?>'>
                         <?= $group->getName() ?>&nbsp;<a title='Remove user from group' class='b-person-group-delete' href='#'>x</a>
                     </li>
                 <?php endforeach ?>
            </ul>
        <?php endif ?>

        <?= $button('plus', trans('Add group'), ['class' => 'b-person-addgroups', 'rel' => $person->getId()]) ?>
    </section>
</div>
