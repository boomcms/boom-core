<form class='ui-widget-content' rel='<?= $group->id ?>' onsubmit='return false;'>
	<fieldset>
		<label for="b-people-group-name"><?= __('Name') ?></label>
		<input type="text" id="b-people-group-name" class="boom-input boom-input-medium" name="name" value="<?=$group->name?>" />

		<?= BoomUI::button('accept', __('Save'), array('id' => 'b-people-group-save')) ?>
	</fieldset>

	<div class="boom-tabs">
		<div class="b-people-help">
			<p>
				Here you can edit the CMS and site permissions for this group.
			</p>
			<p>
				BoomCMS uses a role based permissions system. A group can have three options for each role:
			</p>

			<dl>
				<dt>Allow</dt>
				<dd>Members of the group may perform the role unless it's been denied by another group they're a member of (see below).</dd>
				<dt>Deny</dt>
				<dd>Members of the group cannot perform the role. Deny takes precedence over allow so if a user is a member of two groups and one allows a role while the other denies it you they won't be able to perform the role. You can therefore be confident that when a group denies a role the members of the group won't have access to that functionality regardless of their membership of other groups.</dd>
				<dt>Not set</dt>
				<dd>The group does not allow or deny access to the role. Members of the group would not be able to perform the role but unlike the Deny permission if a user is a member of another group which allows the role they will be able to perform the role. For page permissions if a value is set of a parent page but not a child page then the child will inherit the permissions from the parent.</dd>
			</dl>
		</div>
		<ul>
			<li><a href="#b-group-roles-general"><?=__('CMS Permissions')?></a></li>
			<li><a href="#b-group-roles-pages"><?=__('Page Permissions')?></a></li>
		</ul>

		<div id="b-group-roles-general" class="ui-tabs-panel">
			<p>Use this section to edit permissions which related to the CMS as a whole rather than particular pages in the site tree.</p>
			<?= new View('boom/groups/roles', array('roles' => $general_roles)) ?>
		</div>
		<div id="b-group-roles-pages" class="ui-tabs-panel">
			<div>
				<input type="hidden" name="parent_id" value="">
				<p>Select a page from the tree to edit the permissions for that page and it's descendants.</p>
				<div class="boom-tree">
					<ul>
						<li><a id="page_5" href="/" rel="5">Home</a></li>
					</ul>
				</div>
			</div>
			<div>
				<?= new View('boom/groups/roles', array('roles' => $page_roles)) ?>
			</div>
		</div>
	</div>
</form>

<script type='text/javascript'>
	window.onload = function() {
		$('body').groupPermissionsEditor({
			group : new boomGroup(<?= $group->id ?>)
		});
	};
</script>