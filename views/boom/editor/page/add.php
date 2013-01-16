<form id="b-page-add-form">

	<label for="parent_id">
		<?=__('Parent page')?>
	

	<select name="parent_id">
		<option value="0"><?=__('No parent')?></option>
		<?
		foreach($page->mptt->fulltree() as $node):
			echo "<option value='", $node->id, "'";

			if ($node->id == $page->id)
				echo " selected='selected'";

			echo ">", $node->page->version()->title, "</option>";
		endforeach;
		?>
		</option>
	</select>
	</label>
	
<label for="template_id"><?=__('Template')?>
<?= Form::select('template_id', $templates, $default_template, array('style' => 'width: 24em')); ?>
</label>
</form>