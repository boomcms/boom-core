<form class="b-form-settings">
        <p>
            Set a time when the draft changes to the page will become live.
        </p>

        <p>
            <strong>Note: this will set a time for the page's edits to become live. If you want to embargo the entire page you should use the visibility settings.</strong>
        </p>

        <label>
            <?= Lang::get('Embargo until') ?>
            <input type="text" name="embargoed_until" value="<?= ($version->getEmbargoedUntil()->getTimestamp()) ? $version->getEmbargoedUntil()->format('d F Y h:i') : date('d F Y h:i', time()) ?>" class="boom-datepicker" id="page-embargo" />
        </label>
</form>
