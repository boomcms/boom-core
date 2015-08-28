    <?= View::make('boom::header', ['title' => 'Settings']) ?>

    <form id="b-settings-manager" method='post'>
        <div id="b-topbar" class="b-toolbar">
            <?= $menuButton() ?>
            <?= $menu() ?>

            <?= $button('save', 'Submit', ['b-button-withtext', 'type' => 'submit']) ?>
        </div>

        <table class='b-table'>
            <?php foreach (Config::get('boomcms.settingsManagerOptions') as $name => $attrs): ?>
                <tr>
                    <td><?= $attrs['label'] ?></td>
                    <td>
                        <?php if ($attrs['type'] === 'string'): ?>
                            <input type='text' name='<?= $name ?>' value='<?= Settings::get($name) ?>' />
                        <?php elseif ($attrs['type'] === 'text'): ?>
                            <textarea name='<?= $name ?>'><?= Settings::get($name) ?></textarea>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </form>

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