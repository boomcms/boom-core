
<?
	$this->page = O::fa('page', (int) $_GET['rid']);

	# is the template user-changeable?
	if (Tag::has_tag_name('template',$this->page->template_rid,'Visible')) {
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

	<input type="hidden" name="page_rid" value="<?=$this->page->rid;?>" />
	
	<table>
		<tr>
			<td>
				<label for="parent-page">
					Parent page
				</label>
			</td>
			<td>
				<select name="parent_rid" style="width:24em">
					<option value="<?=O::f('cms_page')->find_by_uri_and_title(null,'Site 1')->rid?>">No parent</option>
					<?
						$r = new Recursion_Page_Combo;
						$r->recurse(O::fa('page')->find_by_title('Site 1'),$this->page->rid);
					?>
					</select>
				</select>
			</td>
		</tr>
		
		<?
		if ($p['attributes'][$template_change_required_perm]){?>
			<tr>
				<td>Template</td>
				<td>
					<select name="template_rid" style="width: 24em">
						<option value="">Inherit from parent</option>
						<?
							foreach ($templates as $tpl) {
								if ($tpl->rid == $this->page->default_child_template_rid) {
									?><option selected="selected" value="<?=$tpl->rid?>"><?=$tpl->name?></option><?
								} else {
									?><option value="<?=$tpl->rid?>"><?=$tpl->name?></option><?
								}
							}
						?>
					</select>
				</td>
			</tr>
		<?}else{
			$hidden_inputs .= '<input type="hidden" name="template" value="'.$this->page->template_rid.'" />';
			if ($p['attributes'][$template_view_required_perm]){?>
				<tr>
					<td>Template</td>
					<td>
						<select name="template_rid">
							<option>
							<?
								foreach ($templates as $tpl) {

									if ($tpl->rid == $this->page->default_child_template_rid) {
										echo $tpl->name;
									}
								}
							?>
							</option>
						</select>
					</td>
				</tr>
			<?}?>
		<?}?>
	</table>
</form>
