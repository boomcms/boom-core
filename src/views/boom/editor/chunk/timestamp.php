<form class="b-form">
    <p>Select a date / time and format below.</p>

    <label>
        Format
        <select name="format" id="format">
            <?php foreach ($formats as $f => $example): ?>
              <option value="<?= $f ?>"<?php if ($f == $format): ?> selected="selected"<?php endif ?>><?= $example ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label>
        Date / time
        <input id="timestamp" type="text" name="timestamp" class="boom-input boom-datepicker" value="<?= date("d F Y H:i", $timestamp) ?>" />
    </label>
</form>
