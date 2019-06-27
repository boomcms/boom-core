
<?php $languages = (array)BoomCMS\Settings\Manager::setLanguages(); ?>

<section id="b-page-relatedlangpages">
    <h1><?= trans('boomcms::settings.relatedlangpages.heading') ?></h1>

    <?php if(count($languages)>1 && isset($languages['en'])) { ?>

    <?= trans('boomcms::settings.relatedlangpages.intro') ?>

    <h2 class="current"><?= trans('boomcms::settings.relatedlangpages.current') ?><span id="current-page-title"></span></h2>
    <h3><?= trans('boomcms::settings.relatedlangpages.related-pages') ?></h3>

    <p class='b-error' id="error-message"></p>
    
    <table class="b-table" id="language">
        <thead>
            <tr>
                <th width="15%"><?= trans('boomcms::settings.relatedlangpages.language') ?></th>
                <th width="85%"><?= trans('boomcms::settings.relatedlangpages.related-page') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($languages as $language => $value) { ?>

            <tr id="<?= $language ?>">
                <td><img src="/vendor/boomcms/boom-core/flags/<?= $language ?>.png" alt="<?= config('boomcms::settings.language.'.$language) ?>" title="<?= config('boomcms::settings.language.'.$language) ?>"> </td>
                <td class="lang"><div id="<?= $language ?>-page" <?= ($language == 'en')? "data-default-page-id=''" : ""?>></div><?= $button('plus', 'add-related-lang-page', ['id' => $language.'-lang-button', 'class' => 'b-lang-addpage b-button-withtext', 'data-lang' => $language]) ?></td>
            </tr>

        <?php } ?>
        </tbody>
    </table>
</div>

    <?php
} else { ?>

<p><?= trans('boomcms::settings.relatedlangpages.settings-message') ?> <a href="/boomcms/settings" target="_blank">settings</a></p>

<?php } ?>


</section>


