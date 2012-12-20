<form onsubmit='return false;'>
	<?= Form::hidden('csrf', Security::token()) ?>
	<?= Form::hidden('type', $type) ?>
	<div class="sledge-tabs">
		<ul>
			<li><a href="#edit-folder-basic">Basic</a></li>
		</ul>
		<div id="edit-folder-basic">
			<table width="100%">
				<tr>
					<td>Name</td>
					<td><input type="text" id="sledge-tagmanager-tag-edit-name" class="sledge-input sledge-input-medium" name="name" value="<?=$tag->name?>" /></td>
				</tr>
				<tr>
					<td>Parent tag</td>
					<td>
						<select id="sledge-tagmanager-tag-edit-parent" name="parent_id">
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
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>
