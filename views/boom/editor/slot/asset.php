<div class="container">
    <section>
        <h1>Current Asset</h1>

        <? if ($chunk->hasContent()): ?>
            <a href="#">
                <img src="<?= Route::url('asset', array('id' => $chunk->target(), 'action' => 'thumb')) ?>" />
            </a>
        <? else: ?>
            <p>
                None set
            </p>
        <? endif ?>
    </section>

    <section>
        <h1>Attributes</h1>

        <? if ($chunk->getCaption()): ?>
            <label>
                Caption

                <input type="text" class="caption" value="<?= $chunk->getCaption() ?>" />
            </label>
        <? endif ?>

        <? if ($chunk->getLink()): ?>
            <label>
                Link

                <input type="text" class="link" value="<?= $chunk->getLink()->getTitle() ?>" />
            </label>
        <? endif ?>
    </section>
</div>