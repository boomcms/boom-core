<form method="post" action="/cms/people/add" id="b-people-create-form" class="b-dialog-form">
	<label>
            Name
            <input type="text" name="name" />
        </label>

	<label for="create-email">
            Email
            <input type="text" id="create-email" name="email" class="boom-input" />
        </label>

	<label for="create-group">
            Group
            <?= Form::select('group_id', $groups, null) ?>
        </label>
</form>
