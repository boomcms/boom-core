<?

	$basetag_rid = (int) $_REQUEST['basetag_rid'];
?>

<form onsubmit="return false">
	<div class="sledge-tabs">
		<ul>
			<li><a href="#edit-folder-basic">Basic</a></li>
			<li><a href="#edit-folder-advanced">Advanced</a></li>
		</ul>
		<div id="edit-folder-basic">
			<table width="100%">
				<tr>
					<td>Name</td>
					<td><input type="text" class="sledge-input sledge-input-medium" name="name" /></td>
				</tr>
				<tr>
					<td>Parent folder</td>
					<td>
						<select name="parent_rid">
						<?
							foreach(Tag::gettags($basetag_rid) as $child_tag) {
								if (!Tag::is_smart('tag', $child_tag->rid)) {
									echo Cms_tag_manager_Controller::recurse_combo($child_tag);
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
							<option value="1">Sequence</option>
							<option value="2">Alphabetical</option>
							<option value="3">Date</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Child ordering direction</td>
					<td>
						<select id="child_ordering_direction" name="child_ordering_direction" style="width:auto">
							<option value="1">Ascending</option>
							<option value="2">Descending</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Item ordering policy</td>
					<td>
						<select id="item_ordering_policy_rid" name="item_ordering_policy_rid" style="width:auto">
							<option value="">- Inherit from my parent -</option>
							<option value="1">Sequence</option>
							<option value="2">Alphabetical</option>
							<option value="3">Date</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>Item ordering direction</td>
					<td>
						<select id="item_ordering_direction" name="item_ordering_direction" style="width:auto">
							<option value="">- Inherit from my parent -</option>
							<option value="1">Ascending</option>
							<option value="2">Descending</option>
						</select>
					</td>
				</tr>
			</table>
		</div>
	</div>
</form>
