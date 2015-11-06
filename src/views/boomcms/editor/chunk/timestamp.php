<form>
    <p><?= Lang::get('boomcms::editor.timestamp.intro') ?></p>

    <label>
        <?= Lang::get('boomcms::editor.timestamp.format') ?>

        <select name="format" id="format">
            <?php foreach (BoomCMS\Core\Chunk\Timestamp::$formats as $format): ?>
              <option value="<?= $format ?>"<?php if ($format == $chunk->getFormat()): ?> selected="selected"<?php endif ?>><?= date($format, time()) ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <label>
        <?= Lang::get('boomcms::editor.timestamp.value') ?>

        <input id="timestamp" type="text" name="timestamp" class="boom-datepicker" value="<?= $chunk->getTimestamp() ? date('d F Y H:i', $chunk->getTimestamp()) : 'Select a date and time' ?>" />
    </label>
</form>
