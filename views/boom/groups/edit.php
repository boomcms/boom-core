<form class='ui-widget-content' rel='<?= $group->id ?>' onsubmit='return false;'>
	<fieldset>
		<label for="b-people-group-name"><?= __('Name') ?></label>
		<input type="text" id="b-people-group-name" class="boom-input boom-input-medium" name="name" value="<?=$group->name?>" />

		<?= BoomUI::button('accept', __('Save'), array('id' => 'b-people-group-save')) ?>
	</fieldset>

	<div class="boom-tabs">
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