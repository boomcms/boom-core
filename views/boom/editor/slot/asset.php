<section>
    <h1>Current Asset</h1>
    <p>Click to select an alternative asset</p>

    <a href="#" data-asset-id="<?= $chunk->target() ?>">
        <? if ($chunk->hasContent()): ?>
            <img src="<?= Route::url('asset', array('id' => $chunk->target(), 'action' => 'thumb', 'width' => 400)) ?>" />
        <? else: ?>
            <p>None set</p>
        <? endif ?>
    </a>
</section>

<section>
    <h1>Attributes</h1>
        <label class="b-title">
            <span>Title</span>
            <textarea><?= $chunk->getTitle() ?></textarea>
        </label>

        <label class="b-caption">
            <span>Caption</span>
            <textarea><?= $chunk->getCaption() ?></textarea>
        </label>

        <label class="b-link">
            <span>Link</span>
            <input type="text" disabled value="<?= $chunk->getLink() ?>" />
            <?= new Boom\UI\Button('edit', 'Edit link', ['class' => 'small']) ?>
        </label>
</section>
