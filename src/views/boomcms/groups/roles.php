<table class='b-group-roles'>
    <thead>
        <th>Role</th>
        <th>Allow</th>
        <th>Deny</th>
        <th>Not set</th>
    </thead>
    <tbody>
        <?php foreach ($roles as $role): ?>
            <tr data-id="<?= $role->id ?>">
                <td><?= $role->description ?></td>

                <td>
                    <input type="radio" id="allow-<?= $role->id ?>" value="1" name="<?= $role->id ?>" />
                    <label for="allow-<?= $role->id ?>">Allow</label>
                </td>

                <td>
                    <input type="radio" id="deny-<?= $role->id ?>" value="0" name="<?= $role->id ?>" />
                    <label for="deny-<?= $role->id ?>">Deny</label>
                </td>

                <td>
                    <input type="radio" id="none-<?= $role->id ?>" value="-1" name="<?= $role->id ?>" checked="checked" />
                    <label for="none-<?= $role->id ?>">Not set</label>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
