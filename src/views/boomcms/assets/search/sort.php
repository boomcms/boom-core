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
    <select id="b-assets-sortby" name='order'>
        <option value="<?= $selected ?>">
            <?= trans('boomcms::asset.search.sort') ?>
        </option>

        <?php foreach ($options as $o): ?>
            <option value="<?= $o ?>">
                <?= trans("boomcms::asset.sort.$o") ?>
            </option>
        <?php endforeach ?>
    </select>

    <label for="b-assets-sortby"><?= trans('boomcms::asset.search.sort') ?></label>
</div>
