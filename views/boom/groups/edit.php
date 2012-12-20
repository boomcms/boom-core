<form rel='<?= $group->id ?>' onsubmit='return false;'>
	<?= Form::hidden('csrf', Security::token()) ?>
	<div class="boom-tabs">
		<ul>
			<li><a href="#edit-group-basic">Basic</a></li>
			<li><a href="#edit-group-permissions">Permissions</a></li>
		</ul>
		<div id="edit-group-basic">
			<table width="100%">
				<tr>
					<td>Name</td>
					<td><input type="text" id="boom-tagmanager-group-edit-name" class="boom-input boom-input-medium" name="name" value="<?=$group->name?>" /></td>
				</tr>
			</table>
		</div>
		<div id="edit-group-permissions">
			<div class="boom-tabs">
				<ul>
					<li><a href="#edit-group-permissions-general">General</a></li>
					<li><a href="#edit-group-permissions-page">Page</a></li>
				</ul>
				<?
				/**
				* Note: the css here currently uses styles instead of classes.
				* TODO: A CSS minded person needs to fix this.
				*/
				?>
				<div id="edit-group-permissions-general">
					<ul class='boom-tree' id='boom-group-permissions-general'>
						<? foreach ($general_permissions as $permission): ?>
							<? $class = ($permission->allowed)? 'color: green' : 'color: red'; ?>
							<li class='boom-group-permission' data-permission=

							<?
								echo "'", $permission->role_id, " 0 ", $permission->allowed, "'";
							?>

								style='<?= $class ?>'><?= $permission->role->description ?>
							</li>
						<? endforeach ?>
					</ul>

					<br class="ui-helper-clear" />

					<button class="boom-button ui-button-text-icon" class='boom-groupmanager-permission-add-general'>
						<span class="ui-button-icon-primary ui-icon ui-icon-disk"></span>
						Add Permission
					</button>
				</div>
				<div id="edit-group-permissions-page">
					<ul class=''>
						<?= Request::factory('cms/page/tree')->post(array('state' => 'collapsed'))->execute() ?>
					</ul>
					
					<ul id="boom-group-permissions-page" style='display: none'>  
						<? foreach ($page_permissions as $permission): ?>  
							<li class='boom-group-permission' data-permission=  

							<?  
								echo "'", $permission->role_id, " ", $permission->page_id, " ", $permission->allowed, "'";  
							?>  

								><?= $permission->role->description ?>  
							</li>  
						<? endforeach ?>  
					</ul>
				</div>
			</div>
		</div>
	</div>
</form>
