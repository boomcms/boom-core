<form class="b-form">
    <p>Select a date / time and format below.</p>

    <label>
        Format
        <select name="format" id="format">
            <?php foreach (BoomCMS\Core\Chunk\Timestamp::$formats as $format): ?>
              <option value="<?= $format ?>"<?php if ($format == $chunk->getFormat()): ?> selected="selected"<?php endif ?>><?= date($format, time()) ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label>
        Date / time
        <input id="timestamp" type="text" name="timestamp" class="boom-input boom-datepicker" value="<?= $chunk->getTimestamp() ? date('d F Y H:i', $chunk->getTimestamp()) : 'Select a date and time' ?>" />
    </label>
</form>
