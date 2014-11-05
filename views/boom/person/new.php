<form method="post" action="/cms/people/add" id="b-people-create-form" class="b-dialog-form">
	<label for="create-name">Name</label>
	<input type="text" id="create-name" name="name" class="boom-input" />

	<label for="create-email">Email</label>
	<input type="text" id="create-email" name="email" class="boom-input" />

	<label for="create-group">Group</label>
	<?= Form::select('group_id', $groups, null, array('id' => 'create-group')) ?>
</form>