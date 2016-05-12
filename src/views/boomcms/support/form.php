<form id="b-support-form">
    <input type="hidden" name="browser" />
    <input type="hidden" name="viewport-width" />
    <input type="hidden" name="viewport-height" />
    <input type="hidden" name="location" />

    <label>
        <p><?= trans('boomcms::support.form.subject') ?></p>
        <input type="text" name="subject" required />
    </label>


    <label>
        <p><?= trans('boomcms::support.form.message') ?></p>
        <textarea name="message" required></textarea>
    </label>
</form>
