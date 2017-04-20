<?php
    $selected = isset($selected) ? $selected : 'created_at desc';

    $options = [
        'created_at desc',
        'created_at asc',
        'last_modified desc',
        'last_modified asc',
        'published_at desc',
        'published_at asc',
        'title asc',
        'title desc',
        'filesize asc',
        'filesize desc',
        'downloads desc',
    ]
?>

<div>
    <h2><?= trans('boomcms::asset.search.sort') ?></h2>

    <select id="b-assets-sortby">
        <?php foreach ($options as $o): ?>
            <option value="<?= $o ?>"<?php if ($selected === $o): ?> selected="selected"<?php endif ?>>
                <?= trans("boomcms::asset.sort.$o") ?>
            </option>
        <?php endforeach ?>
    </select>
</div>
