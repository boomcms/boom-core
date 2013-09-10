<p class="information">
	You can use this form to update your account details. If you leave the password fields blank your password will not be changed.
</p>

<a href="#" class="b-people-edit-avatar">
	<img data-asset-id="<?= $person->get_avatar()->id ?>" src="<?= $person->get_icon_url(60) ?>" />
</a>

<div style="margin-top: 20px;">
	<form id="b-people-profile" onsubmit='return false;' class="b-form">
		<label>
			Name
			<?= Form::input('name', $person->name, array('size' => 35)) ?>
		</label>

		<? if ($auth->login_method_available('password')): ?>
			<label>
				Current password
				<?= Form::password('current_password','', array('size' => 35)) ?>
			</label>

			<label>
				New password
				<?= Form::password('new_password','', array('size' => 35)) ?>
			</label>
		<? endif; ?>

		<?= Form::submit('submit', 'Submit') ?>
	</form>

	<table id="b-people-profile-authlog">
		<? foreach ($logs as $log): ?>
			<tr>
				<td><?= Date::fuzzy_span($log->time) ?></td>
				<td><?= $log->get_action() ?></td>
				<td><?= ucfirst($log->method) ?></td>
				<td><?= long2ip($log->ip) ?></td>
			</tr>
		<? endforeach ?>
	</table>
</div>