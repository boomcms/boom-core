<form rel='<?= $group->id ?>' onsubmit='return false;'>
	<label for="b-people-group-name"><?= __('Name') ?></label>
	<input type="text" id="b-people-group-name" class="boom-input boom-input-medium" name="name" value="<?=$group->name?>" />
</form>