<form id="sledge-form-pagesettings-childsettings" name="pagesettings-childsettings">
	<div id="child-settings" class="sledge-tabs">
		<ul>
			<li><a href="#child-settings-basic">Basic</a></li>
			<li><a href="#child-settings-advanced">Advanced</a></li>
		</ul>
		<div id="child-settings-basic">
			<table width="100%">
				<?//if ($p['child_settings']['Can edit page child parent']){?>
					<tr>
						<td>Children parent page</td>
						<td>
							<select name="pagetype_parent_id" id="pagetype_parent_id" style="width:14em">
							<option value="">Default (this page)</option>
								<?
									foreach( $page->mptt->fulltree() as $node ):
										if( $node->page_id != $page->id ):
											echo "<option value='", $node->page_id, "'";
											echo ">", $node->page->title, "</option>";
										endif;
									endforeach;
								?>				
							</select><br/>
						</td>
					</tr>
				<?//}?>
				<?//if ($p['child_settings']['Can edit page default child template']){?>
					<tr>
						<td>Default child template</td>
						<td>
							<select name="default_child_template_id">
								<option value="$page->template_id">Same as this page</option><?
									foreach ($templates as $tpl):
										if ($tpl->id == $page->template_id):
											?><option selected="selected" value="<?=$tpl->id?>"><?=$tpl->name?></option><?
										else:
											?><option value="<?=$tpl->id?>"><?=$tpl->name?></option><?
										endif;
									endforeach;
								?>
							</select>
						</td>
					</tr>
				<?//}?>
				<?//if ($p['child_settings']['Can edit page child ordering policy']){?>
					<tr>
						<td>Child ordering policy</td>
						<td>
							<select name="child_ordering_policy_rid">
							<?
								
							?>
							</select>
						</td>
					</tr>
				<?//}?>
			</table>
		</div>
		<div id="child-settings-advanced">
			<table width="100%">
				<?//if ($p['child_settings']['Can edit page children hidden from leftnav']){?>
					<tr>
						<td>Children hidden from leftnav?</td>
						<td>
							<select name="children_visible_in_leftnav">
								<option <?if ($page->children_visible_in_leftnav != 't' and $page->children_visible_in_leftnav != 'f') {echo "selected=\"selected\" ";} ?> value="">Inherit from my parent</option>
								<option <?if ($page->children_visible_in_leftnav == true) {echo "selected=\"selected\" ";}?> value="1">Yes</option>
								<option <?if ($page->children_visible_in_leftnav == false) {echo "selected=\"selected\" ";}?> value="0">No</option>
							</select>
						</td>
					</tr>
				<?//}?>
				<?//if ($p['child_settings']['Can edit page children hidden from leftnav cms']){?>
					<tr>
						<td>Children visible in CMS leftnav?</td>
						<td>
							<select name="children_visible_in_leftnav_cms">
								<option <?if ($page->children_visible_in_leftnav_cms === null) echo "selected='selected'"; ?> value="">Inherit from my parent</option>
								<option <?if ($page->children_visible_in_leftnav_cms === true) echo "selected=\"selected\" ";?> value="1">Yes</option>
								<option <?if ($page->children_visible_in_leftnav_cms === false) echo "selected=\"selected\" ";?> value="0">No</option>
							</select>
						</td>
					</tr>
				<?//}?>
				
				<?//if ($p['child_settings']['Can edit page default child URI prefix']){?>
					<tr>
						<td>Default child URI prefix</td>
						<td>
							<input type="text" class="sledge-input" name="default_child_uri_prefix" value="<?=$page->default_child_uri_prefix?>" />
						</td>
					</tr>
				<?//}?>

				<?//if ($p['child_settings']['Can edit page default child sitemap priority']){?>
					<tr>
						<td>Default child sitemap priority</td>
						<td>
							<select name="default_child_sitemap_priority">
								<option value="">Inherit from my parent</option>
								<?for ($i=0.0; $i<=1.0; $i+=0.1) {?>
									<option <?if ($page->default_child_sitemap_priority === $i) {?>selected="selected" <?}?> value="<?=$i?>"><?=$i?></option>
								<?}?>
							</select>
							0.0 is lowest, 1.0 is highest
						</td>
					</tr>
				<?//}?>
				<?//if ($p['child_settings']['Can edit page default child sitemap update frequency']){?>
					<tr>
						<td>Default child sitemap update frequency</td>
						<td>
							<select name="default_child_sitemap_update_frequency">
								<option value="">Inherit from my parent</option>
								<?foreach (array('never','yearly','monthly','weekly','daily','hourly','always') as $period) {?>
									<option <?if ($page->default_child_sitemap_update_frequency === $period) {?>selected="selected" <?}?> value="<?=$period?>"><?=$period?></option>
								<?}?>
							</select>
						</td>
					</tr>
				<?//}?>
				<?//if ($p['child_settings']['Can edit page default grandchild template']) {?>
					<tr>
						<td>Default grandchild template</td>
						<td>
							<select name="default_child_default_child_template_id">
								<option value="">Inherit from childs parent</option>
								<?
									foreach ($templates as $tpl):
										if ($tpl->id == $page->template_id):
											?><option selected="selected" value="<?=$tpl->id?>"><?=$tpl->name?></option><?
										else:
											?><option value="<?=$tpl->id?>"><?=$tpl->name?></option><?
										endif;
									endforeach;
								?>
							</select>
						</td>
					</tr>
				<?//}?>
			</table>
		</div>
	</div>
</form>
