<form onsubmit='return false;'>
	Name

	<input type="text" id="b-tag-name" class="boom-input boom-input-medium" name="name" value="<?=$tag->name?>" />

	Parent tag

	<?= Form::select('parent_id', $all_tags, array('id' => 'b-tag-parent')) ?>
</form>