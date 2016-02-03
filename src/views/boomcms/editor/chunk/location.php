<div>
    <section id="b-location-details">
        <h1><?= trans('boomcms::editor.chunk.location.details') ?></h1>

        <form>
            <label class="b-title">
                <span><?= trans('boomcms::editor.chunk.location.title') ?></span>
                <input type="text" name="title" value="<?= $chunk->getTitle() ?>" />
            </label>

            <label class="b-address">
                <span><?= trans('boomcms::editor.chunk.location.address') ?></span>
                <textarea name="address"><?= str_replace('<br />', '', $chunk->getAddress()) ?></textarea>
            </label>
        </form>
    </section>

    <section>
        <h1><?= trans('boomcms::editor.chunk.location.set-location') ?></h1>

        <label class="b-postcode">
            <span><?= trans('boomcms::editor.chunk.location.postcode') ?></span>
            <input type="text" name="postcode" value="<?= $chunk->getPostcode() ?>" />
        </label>

        <?= $button('globe', 'set-location-postcode', ['id' => 'b-location-set', 'class' => 'b-button-withtext']) ?>

        <label class="b-lat">
            <span><?= trans('boomcms::editor.chunk.location.lat') ?></span>
            <input type="text" name="lat" value="<?= $chunk->getLat() ?>" />
        </label>

        <label class="b-lng">
            <span><?= trans('boomcms::editor.chunk.location.lng') ?></span>
            <input type="text" name="lng" value="<?= $chunk->getLng() ?>" />
        </label>

        <?= $button('globe', 'set-location-latlng', ['id' => 'b-location-latlng', 'class' => 'b-button-withtext']) ?>
    </section>
</div>

<section>
    <h1><?= trans('boomcms::editor.chunk.location.map') ?></h1>
    <p><?= trans('boomcms::editor.chunk.location.map-desc') ?></p>

    <div id="b-location-map" data-lat="<?= $chunk->getLat() ?>" data-lng="<?= $chunk->getLng() ?>">

    </div>

    <?= $button('trash-o', 'remove-location', ['id' => 'b-location-remove', 'class' => 'b-button-withtext']) ?>
</section>
