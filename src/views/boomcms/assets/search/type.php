<div>
    <select id="b-assets-types" name="type">
        <option value="0"><?= trans('boomcms::asset.type.all') ?></option>

        <?php foreach (['audip', 'doc', 'image', 'video'] as $type): ?>
            <option value="<?= $type ?>"<?php if (isset($selected) && $selected == $type): ?> selected="selected"<?php endif ?>>
                <?= trans('boomcms::asset.type.'.$type) ?>
            </option>
        <?php endforeach ?>
    </select>

    <label for="b-assets-types"><?= trans('boomcms::asset.search.type') ?></label>
</div>
