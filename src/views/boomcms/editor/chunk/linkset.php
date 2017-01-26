<label id="b-linkset-title" class="toggle-titile">
    <span><?= trans('boomcms::editor.chunk.linkset.linkset-title') ?></span>
    <input type="text" name="title" placeholder="Linkset title" value="<?= $chunk->getTitle() ?>" />
</label>

<div class="container">
    <section>
        <div id="b-linkset-links">
            <h1><?= trans('boomcms::editor.chunk.linkset.all-links') ?></h1>

            <?php if (count($chunk->getLinks())): ?>
            <p><?= trans('boomcms::editor.chunk.linkset.reorder') ?></p>

                <ul>
                    <?php foreach ($chunk->getLinks() as $link): ?>
                        <li><a class="b-linkset-link" href='#' data-text="<?= $link->getTextAttribute() ?>" data-page-id="<?= $link->getTargetPageId() ?>" data-url="<?= $link->url() ?>" data-title="<?= $link->getTitleAttribute() ?>" data-asset="<?= $link->getAssetId() ?>"><?= $link->getTitle() ?></a></li>
                    <?php endforeach ?>
                </ul>
            <?php else: ?>
                <ul></ul>

                <p class="none"><?= trans('boomcms::editor.chunk.linkset.no-links') ?></p>
            <?php endif ?>
        </div>
    </section>

    <section id="b-linkset-current">
        <h1><?= trans('boomcms::editor.chunk.linkset.current.heading') ?></h1>

        <p class="default"><?= trans('boomcms::editor.chunk.linkset.current.none-selected') ?></p>
        <p class="default"><?= trans('boomcms::editor.chunk.linkset.current.select-one') ?></p>

        <form>
            <label class="b-linkset-target">
                <p><?= trans('boomcms::editor.chunk.linkset.current.target') ?></p>
                <input type="text" name="target" value="" />

                <?= $button('edit', 'linkset-edit-target', ['class' => 'small']) ?>
            </label>

            <div>
                <h2><?= trans('boomcms::editor.chunk.linkset.current.optional') ?></h2>
                <p><?= trans('boomcms::editor.chunk.linkset.current.optional-desc') ?></p>

                <label class="b-linkset-title">
                    <p><?= trans('boomcms::editor.chunk.linkset.current.title') ?></p>
                    <input type="text" name="title" value="" />
                </label>

                <label class="b-linkset-text">
                    <p><?= trans('boomcms::editor.chunk.linkset.current.text') ?></p>
                    <input type="text" name="text" value="" />
                </label>

                <label class="b-linkset-asset">
                    <p><?= trans('boomcms::editor.chunk.linkset.current.asset') ?></p>
                    <?= $button('edit', 'linkset-edit-asset', ['class' => 'small']) ?>

                    <input type="hidden" name="asset" value="" />
                    <img src="" />
                    <p class="none"><?= trans('boomcms::editor.chunk.linkset.current.no-asset') ?></p>
                </label>
            </div>
        </form>
    </section>
</div>

<div id="b-linkset-editor-buttons">
	<?= $button('trash-o', 'delete-linkset', ['id' => 'b-linkset-delete', 'class' => 'b-button-textonly']) ?>
	<?= $button('plus', 'Add link', ['id' => 'b-linkset-add', 'class' => 'b-button-withtext']) ?>
</div>
