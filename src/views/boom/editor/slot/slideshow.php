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
            <? foreach ($slides as $slide): ?>
                <? $link = $slide->getLink() ?>
                <li>
                    <label>
                        <input type="radio" value="<?= $slide->id ?>" name="slide" data-asset="<?= $slide->asset_id ?>" data-title="<?= $slide->title ?>" data-url="<?= $link->url() ?>" data-page="<?= $link->isInternal()? $link->getPage()->getId() : 0 ?>" data-caption="<?= $slide->caption ?>" data-linktext='<?= $slide->linktext ?>' />
                        <img src="<?= Route::url('asset', array('id' => $slide->asset_id)) ?>" />
                    </label>
                </li>
            <? endforeach ?>
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
                <input type="text" name="url" disabled />
                <?= new Boom\UI\Button('edit', 'Edit link', ['class' => 'small']) ?>
            </label>

            <label class="b-slideshow-linktext">
                <p>Link Text</p>
                <input type="text" name="linktext" />
            </label>

            <?= new \BoomCMS\Core\UI\Button('delete', 'Delete this slide', array('id' => 'b-slideshow-editor-current-delete', 'class' => 'b-button-withtext')) ?>
        </form>
    </section>
</div>

<div id="b-slideshow-editor-buttons">
    <?= new \BoomCMS\Core\UI\Button('delete', Lang::get('Delete slideshow'), array('id' => 'b-slideshow-editor-delete', 'class' => 'b-button-textonly')) ?>
    <?= new \BoomCMS\Core\UI\Button('add', Lang::get('Add slide'), array('id' => 'b-slideshow-editor-add', 'class' => 'b-button-withtext')) ?>
</div>