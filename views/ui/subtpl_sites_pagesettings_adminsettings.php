<?
	$p = array();
	foreach (Kohana::config('permissions.attributes_whats') as $what) {
		$p['attributes'][$what] = Permissions::may_i($what);
	}

	$hidden_inputs = '';
?>

<form name="pagesettings-adminsettings">
	<div id="admin-setings" class="sledge-tabs">
		<table width="100%">
			<?if ($p['attributes']['Can edit page standard template']){?>
				<tr>
					<td>Site standard template</td>
					<td>
						<input class="sledge-input" type="text" name="site_standard_template" value="<?=$page->site_standard_template;?>" />
					</td>
				</tr>
			<?} else {
				$hidden_inputs .= '<input type="hidden" name="site_standard_template" value="'.$page->site_standard_template.'" />';
				if ($p['attributes']['Can view page standard template']){?>
					<tr>
						<td>Site standard template</td>
						<td>
							<?=$page->site_standard_template?>
						</td>
					</tr>
				<?}?>
			<?}?>
			<?if ($p['attributes']['Can edit page standard template']){?>
				<tr>
					<td>CMS standard template</td>
					<td>
						<input class="sledge-input" type="text" name="cms_standard_template" value="<?=$page->cms_standard_template;?>" />
					</td>
				</tr>
			<?} else {
				$hidden_inputs .= '<input type="hidden" name="cms_standard_template" value="'.$page->cms_standard_template.'" />';
				if ($p['attributes']['Can view page standard template']){?>
					<tr>
						<td>CMS standard template</td>
						<td>
							<?=$page->cms_standard_template?>
						</td>
					</tr>
				<?}?>
			<?}?>
		       <?if ($p['attributes']['Can edit page internal name']){?>
				<tr>
					<td>Internal name</td>
					<td><input type="text" class="sledge-input" name="internal_name" id="internal_name" value="<?=$page->internal_name;?>" /></td>
				</tr>
			<?} else {
				$hidden_inputs .= '<input type="hidden" name="internal_name" value="'.$page->internal_name.'" />';
				if ($p['attributes']['Can view page internal name']){?>
					<tr>
						<td>Internal name</td>
						<td><?=$page->internal_name?></td>
					</tr>
				<?}?>
			<?}?>
			<?if ($p['attributes']['Can edit page type']){?>
				<tr>
					<td>Page type</td>
					<td><input id="pagetype_description" type="text" name="pagetype_description" class="sledge-input" value="<?=$page->pagetype_description?>" /></td>
				</tr>
			<?} else {
				$hidden_inputs .= '<input type="hidden" name="pagetype_description" value="'.$page->pagetype_description.'" />';
				if ($p['attributes']['Can view page type']){?>
					<tr>
						<td>Page type</td>
						<td><?=$page->pagetype_description?></td>
					</tr>
				<?}?>
			<?}?>
			<?if ($p['attributes']['Can edit page cache duration']){?>
				<tr>
					<td>Cache duration</td>
					<td>
						<select name="cache_duration">
							<option <?if ($page->cache_duration === null){echo "selected=\"selected\" ";}?> value="">Inherit from my parent</option>
							<option <?if ($page->cache_duration == '0'){echo "selected=\"selected\" ";}?> value="0">Disable caching</option>
							<?foreach(array(1,2,3,4,5,10,20,30) as $i) {?>
								<option <?if ($page->cache_duration == $i) {echo "selected=\"selected\" ";}?> value="<?=$i?>"><?=$i?> minute<?if($i!=1)echo 's';?></option>
							<?}?>
							<?foreach(array(1,2,24) as $i) {?>
								<option <?if ($page->cache_duration == ($i*60)) {echo "selected=\"selected\" ";}?> value="<?=($i*60)?>"><?=$i?> hour<?if($i!=1)echo 's';?></option>
							<?}?>
						</select>
					</td>
				</tr>
			<?} else {
				$hidden_inputs .= '<input type="hidden" name="cache_duration" value="'.$page->cache_duration.'" />';
				if ($p['attributes']['Can view page cache duration']){?>
					<tr>
						<td>Cache duration</td>
						<td>
							<?
								if ($page->cache_duration === null) echo 'Inherit from my parent';
								else if ($page->cache_duration == '0') echo 'Caching disabled';
								else {
									foreach(array(1,2,3,4,5,10,20,30) as $i) {
										if ($page->cache_duration == $i) {
											echo $i.' minute';
											if ($i!=1) echo 's';
										}
									}
									foreach(array(1,2,24) as $i) {
										if ($page->cache_duration == ($i*60)) {
											echo $i.' hour';
											if ($i!=1) echo 's';
										}
									}
								}
							?>
						</td>
					</tr>
				<?}?>
			<?}?>
			<?if ($p['attributes']['Can edit page SSL']){?>
					<tr>
						<td>Secure page?</td>
						<td>
							<select name="ssl_only">
								<option <?if ($page->ssl_only != 't' and $page->ssl_only != 'f') {echo "selected=\"selected\" ";}?> value="">Inherit from my parent</option>
								<option <?if ($page->ssl_only == true) {echo "selected=\"selected\" ";}?> value="true">Yes</option>
								<option <?if ($page->ssl_only == false) {echo "selected=\"selected\" ";}?> value="false">No</option>
							</select>
						</td>
					</tr>
			<?} else {?>
				<?if ($page->ssl_only == true) {
					$hidden_inputs .= '<input type="hidden" name="ssl_only" value="true" />';
				} else if ($page->ssl_only == false) {
					$hidden_inputs .= '<input type="hidden" name="ssl_only" value="false" />';
				} else {
					$hidden_inputs .= '<input type="hidden" name="ssl_only" value="" />';
				}?>
				<? if ($p['attributes']['Can view page SSL']){?>
					<tr>
						<td>Secure page?</td>
						<td>
							<?
								if ($page->ssl_only != 't' and $page->ssl_only != 'f') echo 'Inherit from my parent';
								if ($page->ssl_only == true) echo 'Yes';
								if ($page->ssl_only == false) echo 'No';
							?>
						</td>
					</tr>
				<?}?>
			<?}?>
		</table>
	</div>
	<?= $hidden_inputs;?>
</form>
