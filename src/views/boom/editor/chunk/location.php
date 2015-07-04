<section>
    <h1>Location details</h1>

    <form>
        <label class="b-title">
            <span>Title</span>
            <input type="text" name="title" value="<?= $chunk->getTitle() ?>" />
        </label>

        <label class="b-address">
            <span>Address</span>
            <textarea name="address"><?= $chunk->getAddress() ?></textarea>
        </label>

        <label class="b-postcode">
            <span>Postcode</span>
            <input type="text" name="postcode" value="<?= $chunk->getPostcode() ?>" />
        </label>
    </form>
</section>

<section>
    <h1>Map</h1>
    <p>Set the location by moving the pin on the map</p>

    <div class="b-map">

    </div>
</section>
