    <?= View::make('boom::header', ['title' => 'Settings']) ?>

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
            <input type="hidden" name="_token" value="<?= csrf_token() ?>" />

            <table class='b-table'>
                <?php foreach (Config::get('boomcms.settingsManagerOptions') as $name => $type): ?>
                    <tr>
                        <td><?= Lang::get("boom::settings-manager.$name._label") ?></td>

                        <td>
                            <?php if ($type === 'string'): ?>
                                <input type='text' name='<?= $name ?>' value='<?= Settings::get($name) ?>' />
                            <?php elseif ($type === 'text'): ?>
                                <textarea name='<?= $name ?>'><?= Settings::get($name) ?></textarea>
                            <?php endif ?>
                        </td>

                        <td>
                            <?php if (Lang::has("boom::settings-manager.$name._info")): ?>
                                <?= Lang::get("boom::settings-manager.$name._info") ?>
                            <?php endif ?>
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