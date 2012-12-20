<form id="s-page-add-form">
	<table>
		<tr>
			<td>
				<label for="parent-page">
					<?=__('Parent page')?>
				</label>
			</td>
			<td>
				<select name="parent_id" style="width:24em">
					<option value="0"><?=__('No parent')?></option>
					<?
						foreach($page->mptt->fulltree() as $node):
							echo "<option value='", $node->id, "'";

							if ($node->id == $page->id)
								echo " selected='selected'";

							echo ">", $node->page->version()->title, "</option>";
						endforeach;
					?>
					</select>
				</select>
			</td>
		</tr>

		<tr>
			<td><?=__('Template')?></td>
			<td>
				<?= Form::select('template_id', $templates, $default_template, array('style' => 'width: 24em')); ?>
			</td>
		</tr>
	</table>
</form>