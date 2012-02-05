<form id="sledge-form-pagesettings-publishing" name="pagesettings-publishing">

	<div id="sledge-pagesettings" class="sledge-tabs">
			
		<ul>
			<li><a href="#sledge-pagesettings-basic">Basic</a></li>
			<li><a href="#sledge-pagesettings-advanced">Advanced</a></li>
		</ul>
		
		<div id="sledge-pagesettings-basic">
			<table width="100%">
				<tr>
					<td>Visible</td>
					<td>
						<select id="page-visible" name="visible" class="sledge-input sledge_select">
							<option value='1' <? if ($page->visible) echo "selected='selected'"; ?>>Yes</option>
							<option value='0' <? if (!$page->visible) echo "selected='selected'"; ?>>No</option>
						</select>
					</td>
				</tr>
				<?if ($person->can( 'view', $page, 'visible_from')):?>
					<tr>
						<td>Visible from</td>
						<td>
							<? if ($person->can( 'edit', $page, 'visible_from' )): ?>
								<input id="page-visible-from" name="visible_from" class="sledge-input sledge-datepicker" value="<?=date("d F Y", $page->visible_from);?>" />
								<select>
									<option>12:00</option>
								</select>
							<? else:
								echo date("d F Y", $page->visible_from);
							endif; ?>
						</td>
					</tr>
				<?endif;?>	
			
				<?if ($person->can( 'view', $page, 'visible_to' )):?>
					<tr>	
						<? if ($person->can( 'edit', $page, 'visible_to' )):?>
							<td>
								<label for="page-visible-to">Visible to</label>
								<input id="sledge-page-toggle-visible" type="checkbox" value="1" name='toggle_visible_to' class="ui-helper-right ui-helper-reset"<?=($page->visible_to) ? ' checked="checked"' : ''; ?> />
							</td>
							<td>	
								<input	id="page-visible-to" 
									name="visible_to" 
									class="sledge-input sledge-datepicker" 
									value="<?=($page->visible_to) ?	date('Y-m-d H:i:s',$page->visible_to) : 'forever'; ?>"
									<?=(!$page->visible_to) ? ' disabled="disabled"' : ''; ?>
								/>
								<select id="page-visible-to-time" disabled="diabled">
									<option>12:00</option>
								</select>
							</td>
						<? else:
								echo ($page->visible_to)? date('Y-m-d H:i:s',$page->visible_to) : 'forever';
							endif;
						?>
					</tr>
				<?endif;?>
			
				<? if ($person->can( 'view', $page, 'parent' )):?>
						<tr>
							<td>Parent page</td>
							<td>
								<? if ($person->can( 'edit', $page, 'parent' )): ?>
									<select style="width: 25em" name="parent_id">
									<option value="0">No parent</option>
									<?
										foreach( $page->mptt->fulltree() as $node ):
											echo "<option value='", $node->page_id, "'";
											if ($node->id == $page->mptt->parent_id)
											{
												echo " selected='selected'";
											}
											echo ">", $node->page->title, "</option>";
										endforeach;
									?>
									</select>
								<? else:
										echo $page->mptt->parent()->page->title;
									endif;
								?>
							</td>
						</tr>
				<? endif;?>
				
				<?if ($person->can( 'view', $page, 'primary_uri' )):?>
					<tr>
						<td>URI</td>
						<td>
							<? if ($person->can( 'edit', $page, 'primary_uri' )):?>
								<input class="sledge-input sledge-input-uri" type="text" name="uri" value="<?=$page->get_primary_uri()?>" />
							<? else:
									echo $page->get_primary_uri();
								endif;
							?>
						</td>
					</tr>
				<?endif;?>
				
				<? if ($person->can( 'view', $page, 'visible_in_leftnav' )):?>
					<tr>
						<td>Visible in navigation?</td>
						<td>
							<? if ($person->can( 'edit', $page, 'visible_in_leftnav' )):?>
								<select name="visible_in_leftnav">
									<option <?if ($page->visible_in_leftnav == null) echo "selected=\"selected\" ";?> value="">Inherit from my parent</option>
									<option <?if ($page->visible_in_leftnav == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
									<option <?if ($page->visible_in_leftnav == false) echo "selected=\"selected\" ";?> value="0">No</option>
								</select>
							<? else:
									echo ($page->visible_in_leftnav == true)? 'Yes' : 'No';
								endif;
							?>
						</td>
					</tr>
				<? endif; ?>
				
				<? if ($person->can( 'view', $page, 'enable_rss' )):?>
					<tr>
						<td>Enable RSS feeds?</td>
						<td>
							<? if ($person->can( 'edit', $page, 'enable_rss' )): ?>
								<select name="enable_rss">
									<option <?if ($page->enable_rss) echo "selected=\"selected\" ";?> value="1">Yes</option>
									<option <?if (!$page->enable_rss) echo "selected=\"selected\" ";?> value="0">No</option>
								</select>
							<? else:
								echo ($page->enable_rss)? 'Yes' : 'No';
							endif;
							?>
						</td>
					</tr>
				<? endif; ?>
			</table>
		</div>
		<div id="sledge-pagesettings-advanced">
			<table width="100%">
				
				<?if ($person->can( 'view', $page, 'template_id' )): ?>
					<tr>
						<td width="180">Template:</td>
						<td>
							<? if ($person->can( 'edit', $page, 'template_id' )): ?>
								<select name="template_id">
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
							<? else:
								echo $page->template->name;
							endif;
							?>
						</td>
					</tr>
				<? endif; ?>
		
				<?if ($person->can( 'view', $page, 'visible_in_leftnav_cms' )): ?>
					<tr>
						<td>Visible in CMS navigation?</td>
						<td>
							<? if ($person->can( 'edit', $page, 'visible_in_leftnav_cms' )): ?>
								<select name="visible_in_leftnav_cms">
									<option <?if ($page->visible_in_leftnav_cms == null) echo "selected=\"selected\" ";?> value="">Inherit from my parent</option>
									<option <?if ($page->visible_in_leftnav_cms == true) echo "selected=\"selected\" ";?> value="1">Yes</option>
									<option <?if ($page->visible_in_leftnav_cms == false) echo "selected=\"selected\" ";?> value="0">No</option>
								</select>
							<? else:
								echo ($page->visible_in_leftnav_cms)? 'Yes' : 'No';
							endif;
							?>
						</td>
					</tr>
				<? endif; ?>
			</table>
		</div>
	</div>
</form>
