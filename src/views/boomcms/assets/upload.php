<form method="post" enctype="multipart/form-data" class="b-assets-upload">
    <div class='info'>
        <label for="b-assets-upload-file"><?= trans('boomcms::asset.upload.select-files') ?></label>
        <p><?= trans('boomcms::asset.upload.drag-drop') ?></p>
    </div>

    <div class='errors'>
        <h2><?= trans('boomcms::asset.upload.errors') ?></h2>

        <ul></ul>
    </div>

    <p class='failed'><?= trans('boomcms::asset.upload.failed') ?></p>
    <div class="progress"></div>
    <?= $button('times', 'cancel', ['class' => 'cancel']) ?>

    <input type="file" name="b-assets-upload-files[]" id="b-assets-upload-file" multiple min="1">
</form>
