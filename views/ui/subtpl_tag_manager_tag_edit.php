<?

	$tag = O::fa('tag', (int) $_REQUEST['tag_rid']);

	!$tag->rid and die();

	$basetag_rid = (int) $_REQUEST['basetag_rid'];
?>

<div class="sledge-tabs">
	<ul>
		<li><a href="#edit-folder-basic">Basic</a></li>
		<li><a href="#edit-folder-advanced">Advanced</a></li>
	</ul>
	<div id="edit-folder-basic">
		<table width="100%">
			<tr>
				<td>Name</td>
				<td><input type="text" class="sledge-input sledge-input-medium" name="name" value="<?=$tag->name?>" /></td>
			</tr>
			<tr>
				<td>Parent folder</td>
				<td>
					<select name="parent_rid">
					<?
						foreach(Tag::gettags($basetag_rid) as $child_tag) {
							if (!Tag::is_smart('tag', $child_tag->rid)) {
								echo Cms_tag_manager_Controller::recurse_combo($child_tag, NULL, 0, $tag->parent_rid);
							}
						}
					?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div id="edit-folder-advanced">
		<table width="100%">
			<tr>
				<td>Child ordering policy</td>
				<td>
					<select name="child_ordering_policy_rid">
						<option value="1"<?if ($tag->child_ordering_policy_rid==1){?> selected="selected"<?}?>>Sequence</option>
						<option value="2"<?if ($tag->child_ordering_policy_rid==2){?> selected="selected"<?}?>>Alphabetical</option>
						<option value="3"<?if ($tag->child_ordering_policy_rid==3){?> selected="selected"<?}?>>Date</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Child ordering direction</td>
				<td>
					<select id="child_ordering_direction" name="child_ordering_direction" style="width:auto">
						<option value="1"<?if ($tag->child_ordering_direction==1){?> selected="selected"<?}?>>Ascending</option>
						<option value="2"<?if ($tag->child_ordering_direction==2){?> selected="selected"<?}?>>Descending</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Item ordering policy</td>
				<td>
					<select id="item_ordering_policy_rid" name="item_ordering_policy_rid" style="width:auto">
						<option value="">- Inherit from my parent -</option>
						<option value="1"<?if ($tag->item_ordering_policy_rid==1){?> selected="selected"<?}?>>Sequence</option>
						<option value="2"<?if ($tag->item_ordering_policy_rid==2){?> selected="selected"<?}?>>Alphabetical</option>
						<option value="3"<?if ($tag->item_ordering_policy_rid==3){?> selected="selected"<?}?>>Date</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>Item ordering direction</td>
				<td>
					<select id="item_ordering_direction" name="item_ordering_direction" style="width:auto">
						<option value="">- Inherit from my parent -</option>
						<option value="1"<?if ($tag->item_ordering_direction==1){?> selected="selected"<?}?>>Ascending</option>
						<option value="2"<?if ($tag->item_ordering_direction==2){?> selected="selected"<?}?>>Descending</option>
					</select>
				</td>
			</tr>
		</table>
	</div>
</div>
