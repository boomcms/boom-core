<p><?= trans('boomcms::editor.conflict.exists') ?></p>
<p><?= trans('boomcms::editor.conflict.options') ?></p>

<div class="buttons">
    <?= $button(null, 'conflict-reload', ['id' => 'b-conflict-reload', 'class' => 'b-button-textonly']) ?>
    <?= $button(null, 'conflict-overwrite', ['id' => 'b-conflict-overwrite', 'class' => 'b-button-textonly']) ?>
    <?= $button(null, 'conflict-inspect', ['id' => 'b-conflict-inspect', 'class' => 'b-button-textonly']) ?>
</div>
