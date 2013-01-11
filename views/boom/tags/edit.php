<form onsubmit='return false;'>
	<?= Form::hidden('csrf', Security::token()) ?>
	<?= Form::hidden('type', $type) ?>
	<div class="boom-tabs">
		<ul>
			<li><a href="#edit-folder-basic">Basic</a></li>
		</ul>
		<div id="edit-folder-basic">
			Name
			
			<input type="text" id="boom-tagmanager-tag-edit-name" class="boom-input boom-input-medium" name="name" value="<?=$tag->name?>" />
			
			Parent tag
			
			<select id="boom-tagmanager-tag-edit-parent" name="parent_id">
			<?
				echo "<option value=''>None</option>";

				foreach(ORM::factory('Tag')->where('type', '=', $type)->find_all() as $t):
					echo "<option";

					if ($tag->parent_id AND $tag->parent_id == $t->parent_id)
					{
						echo " selected='selected'";
					}

					echo " value='", $t->id, "'>", $t->name, "</option>";
				endforeach
			?>
			</select>
		</div>
	</div>
</form>
