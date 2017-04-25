<div class="b-assets-upload">
    <?php /*if ():*/ ?>
        <form method="post" enctype="multipart/form-data" class="b-assets-upload-form">
            <div class="b-assets-upload-container">
                <div class="b-assets-upload-info">
                    <p>
                        <?= trans('boomcms::asset.upload.info1') ?>
                        <label for="b-assets-upload-file"><?= trans('boomcms::asset.upload.info2') ?></label>
                        <?= trans('boomcms::asset.upload.info3') ?>
                    </p>

                    <p class="message"></p>

                    <div class="b-assets-upload-progress"></div>
                    <?= $button('times', 'cancel', ['class' => 'b-assets-upload-cancel']) ?>
                </div>

                <input type="file" name="b-assets-upload-files[]" id="b-assets-upload-file" multiple min="1" max="5" />
            </div>
        </form>
    <?php /*else: ?>
        <div class="b-assets-upload-error">
            <p>
                <?= trans('boomcms::asset.upload.not-writable') ?>
            </p>
        </div>
    <?php endif */ ?>
</div>
