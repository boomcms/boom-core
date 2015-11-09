<?php
    $selected = isset($selected) ? $selected : 'last_modified desc';

    $options = [
        'last_modifed desc',
        'last_modified asc',
        'title asc',
        'title desc',
        'filesize asc',
        'filesize desc',
        'downloads desc',
    ]
?>

<select id="b-assets-sortby">
    <?php foreach ($options as $o): ?>
        <option value="<?= $o ?>"<?php if ($selected === $o): ?> selected="selected"<?php endif ?>>
            <?= Lang::get("boomcms::asset.sort.$o") ?>
        </option>
    <?php endforeach ?>
</select>