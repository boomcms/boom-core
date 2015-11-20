<section id="b-page-relations">
    <h1><?= Lang::get('boomcms::settings.relations.heading') ?></h1>

    <?= Lang::get('boomcms::settings.relations.intro') ?>

    <h2 class="current" style="display: none">
        <?= Lang::get('boomcms::settings.relations.current') ?>
    </h2>

    <ul id="b-page-relations"></ul>

    <?= $button('plus', Lang::get('boomcms::settings.relations.add'), ['id' => 'b-tags-addpage', 'class' => 'b-button-withtext']) ?>
</section>