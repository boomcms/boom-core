<div>
    <h2><?= trans('boomcms::asset.search.uploaded-by') ?></h2>

    <select id="b-assets-uploadedby" name="uploadedby">
        <option value="0"><?= trans('boomcms::asset.search.uploaded-by') ?></option>

        <?php foreach (Person::getAssetUploaders() as $person): ?>
            <option value="<?= $person->getId() ?>"<?php if (isset($selected) && $selected == $person->getId()): ?> selected="selected"<?php endif ?>>
                <?= $person->getName() ?> (<?= $person->getEmail() ?>)
            </option>
        <?php endforeach ?>
    </select>
</div>
