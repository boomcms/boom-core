<section>
    <h2><?= trans('boomcms::people.details') ?></h2>

    <form>
        <label>
            <?= trans('boomcms::people.name') ?>

            <input type="text" name="name" value="<%= name %>" />
        </label>

        <label for="person-email">
            <?= trans('boomcms::people.email') ?>

            <input type="text" name="email" disabled="disabled" value="<%= email %>" />
        </label>

        <label for='person-status'>
            <?= trans('boomcms::people.status') ?>

            <select name="enabled" id="person-status">
                <option value="">Disabled</option>
                <option value="1">>Enabled</option>
            </select>
        </label>

        <?php /*if (Gate::allows('editSuperuser', $person)): ?>
            <label for='person-superuser'>
                <?= trans('boomcms::people.superuser') ?>

                <select name="superuser" id="person-superuser">
                    <option value="">No</option>
                    <option value="1">Yes</option>
                </select>
            </label>
        <?php endif*/ ?>

        <div>
            <?= $button('save', 'save', ['id' => 'b-person-save', 'class' => 'b-people-save']) ?>
            <?= $button('trash-o', 'delete', ['id' => 'b-person-delete']) ?>
        </div>
    </form>
</section>

<section>
    <h2><?= trans('boomcms::people.groups-heading') ?></h2>
    <p><?= trans('boomcms::people.groups') ?></p>

    <select class='b-person-groups' multiple>

    </select>
</section>

<?php if (Gate::allows('manageSites', Router::getActiveSite())): ?>
    <section>
        <h2><?= trans('boomcms::people.sites-heading') ?></h2>
        <p><?= trans('boomcms::people.sites') ?></p>

        <select class='b-person-sites' multiple>

        </select>
    </section>
<?php endif ?>
