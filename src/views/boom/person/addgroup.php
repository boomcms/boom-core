<div id="b-people-addgroup">
    <form onsubmit='return false;'>
        <p>Select some groups to add from the list below.</p>
        <p>You can select multiple groups to add the person to all selected groups.</p>

        <select name="groups[]" multiple>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group->getId() ?>"><?= $group->getName() ?></option>
            <?php endforeach ?>
        </select>
    </form>
</div>
