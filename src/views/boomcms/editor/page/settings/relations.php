<section id="b-page-relations">
    <h1><?= Lang::get('boomcms::settings.relations.heading') ?></h1>

    <?= Lang::get('boomcms::settings.relations.intro') ?>

    <h2 class="current"<?php if (!count($relatedPages)): ?> style="display: none"<?php endif ?>>
        <?= Lang::get('boomcms::settings.relations.current') ?>
    </h2>

    <ul id="b-page-relations">
        <?php foreach ($relatedPages as $p): ?>
            <li>
                <span class="title"><?= $p->getTitle() ?></span>
                <span class="uri"><?= $p->url()->getLocation() ?></span>

                <a href="#" data-page-id="<?= $p->getId() ?>" class="fa fa-trash-o"><span>Remove</span></a>
            </li>
        <?php endforeach ?>
    </ul>

    <?= $button('plus', Lang::get('boomcms::settings.relations.add'), ['id' => 'b-tags-addpage', 'class' => 'b-button-withtext']) ?>
</section>