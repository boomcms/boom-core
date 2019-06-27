<?= view('boomcms::header', ['title' => 'Settings']) ?>

<?php 
    $languages = Config::get('boomcms.languages');
    $options = BoomCMS\Settings\Manager::options(); 
?>

<div id="b-topbar" class="b-toolbar">
    <?= $menuButton() ?>
    <?= $menu() ?>

    <?= $button('save', 'Save settings', [
        'class' => 'b-button-withtext',
        'type'  => 'submit',
        'form'  => 'b-settings-manager-form',
    ]) ?>
</div>

<main id="b-container">
    <div id="b-settings-manager">
        <?php if (isset($message)): ?>
            <p class="message"><?= $message ?></p>
        <?php endif ?>

        <form method="post" id="b-settings-manager-form">
            <h1><?= trans('boomcms::settings-manager._heading') ?></h1>

            <input type="hidden" name="_token" value="<?= csrf_token() ?>" />

            <table class='b-table'>
                <?php foreach ($options as $option): ?>
                    <tr>
                        <td><?= $option['label'] ?></td>

                        <td>
                            <?php if ($option['type'] === 'string'): ?>
                                <input type='text' name='settings[<?= $option['name'] ?>]' value='<?= $option['value'] ?>' />
                            <?php elseif ($option['type'] === 'text'): ?>
                                <textarea name='settings[<?= $option['name'] ?>]'><?= $option['value'] ?></textarea>
                            <?php elseif ($option['type'] === 'language'): ?>
                                <input type='hidden' name='settings[<?= $option['name'] ?>][en]' value=1 /> 
                                <?php foreach($languages as $code => $language) { ?>
                                    <?php $value = (array)$option['value']; ?>
                                    <label class="language-option"> 
                                        <input type='checkbox' <?= (isset($value[$code]))? 'checked' : '' ?> name='settings[<?= $option['name'] ?>][<?= $code ?>]' value=1 /> <img src="/vendor/boomcms/boom-core/flags/<?= $code ?>.png" alt="<?= $language ?>" title="<?= $language ?>">
                                    </label>
                                <?php } ?>
                            <?php endif ?>
                        </td>

                        <td>
                            <?= $option['info'] ?>
                        </td>
                    </tr>
                <?php endforeach ?>

            </table>
        </form>
    </div>
</main>

<?= view('boomcms::footer') ?>
