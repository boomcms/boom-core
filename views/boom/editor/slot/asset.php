<section>
    <h1>Current Asset</h1>
    <p>Click to select an alternative asset</p>

    <a href="#" data-asset-id="<?= $chunk->target() ?>">
        <? if ($chunk->hasContent()): ?>
            <img src="<?= Route::url('asset', array('id' => $chunk->target(), 'action' => 'thumb')) ?>" />
        <? else: ?>
            <p>None set</p>
        <? endif ?>
    </a>
</section>

<section>
    <h1>Attributes</h1>

        <label class="b-caption">
            <span>Caption</span>
            <input type="text" class="caption" value="<?= $chunk->getCaption() ?>" />
        </label>

        <label class="b-link">
            <span>Link</span>
            <input type="text" class="link" value="<?= $chunk->getLink() ?>" />
        </label>

        <label class="b-title">
            <span>Title</span>
            <input type="text" class="link" value="<?= $chunk->getTitle() ?>" />
        </label>
</section>
