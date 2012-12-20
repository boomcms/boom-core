<select id='b-permissions-add-action' name='action' multiple='multiple' size='10'>
	<? foreach ($roles as $role): ?>
		<option value='<?=$role->id?>'><?= $role->description ?></option>
	<? endforeach ?>
</select>
<br />
<select id='b-permissions-add-value' name='value'>
	<option value='1'>Allow</option>
	<option value='0'>Deny</option>
</select>
