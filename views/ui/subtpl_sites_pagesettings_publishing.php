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
	foreach (Kohana::config('permissions.current_version_whats') as $what) {
		$p['current_version'][$what] = Permissions::may_i($what);
	}
	foreach (Kohana::config('permissions.attributes_whats') as $what) {
		$p['attributes'][$what] = Permissions::may_i($what);
	}

	$i_can_has_all_templates = $p['attributes']['Can edit page template (all)'];

	$visible_tag = O::fa('tag')->find_by_name('Visible');

	$templates = (!$i_can_has_all_templates) ? Relationship::find_partners('template',$visible_tag) : O::fa('template');

	$templates = $templates->orderby('name','asc')->find_all();

	$hidden_inputs = '';

?>
<form id="sledge-form-pagesettings-publishing" name="pagesettings-publishing">

	<div id="sledge-pagesettings" class="sledge-tabs">
			
		<ul>
			<li><a href="#sledge-pagesettings-basic">Basic</a></li>
			<li><a href="#sledge-pagesettings-advanced">Advanced</a></li>
		</ul>
		
		<div id="sledge-pagesettings-basic">
			<table width="100%">
				<?if ($p['current_version']['Can edit page visible from']){?>
					<tr>
						<td>Visible from</td>
						<td>
							<input id="page-visible-from" name="visible_from_timestamp" class="sledge-input sledge-datepicker" value="<?=date("d F Y", $this->page->visiblefrom_timestamp);?>" />
							<select>
								<option>12:00</option>
							</select>
						</td>
					</tr>
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="visible_from_timestamp" value="'.date("Y-m-d H:i:s", $this->page->visiblefrom_timestamp).'" />';		
					if($p['current_version']['Can view page visible from']){?>
						<tr>	
							<td>Visible from</td>
							<td><?=date("Y-m-d H:i:s", $this->page->visiblefrom_timestamp);?></td>
						</tr>
					<?}?>
				<?}?>	
				<?if ($p['current_version']['Can edit page visible to']){?>
					<tr>	
						<td>
							<label for="page-visible-to">Visible to</label>
							<input id="sledge-page-toggle-visible" type="checkbox" value="1" class="ui-helper-right ui-helper-reset"<?=($this->page->visibleto_timestamp) ? ' checked="checked"' : ''; ?> />
						</td>
						<td>	
							<input	id="page-visible-to" 
								name="visible_to_timestamp" 
								class="sledge-input sledge-datepicker" 
								value="<?=($this->page->visibleto_timestamp) ?	date('Y-m-d H:i:s',$this->page->visibleto_timestamp) : 'forever'; ?>"
								<?=(!$this->page->visibleto_timestamp) ? ' disabled="disabled"' : ''; ?>
							/>
							<select id="page-visible-to-time" disabled="diabled">
								<option>12:00</option>
							</select>
						</td>
					</tr>
				<?}else{
					$hidden_inputs .= '<input type="hidden" name="visible_to_timestamp" value="'.date("Y-m-d H:i:s", $this->page->visibleto_timestamp).'" />';
					if($p['current_version']['Can view page visible to']){?>
					<tr>
						<td>Visible to:</td>
						<td><?=($this->page->visibleto_timestamp) ? date('Y-m-d H:i:s',$this->page->visibleto_timestamp) : 'Forever'; ?></td>
					</tr>
					<?}?>
				<?}?>
				<?if ($p['attributes']['Can edit page parent']){?>
					<tr>
						<td>Parent page</td>
						<td>
							<select style="width: 25em" name="parent_rid">
							<option value="<?=O::f('cms_page')->find_by_uri_and_title(null,'Site 1')->rid?>">No parent</option>
							<?
								$r = new Recursion_Page_Combo;
								$r->recurse(O::fa('page')->find_by_title('Site 1'),$this->page->parent_rid,true,false,false,$this->page,false,false,false,false,$this->page);
							?>
							</select>
						</td>
					</tr>
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="parent_rid" value="'.$this->page->parent_rid.'" />';
					if ($this->page->parent_rid) {
						$parent_page = O::f('cms_page',$this->page->parent_rid);
						$parent = '<a href="'.$parent_page->absolute_uri().'">'.$parent_page->title.'</a>';
					} else { $parent = 'None'; }
					if ($p['attributes']['Can view page parent']){?>
						<tr>
							<td>Parent page</td>
							<td>
								<?=$parent?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['attributes']['Can edit page URI']){?>
					<tr>
						<td>URI</td>
						<td>
							<input class="sledge-input sledge-input-uri" type="text" name="uri" value="<?=$this->page->uri?>" />
						</td>
					</tr>
				<?} else {
					$hidden_inputs .= '<input type="hidden" name="uri" value="'.$this->page->uri.'" />';
					if ($p['attributes']['Can view page URI']){?>
						<tr>
							<td>URI</td>
							<td>
								<?=$this->page->uri?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['attributes']['Can edit page hidden from leftnav']){?>
					<tr>
						<td>Hidden from navigation?</td>
						<td>
							<select name="hidden_from_leftnav">
								<option <?if ($this->page->hidden_from_leftnav != 't' and $this->page->hidden_from_leftnav != 'f') {echo "selected=\"selected\" ";}?> value="">Inherit from my parent</option>
								<option <?if ($this->page->hidden_from_leftnav == 't') {echo "selected=\"selected\" ";}?> value="true">Yes</option>
								<option <?if ($this->page->hidden_from_leftnav == 'f') {echo "selected=\"selected\" ";}?> value="false">No</option>
							</select>
						</td>
					</tr>
				<?} else {?>
					<?if ($this->page->hidden_from_leftnav == 't') {
						$hidden_inputs .= '<input type="hidden" name="hidden_from_leftnav" value="true" />';
					} else if ($this->page->ssl_only == 'f') {
						$hidden_inputs .= '<input type="hidden" name="hidden_from_leftnav" value="false" />';
					} else {
						$hidden_inputs .= '<input type="hidden" name="hidden_from_leftnav" value="" />';
					}?>
					<? if ($p['attributes']['Can view page hidden from leftnav']){?>
						<tr>
							<td>Hidden from navigation?</td>
							<td>
								<?
									if ($this->page->hidden_from_leftnav != 't' and $this->page->hidden_from_leftnav != 'f') echo 'Inherit from my parent';
									if ($this->page->hidden_from_leftnav == 't') echo 'Yes';
									if ($this->page->hidden_from_leftnav == 'f') echo 'No';
								?>
							</td>
						</tr>
					<?}?>
				<?}?>
			</table>
		</div>
		<div id="sledge-pagesettings-advanced">
			<table width="100%">
				<?if ($p['attributes'][$template_change_required_perm]){?>
					<tr>
						<td width="180">Template:</td>
						<td>
							<select name="template">
								<?
									foreach ($templates as $tpl) {
										if ($tpl->rid == $this->page->template_rid) {
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
							<td>Template:</td>
							<td>
								<?
									foreach ($templates as $tpl) {
										if ($tpl->rid == $this->page->template_rid) {
											echo $tpl->name;
										}
									}
								?>
							</td>
						</tr>
					<?}?>
				<?}?>
				<?if ($p['attributes']['Can edit page hidden from leftnav cms']){?>
					<tr>
						<td>Hidden from CMS navigation?</td>
						<td>
							<select name="hidden_from_leftnav_cms">
								<option <?if ($this->page->hidden_from_leftnav_cms != 't' and $this->page->hidden_from_leftnav_cms != 'f') {echo "selected=\"selected\" ";}?> value="">Inherit from my parent</option>
								<option <?if ($this->page->hidden_from_leftnav_cms == 't') {echo "selected=\"selected\" ";}?> value="true">Yes</option>
								<option <?if ($this->page->hidden_from_leftnav_cms == 'f') {echo "selected=\"selected\" ";}?> value="false">No</option>
							</select>
						</td>
					</tr>
				<?} else {?>
					<?if ($this->page->hidden_from_leftnav_cms == 't') {
						$hidden_inputs .= '<input type="hidden" name="hidden_from_leftnav_cms" value="true" />';
					} else if ($this->page->ssl_only == 'f') {
						$hidden_inputs .= '<input type="hidden" name="hidden_from_leftnav_cms" value="false" />';
					} else {
						$hidden_inputs .= '<input type="hidden" name="hidden_from_leftnav_cms" value="" />';
					}?>
					<? if ($p['attributes']['Can view page hidden from leftnav cms']){?>
						<tr>
							<td>Hidden from CMS navigation?</td>
							<td>
								<?
									if ($this->page->hidden_from_leftnav_cms != 't' and $this->page->hidden_from_leftnav_cms != 'f') echo 'Inherit from my parent';
									if ($this->page->hidden_from_leftnav_cms == 't') echo 'Yes';
									if ($this->page->hidden_from_leftnav_cms == 'f') echo 'No';
								?>
							</td>
						</tr>
					<?}?>
				<?}?>
			</table>
		</div>
	</div>
	<?= $hidden_inputs ;?>
</form>
