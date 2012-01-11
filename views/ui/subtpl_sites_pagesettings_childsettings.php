<?
	$this->page = O::fa('page', (int) $_GET['rid']);
	$p = array();
	foreach (Kohana::config('permissions.child_settings_whats') as $what) {
		$p['child_settings'][$what] = Permissions::may_i($what);
	}

	$hidden_inputs = '';
?>
<form id="sledge-form-pagesettings-childsettings" name="pagesettings-childsettings">
	<div id="child-settings" class="sledge-tabs">
		<ul>
			<li><a href="#child-settings-basic">Basic</a></li>
			<li><a href="#child-settings-advanced">Advanced</a></li>
		</ul>
		<div id="child-settings-basic">
			<table width="100%">
				<?if ($p['child_settings']['Can edit page child parent']){?>
					<tr>
						<td>Children parent page</td>
						<td>
							<select name="pagetype_parent_rid" id="pagetype_parent_rid" style="width:14em">
							<option value="NULL">Default (this page)</option>
							<?
								$r = new Recursion_Page_Combo;
								$r->recurse(O::fa('page')->find_by_title('Site 1'),$this->page->pagetype_parent_rid,true,false,false,$this->page,false,false,false);					
							?>
							</select><br/>
						</td>
					</tr>
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="pagetype_parent_rid" value="'.$this->page->pagetype_parent_rid.'" />';
					if ($p['child_settings']['Can view page child parent']){?>
						<tr>	
							<td>Children parent page</td>
							<td>	
								<? if (!$this->page->pagetype_parent_rid) {?>
									Default (this page)
								<?}else{
									$child_parent_page = O::f('cms_page',$this->page->pagetype_parent_rid);
									?>
									<a href="<?=$child_parent_page->absolute_uri()?>"><?=$child_parent_page->title?></a>
								<?}?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['child_settings']['Can edit page default child template']){?>
					<tr>
						<td>Default child template</td>
						<td>
							<select name="default_child_template_rid">
								<option value="">Same as this page</option><?
									foreach (O::fa('template')->orderby('name', 'asc')->find_all() as $tpl) {
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
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="default_child_template_rid" value="'.$this->page->default_child_template_rid.'" />';
					if ($p['child_settings']['Can view page default child template']){?>
						<tr>
							<td>Default child template</td>
							<td>
								<? if (!$this->page->default_child_template_rid) {?>
									Same as this page
								<?}else{?>
									<?=O::fa('template',$this->page->default_child_template_rid)->name?>
								<?}?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['child_settings']['Can edit page child ordering policy']){?>
					<tr>
						<td>Child ordering policy</td>
						<td>
							<select name="child_ordering_policy_rid">
							<?
								$refchildorderingpolicy = O::fa('ref_child_ordering_policy')->find_all();
								foreach ($refchildorderingpolicy as $policy) {
									if ($policy->id == $this->page->child_ordering_policy_rid) {
										?><option selected="selected" value="<?=$policy->id?>"><?=$policy->name?></option><?
									} else {
										?><option value="<?=$policy->id?>"><?=$policy->name?></option><?
									}
								}
							?>
							</select>
						</td>
					</tr>
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="child_ordering_policy_rid" value="'.$this->page->child_ordering_policy_rid.'" />';
					if ($p['child_settings']['Can view page child ordering policy']){?>
						<tr>
							<td>Child ordering policy</td>
							<td>
								<?=O::fa('ref_child_ordering_policy',$this->page->child_ordering_policy_rid)->name?>
							</td>
						</tr>
					<?}?>
				<?}?>
			</table>
		</div>
		<div id="child-settings-advanced">
			<table width="100%">
				<?if ($p['child_settings']['Can edit page children hidden from leftnav']){?>
					<tr>
						<td>Children hidden from leftnav?</td>
						<td>
							<select name="children_hidden_from_leftnav">
								<option <?if ($this->page->children_hidden_from_leftnav != 't' and $this->page->children_hidden_from_leftnav != 'f') {echo "selected=\"selected\" ";} ?> value="">Inherit from my parent</option>
								<option <?if ($this->page->children_hidden_from_leftnav == true) {echo "selected=\"selected\" ";}?> value="true">Yes</option>
								<option <?if ($this->page->children_hidden_from_leftnav == false) {echo "selected=\"selected\" ";}?> value="false">No</option>
							</select>
						</td>
					</tr>
				<?} else {?>
					<?if ($this->page->children_hidden_from_leftnav == true){
						$hidden_inputs .= '<input type="hidden" name="children_hidden_from_leftnav" value="true" />';
					} else if ($this->page->children_hidden_from_leftnav == false){
						$hidden_inputs .= '<input type="hidden" name="children_hidden_from_leftnav" value="false" />';
					} else {
						$hidden_inputs .= '<input type="hidden" name="children_hidden_from_leftnav" value="" />';
					}
					if ($p['child_settings']['Can view page children hidden from leftnav']){?>
						<tr>
							<td>Children hidden from leftnav?</td>
							<td>
								<?
									if ($this->page->children_hidden_from_leftnav != 't' and $this->page->children_hidden_from_leftnav != 'f') echo 'Inherit from my parent';
									if ($this->page->children_hidden_from_leftnav == true) echo 'Yes';
									if ($this->page->children_hidden_from_leftnav == false) echo 'No';
								?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['child_settings']['Can edit page children hidden from leftnav cms']){?>
					<tr>
						<td>Children hidden from CMS leftnav?</td>
						<td>
							<select name="children_hidden_from_leftnav_cms">
								<option <?if ($this->page->children_hidden_from_leftnav_cms != 't' and $this->page->children_hidden_from_leftnav_cms != 'f') {echo "selected=\"selected\" ";} ?> value="">Inherit from my parent</option>
								<option <?if ($this->page->children_hidden_from_leftnav_cms == true) {echo "selected=\"selected\" ";}?> value="true">Yes</option>
								<option <?if ($this->page->children_hidden_from_leftnav_cms == false) {echo "selected=\"selected\" ";}?> value="false">No</option>
							</select>
						</td>
					</tr>
				<?} else {?>
					<?if ($this->page->children_hidden_from_leftnav_cms == true){
						$hidden_inputs .= '<input type="hidden" name="children_hidden_from_leftnav_cms" value="true" />';
					} else if ($this->page->children_hidden_from_leftnav_cms == false){
						$hidden_inputs .= '<input type="hidden" name="children_hidden_from_leftnav_cms" value="false" />';
					} else {
						$hidden_inputs .= '<input type="hidden" name="children_hidden_from_leftnav_cms" value="" />';
					}
					if ($p['child_settings']['Can view page children hidden from leftnav cms']){?>
						<tr>
							<td>Children hidden from CMS leftnav?</td>
							<td>
								<?
									if ($this->page->children_hidden_from_leftnav_cms != 't' and $this->page->children_hidden_from_leftnav_cms != 'f') echo 'Inherit from my parent';
									if ($this->page->children_hidden_from_leftnav_cms == true) echo 'Yes';
									if ($this->page->children_hidden_from_leftnav_cms == false) echo 'No';
								?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['child_settings']['Can edit page default child URI prefix']){?>
					<tr>
						<td>Default child URI prefix</td>
						<td>
							<input type="text" class="sledge-input" name="default_child_uri_prefix" value="<?=$this->page->default_child_uri_prefix?>" />
						</td>
					</tr>
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="default_child_uri_prefix" value="'.$this->page->default_child_uri_prefix.'" />';
					if ($p['child_settings']['Can view page default child URI prefix']){?>
						<tr>
							<td>Default child URI prefix</td>
							<td>
								<?=$this->page->default_child_uri_prefix?>
							</td>
						</tr>
					<?}?>
				<?}?>

				<?if ($p['child_settings']['Can edit page default child sitemap priority']){?>
					<tr>
						<td>Default child sitemap priority</td>
						<td>
							<select name="default_child_sitemap_priority">
								<option value="">Inherit from my parent</option>
								<?for ($i=0.0; $i<=1.0; $i+=0.1) {?>
									<option <?if ($this->page->default_child_sitemap_priority === $i) {?>selected="selected" <?}?> value="<?=$i?>"><?=$i?></option>
								<?}?>
							</select>
							0.0 is lowest, 1.0 is highest
						</td>
					</tr>
				<?}else{
					$hidden_inputs .= '<input type="hidden" name="default_child_sitemap_priority" value="'.$this->page->default_child_sitemap_priority.'" />';
					if ($p['child_settings']['Can view page default child sitemap priority']){?>
						<tr>
							<td>Default child sitemap priority</td>
							<td>
								<?=(($this->page->default_child_sitemap_priority == null) ? 'Inherit from my parent' : $this->page->default_child_sitemap_priority)?>
								0.0 is lowest, 1.0 is highest
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['child_settings']['Can edit page default child sitemap update frequency']){?>
					<tr>
						<td>Default child sitemap update frequency</td>
						<td>
							<select name="default_child_sitemap_update_frequency">
								<option value="">Inherit from my parent</option>
								<?foreach (array('never','yearly','monthly','weekly','daily','hourly','always') as $period) {?>
									<option <?if ($this->page->default_child_sitemap_update_frequency === $period) {?>selected="selected" <?}?> value="<?=$period?>"><?=$period?></option>
								<?}?>
							</select>
						</td>
					</tr>
				<?}else{
					$hidden_inputs .= '<input type="hidden" name="default_child_sitemap_update_frequency" value="'.$this->page->default_child_sitemap_update_frequency.'" />';
					if ($p['child_settings']['Can view page default child sitemap update frequency']){?>
						<tr>
							<td>Default child sitemap update frequency</td>
							<td>
								<?=(($this->page->default_child_sitemap_update_frequency == null) ? 'Inherit from my parent' : $this->page->default_child_sitemap_update_frequency)?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['child_settings']['Can edit page default grandchild template']) {?>
					<tr>
						<td>Default grandchild template</td>
						<td>
							<select name="default_child_default_child_template_rid">
								<option value="">Inherit from childs parent</option>
								<?foreach (O::fa('template')->orderby('name', 'asc')->find_all() as $tpl) {?>
									<option value="<?=$tpl->rid?>"<?if ($tpl->rid == $this->page->default_child_default_child_template_rid) {?> selected="selected"<?}?>>
										<?=$tpl->name?>
									</option>
								<?}?>
							</select>
						</td>
					</tr>
				<?}else{
					$hidden_inputs .= '<input type="hidden" name="default_child_default_child_template_rid" value="'.$this->page->default_child_default_child_template_rid.'" />';
					if ($p['child_settings']['Can view page default grandchild template']) {?>
						<tr>
							<td>Default grandchild template</td>
							<td>
								<?=(($this->page->default_child_default_child_template_rid == null) ? 'Inherit from childs parent' : O::fa('template',$this->page->default_child_default_child_template_rid)->name)?>
							</td>
						</tr>
					<?}?>
				<?}?>
			</table>
		</div>
	</div>
</form>
