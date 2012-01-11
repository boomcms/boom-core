
<?
	# is the template user-changeable?
	if (Tag::has_tag_name('template',$page->template_rid,'Visible')) {
		$template_change_required_perm = 'Can edit page template (restricted)';
		$template_view_required_perm = 'Can view page template (restricted)';
	} else {
		$template_change_required_perm = 'Can edit page template (all)';
		$template_view_required_perm = 'Can view page template (all)';
	}
	
	$p = array();
	foreach (Kohana::config('permissions.attributes_whats') as $what) {
		$p['attributes'][$what] = Permissions::may_i($what);
	}

	$i_can_has_all_templates = $p['attributes']['Can edit page template (all)'];

	$visible_tag = O::fa('tag')->find_by_name('Visible');

	$templates = (!$i_can_has_all_templates) ? Relationship::find_partners('template',$visible_tag) : O::fa('template');

	$templates = $templates->orderby('name','asc')->find_all();

?>


<form id="sledge-page-add-form">

	<input type="hidden" name="page_rid" value="<?=$page->rid;?>" />
	
	<table>
		<tr>
			<td>
				<label for="parent-page">
					Parent page
				</label>
			</td>
			<td>
				<select name="parent_rid" style="width:24em">
					<option value="0">No parent</option>
					<?
						foreach( $page->mptt->full_tree() as $node ):
							echo "<option value='", $node->page_id, "'>", $node->page->title, "</option>";
						endforeach;
					?>
					</select>
				</select>
			</td>
		</tr>
		
		<?
		if ($p['attributes'][$template_change_required_perm]):?>
			<tr>
				<td>Template</td>
				<td>
					<select name="template_rid" style="width: 24em">
						<option value="">Inherit from parent</option>
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
		<?else:
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
		endif;?>
	</table>
</form>
