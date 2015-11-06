<section>
    <h1>Location details</h1>

    <form>
        <label class="b-title">
            <span>Title</span>
            <input type="text" name="title" value="<?= $chunk->getTitle() ?>" />
        </label>

        <label class="b-address">
            <span>Address</span>
            <textarea name="address"><?= str_replace('<br />', '', $chunk->getAddress()) ?></textarea>
        </label>

        <label class="b-postcode">
            <span>Postcode</span>
            <input type="text" name="postcode" value="<?= $chunk->getPostcode() ?>" />
        </label>

        <?= $button('globe', 'Set map location from address', ['id' => 'b-location-set', 'class' => 'b-button-withtext']) ?>
    </form>
</section>

<section>
    <h1>Map</h1>
    <p>Set the location by moving the pin on the map</p>

    <div id="b-location-map" data-lat="<?= $chunk->getLat() ?>" data-lng="<?= $chunk->getLng() ?>">

    </div>

    <?= $button('trash-o', 'Remove location', ['id' => 'b-location-remove', 'class' => 'b-button-withtext']) ?>
</section>
