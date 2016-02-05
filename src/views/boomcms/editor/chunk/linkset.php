<div class="container">
    <section>
        <label id="b-linkset-title">
                <p>Linkset title</p>
                <input type="text" name="title" value="<?= $chunk->getTitle() ?>" />
        </label>

        <div id="b-linkset-links">
            <h1>All links</h1>

            <ul>
                <?php foreach ($chunk->getLinks() as $link): ?>
                    <li><a class="b-linkset-link" href='#' data-page-id="<?= $link->getTargetPageId() ?>" data-url="<?= $link->getUrl() ?>" data-title="<?= $link->getTitle() ?>" data-asset="<?= $link->getAssetId() ?>"><?= $link->getTitle() ?></a></li>
                <?php endforeach ?>
            </ul>

            <?php if (!count($chunk->getLinks())): ?>
                <p class="none">This linkset does not contain any links.</p>
            <?php endif ?>
        </div>
    </section>

    <section id="b-linkset-current">
        <h1>Current link</h1>

        <p class="default">No link selected.</p>
        <p class="default">Click on a link to the left or add a new link to edit it here.</p>
        <p class="default">Drag links to re-order them.</p>

        <form>
            <label class="b-linkset-target">
                <p>Target</p>
                <input type="text" name="target" disabled value="" />
                <?= $button('edit', 'Edit link target', ['class' => 'small']) ?>
            </label>

            <label class="b-linkset-title">
                <p>Text</p>
                <input type="text" name="title" value="" />
            </label>

            <label class="b-linkset-asset">
                <p>Asset</p>
                <input type="hidden" name="asset" value="" />

                <a href="#" class="set"><img src="" /></a>
                <p class="none">None set. <a href="#">Add an associated asset</a></p>
            </label>
        </form>
    </section>
</div>

<div id="b-linkset-editor-buttons">
	<?= $button('trash-o', trans('Delete linkset'), ['id' => 'b-linkset-delete', 'class' => 'b-button-textonly']) ?>
	<?= $button('plus', 'Add link', ['id' => 'b-linkset-add', 'class' => 'b-button-withtext']) ?>
</div>
