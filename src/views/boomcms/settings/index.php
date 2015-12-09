    <?= View::make('boomcms::header', ['title' => 'Settings']) ?>

    <div id="b-settings-manager">
        <div id="b-topbar" class="b-toolbar">
            <?= $menuButton() ?>
            <?= $menu() ?>

            <?= $button('save', 'Save settings', [
                'class' => 'b-button-withtext',
                'type'  => 'submit',
                'form'  => 'b-settings-manager-form',
            ]) ?>
        </div>

        <?php if (isset($message)): ?>
            <p class="message"><?= $message ?></p>
        <?php endif ?>

        <form method="post" id="b-settings-manager-form">
            <h1><?= trans('boomcms::settings-manager._heading') ?></h1>

            <input type="hidden" name="_token" value="<?= csrf_token() ?>" />

            <table class='b-table'>
                <?php foreach (BoomCMS\Settings\Manager::options() as $option): ?>
                    <tr>
                        <td><?= $option['label'] ?></td>

                        <td>
                            <?php if ($option['type'] === 'string'): ?>
                                <input type='text' name='settings[<?= $option['name'] ?>]' value='<?= $option['value'] ?>' />
                            <?php elseif ($option['type'] === 'text'): ?>
                                <textarea name='settings[<?= $option['name'] ?>]'><?= $option['value'] ?></textarea>
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

    <?= $boomJS ?>
    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            $.boom.init();
        })(jQuery);
        //]]>
    </script>
</body>
</html>