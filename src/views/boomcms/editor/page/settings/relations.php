<section id="b-page-relations">
    <h1><?= trans('boomcms::settings.relations.heading') ?></h1>

    <?= trans('boomcms::settings.relations.intro') ?>

    <h2 class="current" style="display: none">
        <?= trans('boomcms::settings.relations.current') ?>
    </h2>

    <ul id="b-page-relations"></ul>

    <?= $button('plus', trans('boomcms::settings.relations.add'), ['id' => 'b-tags-addpage', 'class' => 'b-button-withtext']) ?>
</section>