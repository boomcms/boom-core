<div>
    <select id="b-assets-extensions" name="extension">
        <option value="0">
            <?= trans('boomcms::asset.search.all-extensions') ?>
        </option>

        <?php foreach (Asset::extensions() as $extension): ?>
            <option value="<?= $extension ?>"<?php if (isset($selected) && $selected === $extension): ?> selected="selected"<?php endif ?>>
                <?= $extension ?>
            </option>
        <?php endforeach ?>
    </select>

    <label for="b-assets-extensions"><?= trans('boomcms::asset.search.extension') ?></label>

</div>
