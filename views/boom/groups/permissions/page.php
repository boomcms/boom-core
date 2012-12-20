<div id="boom-group-permissions-page-add">
	<div id="edit-group-permissions-page">
		<ul class='boom-tree' id='boom-group-permissions-page-list'>
			<? foreach ($permissions as $permission): ?>
				<? $class = ($permission->enabled)? 'color: green' : 'color: red'; ?>
				<li class='boom-group-permissions-page-permission' data-permission=

				<?
					echo "'", $permission->role_id, " ", $permission->page_id, " ", $permission->enabled, "'";
				?>

					style='<?= $class ?>'><?= $permission->role->description ?>
				</li>
			<? endforeach ?>
		</ul>

		<button class="boom-button ui-button-text-icon">
			<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
			Add Permission
		</button>
	</div>
</div>
