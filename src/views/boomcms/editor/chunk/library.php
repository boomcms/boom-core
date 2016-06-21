<div class="b-chunk-library">
    <h1><?= trans('boomcms::editor.chunk.library.heading') ?></h1>
    <p><?= trans('boomcms::editor.chunk.library.about') ?></p>

    <section>
        <?= view('boomcms::assets.search.type', ['selected' => $chunk->getParam('type')]) ?>
    </section>

    <section id="b-tags-search">
        <?= view('boomcms::assets.search.tag', ['tags' => $chunk->getTags()]) ?>
    </section>

    <section>
        <?= view('boomcms::assets.search.sort', ['selected' => $chunk->getOrder()]) ?>
    </section>

    <section>
        <h2><?= trans('boomcms::editor.chunk.library.limit') ?></h2>
        <p><?= trans('boomcms::editor.chunk.library.limit_about') ?></p>

        <input type="text" name="limit" value="<?= $chunk->getLimit() ?>">
    </section>
</div>

<div class="buttons">
    <?= $button('trash-o', 'clear-filters', ['class' => 'clear b-button-withtext']) ?>
</div>
