<div id='b-page-urls' class="b-pagesettings">
    <section>
        <h1><?= trans('boomcms::urls.heading') ?></h1>

        <ul class='b-page-urls-help'>
            <li><?= trans('boomcms::urls.help.primary') ?></li>
            <li><?= trans('boomcms::urls.help.one-primary') ?></li>
            <li><?= trans('boomcms::urls.help.redirect') ?></li>
            <li><?= trans('boomcms::urls.help.make-primary') ?></li>
            <li><?= trans('boomcms::urls.help.no-edit') ?></li>
        </ul>

        <ul id='b-page-urls-list'>
            <?php foreach ($urls as $url): ?>
                <li data-url="<?= $url->getLocation() ?>" data-id="<?= $url->getId() ?>" <?php if ((bool) $url->isPrimary()): echo 'class="b-page-urls-primary"'; endif ?>>
                    <span class='b-page-urls-primary-indicator'>primary</span>
                    <span title="Remove URL" class="fa fa-trash-o b-urls-remove"></span>
                    <label class="primary" for="is_primary_<?= $url->getId() ?>"><?= $url->getLocation() ?></label>
                    <input type="radio" name="is_primary" value="<?= $url->getLocation() ?>" id="is_primary_<?= $url->getId() ?>" class="ui-helper-hidden b-urls-primary" <?php if ($url->isPrimary()): ?> checked="checked"<?php endif ?>/>
                </li>
            <?php endforeach ?>
        </ul>
    </section>

    <section>
        <h1><?= trans('boomcms::urls.add.heading') ?></h1>

        <ul>
            <li><?= trans('boomcms::urls.add.lowercase') ?></li>
            <li><?= trans('boomcms::urls.add.nospaces') ?></li>
            <li><?= trans('boomcms::urls.add.hyphens') ?></li>
            <li><?= trans('boomcms::urls.add.nosurprises') ?></li>
            <li><?= trans('boomcms::urls.add.keywords') ?></li>
        </ul>

        <form>
            <label for="url">
                <?= trans('boomcms::urls.add.new') ?>
            </label>

            <p>
                <input type='text' name='url' id='url' placeholder='<?= trans('boomcms::urls.add.placeholder') ?>' />

                <button type="submit">
                    <span class="fa fa-plus"></span>
                </button>
            </p>
        </form>
    </section>
</div>
