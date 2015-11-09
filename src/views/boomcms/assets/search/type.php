<select id="b-assets-types" name="types">
    <option value="0">Filter by type</option>

    <?php foreach (array_keys(AssetHelper::types()) as $type): ?>
        <option value="<?= $type ?>"<?php if (isset($selected) && $selected == $type): ?> selected="selected"<?php endif ?>>
            <?= Lang::get('boomcms::asset.type.'.$type) ?>
        </option>
    <?php endforeach ?>
</select>