<form onsubmit='return false;'>
	<label for="b-tag-name">Name

	<input type="text" id="b-tag-name" class="boom-input boom-input-medium" name="name" value="<?=$tag->name?>" /></label>

	<label for="b-tag-parent">Parent tag

	<?= Form::select('parent_id', $all_tags, $tag->parent_id, array('id' => 'b-tag-parent')) ?></label>
</form>