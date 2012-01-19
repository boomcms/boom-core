<form id="sledge-form-pagesettings-publishing" name="pagesettings-publishing">

	<div id="sledge-pagesettings" class="sledge-tabs">
			
		<ul>
			<li><a href="#sledge-pagesettings-basic">Basic</a></li>
			<li><a href="#sledge-pagesettings-advanced">Advanced</a></li>
		</ul>
		
		<div id="sledge-pagesettings-basic">
			<table width="100%">
				<?//if ($p['current_version']['Can edit page visible from']){?>
					<tr>
						<td>Visible from</td>
						<td>
							<input id="page-visible-from" name="visible_from_timestamp" class="sledge-input sledge-datepicker" value="<?=date("d F Y", $page->visible_from);?>" />
							<select>
								<option>12:00</option>
							</select>
						</td>
					</tr>
				<?//}?>	
				<?//if ($p['current_version']['Can edit page visible to']){?>
					<tr>	
						<td>
							<label for="page-visible-to">Visible to</label>
							<input id="sledge-page-toggle-visible" type="checkbox" value="1" class="ui-helper-right ui-helper-reset"<?=($page->visible_to) ? ' checked="checked"' : ''; ?> />
						</td>
						<td>	
							<input	id="page-visible-to" 
								name="visible_to_timestamp" 
								class="sledge-input sledge-datepicker" 
								value="<?=($page->visible_to) ?	date('Y-m-d H:i:s',$page->visibleto_timestamp) : 'forever'; ?>"
								<?=(!$page->visible_to) ? ' disabled="disabled"' : ''; ?>
							/>
							<select id="page-visible-to-time" disabled="diabled">
								<option>12:00</option>
							</select>
						</td>
					</tr>
				<?//}?>
				<?//if ($p['attributes']['Can edit page parent']){?>
					<tr>
						<td>Parent page</td>
						<td>
							<select style="width: 25em" name="parent_rid">
							<option value="0">No parent</option>
							<?
								foreach( $page->mptt->fulltree() as $node ):
									echo "<option value='", $node->page_id, "'";
									echo ">", $node->page->title, "</option>";
								endforeach;
							?>
							</select>
						</td>
					</tr>
				<?//}?>
				<?//if ($p['attributes']['Can edit page URI']){?>
					<tr>
						<td>URI</td>
						<td>
							<input class="sledge-input sledge-input-uri" type="text" name="uri" value="<?=$page->url()?>" />
						</td>
					</tr>
				<?//}?>
				<?//if ($p['attributes']['Can edit page hidden from leftnav']){?>
					<tr>
						<td>Visible in navigation?</td>
						<td>
							<select name="visible_in_leftnav">
								<option <?if ($page->visible_in_leftnav === null) echo "selected=\"selected\" ";?> value="">Inherit from my parent</option>
								<option <?if ($page->visible_in_leftnav === true) echo "selected=\"selected\" ";?> value="true">Yes</option>
								<option <?if ($page->visible_in_leftnav === false) echo "selected=\"selected\" ";?> value="false">No</option>
							</select>
						</td>
					</tr>
				<?//}?>
			</table>
		</div>
		<div id="sledge-pagesettings-advanced">
			<table width="100%">
				<?//if ($p['attributes'][$template_change_required_perm]){?>
					<tr>
						<td width="180">Template:</td>
						<td>
							<select name="template">
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
				<?//if ($p['attributes']['Can edit page hidden from leftnav cms']){?>
					<tr>
						<td>Visible in CMS navigation?</td>
						<td>
							<select name="visible_in_leftnav_cms">
								<option <?if ($page->visible_in_leftnav_cms === null) echo "selected=\"selected\" ";?> value="">Inherit from my parent</option>
								<option <?if ($page->visible_in_leftnav_cms === true) echo "selected=\"selected\" ";?> value="true">Yes</option>
								<option <?if ($page->visible_in_leftnav_cms === false) echo "selected=\"selected\" ";?> value="false">No</option>
							</select>
						</td>
					</tr>
				<?//}?>
			</table>
		</div>
	</div>
</form>
