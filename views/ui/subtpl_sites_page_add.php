<form id="sledge-page-add-form">
	<input type='hidden' name='parent_id' value='<?= $page->id; ?>' />	
	<table>	
		<?
		//if ($p['attributes'][$template_change_required_perm]):?>
			<tr>
				<td>Template</td>
				<td>
					<select name="template_id" style="width: 24em">
						<option value="0">Inherit from parent</option>
						<?
							foreach ($templates as $tpl):
								if ($tpl->id == $page->default_child_template_id):
									echo "<option selected='selected' value='", $tpl->id, "'>", $tpl->name, "</option>";
								else:
									echo "<option value='", $tpl->id, "'>". $tpl->name, "</option>";
								endif;
							endforeach;
						?>
					</select>
				</td>
			</tr>
		<?/*else:
			$hidden_inputs .= '<input type="hidden" name="template" value="'.$page->template_rid.'" />';
			if ($p['attributes'][$template_view_required_perm]):?>
				<tr>
					<td>Template</td>
					<td>
						<select name="template_rid">
							<option>
							<?
								foreach ($templates as $tpl):

									if ($tpl->id == $page->default_child_template_id):
										echo $tpl->name;
									endif;
								endforeach;
							?>
							</option>
						</select>
					</td>
				</tr>
			<?endif;
		endif;*/?>
	</table>
</form>
