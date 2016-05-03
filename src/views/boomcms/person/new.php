<form method="post" action="/boomcms/people/add" id="b-people-create-form" class="b-dialog-form">
    <label>
        Name
        <input type="text" name="name" />
    </label>

    <label for="create-email">
        Email
        <input type="text" id="create-email" name="email" class="boom-input" />
    </label>

    <label for="create-group">
        Groups

        <select name="groups[]" multiple>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group->getId() ?>"><?= $group->getName() ?></option>
            <?php endforeach ?>
        </select>
    </label>
</form>
