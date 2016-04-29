<div class="b-chunk-calendar">
    <p><?= trans('boomcms::editor.calendar.info') ?></p>

    <div class="calendar"></div>

    <div class="content">
        <textarea name="text"></textarea>

        <?= $button('trash-o', 'remove-date', ['class' => 'remove b-button-withtext']) ?>
    </div>

    <textarea name="dates"><?= json_encode($chunk->getDates()) ?></textarea>
</div>
