<?= View::make('boom::header', ['title' => 'Profile']) ?>
    <div id="b-topbar" class="b-toolbar">
        <?= $menuButton() ?>
        <?= $menu() ?>
    </div>

    <div id="b-account-profile">
        <p class="information">
            You can use this form to update your account details. If you leave the password fields blank your password will not be changed.
        </p>

        <div style="margin-top: 20px;">
            <?php if (isset($message)): ?>
                <p class="message"><?= $message ?></p>
            <?php endif ?>

            <form method="post" action="/cms/profile">
                <label>
                    Name

                    <input type="text" name="name" size="35" value="<?= $person->getName() ?>" />
                </label>

                <label>
                    Current password

                    <input type="password" name="current_password" size="35" />
                </label>

                <label>
                    New password

                    <input type="password" name="new_password" size="35" />
                </label>

                <input type="submit" value="Submit" />
            </form>

            <table id="b-people-profile-authlog">
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log->time ?></td>
                        <td><?= $log->get_action() ?></td>
                        <td><?= ucfirst($log->method) ?></td>
                        <td><?= long2ip($log->ip) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>

    <script type="text/javascript">
        //<![CDATA[
        (function ($) {
            $.boom.init();
        })(jQuery);
        //]]>
    </script>
</body>
</html>
