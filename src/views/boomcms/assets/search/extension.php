<div>
    <h2><?= trans('boomcms::asset.search.extension') ?></h2>

    <select id="b-assets-extensions" name="extension">
        <option value="0">
            <?= trans('boomcms::asset.search.extension') ?>
        </option>

        <?php foreach (Asset::extensions() as $extension): ?>
            <option value="<?= $extension ?>"<?php if (isset($selected) && $selected === $extension): ?> selected="selected"<?php endif ?>>
                <?= $extension ?>
            </option>
        <?php endforeach ?>
    </select>
</div>
