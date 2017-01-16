<?= $button('accept', 'all-assets', ['id' => 'b-assets-all', 'class' => 'b-button-textonly']) ?>
<?= view('boomcms::assets.search.title') ?>
<?= view('boomcms::assets.search.type') ?>
<?= view('boomcms::assets.search.extension') ?>
<?= view('boomcms::assets.search.uploaded-by') ?>

<div>
    <h2><?= trans('boomcms::asset.search.tag') ?></h2>
    <?= view('boomcms::assets.search.tag') ?>
</div>


