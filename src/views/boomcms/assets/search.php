<form action="#">
    <?= view('boomcms::assets.search.text') ?>
    <?= view('boomcms::assets.search.type') ?>
    <?= view('boomcms::assets.search.extension') ?>
    <?= view('boomcms::assets.search.uploaded-by') ?>
    <?= view('boomcms::assets.search.sort') ?>

    <?= $button('search', 'search-assets', ['class' => 'b-button-textonly', 'type' => 'submit']) ?>
</form>
