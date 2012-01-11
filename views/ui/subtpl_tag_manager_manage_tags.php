<?
	$edit_tag_rid = (int) $_REQUEST['tag_rid'];
	$base_tag_rid = (int) $_REQUEST['basetag_rid'];
?>

<div class="sledge-tabs">
	<ul>
		<li><a href="#sledge-tagmanager-tags-tags">Tags</a></li>
		<li><a href="#sledge-tagmanager-tags-add">Add tag</a></li>
	</ul>
	<div id="sledge-tagmanager-tags-tags">
		<div class="ui-widget">
			<div class="ui-state-highlight ui-corner-all"> 
				<p style="margin: .5em;">
					<span style="float: left; margin-right: 0.3em; margin-top:-.2em" class="ui-icon ui-icon-info"></span>
					Click on a tag name to edit the tag.
				</p> 
			</div>
		</div>
		<br />
		<?= Tag::get_tag_tree('cms', $edit_tag_rid, false, false, true, 'subtpl_tag_tree_manage')?>
	</div>
	<div id="sledge-tagmanager-tags-add">
		<form onsubmit="return false">
			<table width="100%">
				<tbody>
					<tr>
						<td>Tag name</td>
						<td><input type="text" class="sledge-input" name="name" /></td>
					</tr>
					<tr>
						<td>Tag parent</td>
						<td>
							<select name="parent_rid" id="folderparent" style="width:150px">
							<?
								foreach(Tag::gettags($base_tag_rid) as $tag) {
									if (!Tag::is_smart('tag', $tag->rid)) {?>
										<option value="<?=$tag->rid?>"><?=$tag->name?></option>
										<? foreach(Tag::gettags($tag->rid) as $child_tag) {
											if (!Tag::is_smart('tag', $child_tag->rid)) {
												echo Cms_tag_manager_Controller::recurse_combo($child_tag);
											}
										}
									}
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
							<button id="sledge-tagmanager-tags-add-save" class="sledge-button ui-button-text-icon ui-button ui-widget ui-state-default ui-corner-all">
								<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
								Save
							</button>
						</td>
					</tr>
				</tbody>
			</table>
		</form>
	</div>
</div>
