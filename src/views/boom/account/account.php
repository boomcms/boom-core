<?= View::make('boom::header', ['title' => 'Manage Account']) ?>

    <div id="b-topbar" class="b-toolbar">
        <?= $menuButton() ?>
        <?= $menu() ?>
    </div>

    <div id="b-account">
        <p class="information">
            You can use this form to update your account details. If you leave the password fields blank your password will not be changed.
        </p>

        <div style="margin-top: 20px;">
            <?php if (isset($message)): ?>
                <p class="message"><?= $message ?></p>
            <?php endif ?>

            <form method="post" action="/cms/account">
				<input type="hidden" name="_token" value="<?= csrf_token() ?>" />

				<p>
					<label for="name">Name</label>
					<input id="name" type="text" name="name" value="<?= $person->getName() ?>" />
				</p>

				<p>
					<label for="current_password">Current password</label>
					<input id="current_password" type="password" name="current_password" />
				</p>

				<p>
					<label for="password1">New password</label>
					<input id="password1" type="password" name="password1" />
				</p>

				<p>
					<label for="password2">Confirm new password</label>
					<input id="password2" type="password" name="password2" />
				</p>

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
