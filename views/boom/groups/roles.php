<table class='b-group-roles'>
	<thead>
		<th>Role</th>
		<th>Allow</th>
		<th>Deny</th>
		<th>Not set</th>
	</thead>
	<tbody>
		<? foreach ($roles as $role): ?>
			<tr data-id="<?= $role->id ?>">
				<td><?= $role->description ?></td>
				<td><?= Form::radio($role->id, 1, false, array( 'id' => 'allow-' . $role->id )); ?><label for="allow-<?= $role->id ?>">Allow</label></td>
				<td><?= Form::radio($role->id, 0, false, array( 'id' => 'deny-' . $role->id )); ?><label for="deny-<?= $role->id ?>">Deny</label></td>
				<td><?= Form::radio($role->id, -1, true, array( 'id' => 'none-' . $role->id )); ?><label for="none-<?= $role->id ?>">Not set</label></td>
			</tr>
		<? endforeach; ?>
	</tbody>
</table>