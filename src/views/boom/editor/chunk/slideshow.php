<div class='container'>
    <section>
        <h1>All Slides</h1>
        <p>
            All of the slideshow's slides are shown below.
        </p>
        <ul>
            <li>Drag slides to re-order them.</li>
            <li>Click on an image to edit or delete the slide</li>
            <li>Click on the + button below to add a new slide</li>
        </ul>

        <ol id="b-slideshow-editor-slides">
            <?php foreach ($chunk->getSlides() as $slide): ?>
                <?php $link = $slide->getLink() ?>
                <li>
                    <label>
                        <input type="radio" value="<?= $slide->getId() ?>" name="slide" data-asset="<?= $slide->getAssetId() ?>" data-title="<?= $slide->getTitle() ?>" data-url="<?= $link->url() ?>" data-page="<?= $link->isInternal() ? $link->getPage()->getId() : 0 ?>" data-caption="<?= $slide->getCaption() ?>" data-linktext='<?= $slide->getLinkText() ?>' />
                        <img src="<?= $assetURL(['id' => $slide->getAssetId()]) ?>" />
                    </label>
                </li>
            <?php endforeach ?>
        </ol>
    </section>

    <section id="b-slideshow-editor-current">
        <h1>Current slide</h1>
        <p class='default'>
            No slide selected.
        </p>
        <p class='default'>
            Click on a slide to the left or add a new slide to edit it here.
        </p>
        <form>
            <p>Click on the slide image to change the image</p>
            <a href="#"><img src="" /></a>

            <label class="b-slideshow-title">
                <p>Title</p>
                <input type="text" name="title" />
            </label>

            <label class="b-slideshow-caption">
                <p>Caption</p>
                <input type="text" name="caption" />
            </label>

            <label class="b-slideshow-link">
                <p>Link</p>
                <?= $button('edit', 'Edit link', ['class' => 'small']) ?>
                <input type="text" name="url" disabled />
            </label>

            <label class="b-slideshow-linktext">
                <p>Link Text</p>
                <input type="text" name="linktext" />
            </label>

            <?= $button('trash-o', 'Delete this slide', ['id' => 'b-slideshow-editor-current-delete', 'class' => 'b-button-withtext']) ?>
        </form>
    </section>
</div>

<div id="b-slideshow-editor-buttons">
    <?= $button('trash-o', Lang::get('Delete slideshow'), ['id' => 'b-slideshow-editor-delete', 'class' => 'b-button-textonly']) ?>
    <?= $button('plus', Lang::get('Add slide'), ['id' => 'b-slideshow-editor-add', 'class' => 'b-button-withtext']) ?>
</div>
