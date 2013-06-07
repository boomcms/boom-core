<p class="information">
	You can use this form to update your account details. If you leave the password fields blank your password will not be changed.
</p>

<img src="<?= $person->get_icon_url(48) ?>" />

<div style="margin-top: 20px;">
	<form id="b-people-profile" onsubmit='return false;'>
		<label>
			Name
			<?= Form::input('name', $person->name, array('size' => 35)) ?>
		</label>

		<label>
			Current password
			<?= Form::password('current_password','', array('size' => 35)) ?>
		</label>

		<label>
			New password
			<?= Form::password('new_password','', array('size' => 35)) ?>
		</label>

		<?= Form::submit('submit', 'Submit') ?>
	</form>
</div>