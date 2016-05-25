<div>
    <h2><?= trans('boomcms::asset.search.type') ?></h2>

    <select id="b-assets-types" name="types">
        <option value="0">Filter by type</option>

        <?php foreach (array_keys(AssetHelper::types()) as $type): ?>
            <option value="<?= $type ?>"<?php if (isset($selected) && $selected == $type): ?> selected="selected"<?php endif ?>>
                <?= trans('boomcms::asset.type.'.$type) ?>
            </option>
        <?php endforeach ?>
    </select>
</div>
