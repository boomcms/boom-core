<?= View::make('boom/header', ['title' => 'Profile']) ?>
    <div id="b-topbar" class="b-toolbar">
        <?= new \Boom\UI\MenuButton() ?>
        <?= new \Boom\Menu\Menu  ?>
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
                    <?= Form::input('name', $person->getName(), ['size' => 35]) ?>
                </label>

                <label>
                    Current password
                    <?= Form::password('current_password','', ['size' => 35]) ?>
                </label>

                <label>
                    New password
                    <?= Form::password('new_password','', ['size' => 35]) ?>
                </label>

                <?= Form::submit('submit', 'Submit') ?>
            </form>

            <table id="b-people-profile-authlog">
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= Date::fuzzy_span($log->time) ?></td>
                        <td><?= $log->get_action() ?></td>
                        <td><?= ucfirst($log->method) ?></td>
                        <td><?= long2ip($log->ip) ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>

	<?= Boom::include_js() ?>
	<script type="text/javascript">
		//<![CDATA[
		(function ($) {
			$.boom.init({
				csrf: '<?= Security::token() ?>'
			});
		})(jQuery);
		//]]>
	</script>
</body>
</html>
