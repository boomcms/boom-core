<div class="b-chunk-html">
    <h3><?= trans('boomcms::editor.html.heading') ?></h3>
    <p><?= trans('boomcms::editor.html.info') ?></p>
    <textarea><?= htmlentities($chunk->getContent()) ?></textarea>
</div>
