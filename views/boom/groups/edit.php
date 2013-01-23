<form rel='<?= $group->id ?>' onsubmit='return false;'>
	<label for="b-people-group-name"><?= __('Name') ?></label>
	<input type="text" id="b-people-group-name" class="boom-input boom-input-medium" name="name" value="<?=$group->name?>" />

	<button class="boom-button" id="b-people-group-save" data-icon="ui-icon-boom-accept">
		<?= __('Save') ?>
	</button>

	<div class="boom-tabs ui-helper-clearfix">
		<ul>
			<li><a href="#b-group-roles-general"><?=__('CMS')?></a></li>
			<li><a href="#b-group-roles-pages"><?=__('Pages')?></a></li>
		</ul>

		<div id="b-group-roles-general" class="ui-tabs-panel ui-widget-content ui-helper-left">
				<ul>
					<? foreach ($general_roles as $role): ?>
						<li data-id="<?= $role->id ?>">
							<?= $role->description ?>

							<?= Form::radio( $role->id, 1, FALSE, array( 'id' => 'allow-' . $role->id )); ?><label for="allow-<?= $role->id ?>">A</label> 
							 <?= Form::radio( $role->id, 0, FALSE, array( 'id' => 'deny-' . $role->id )); ?><label for="deny-<?= $role->id ?>">D</label>
							<?= Form::radio( $role->id, -1, TRUE, array( 'id' => 'none-' . $role->id )); ?><label for="none-<?= $role->id ?>">X</label>
						</li>
					<? endforeach; ?>
				</ul>
		</div>
		<div id="b-group-roles-pages" class="ui-tabs-panel ui-widget-content ui-helper-left">
			<div>
				<?= Request::factory('cms/page/tree')->execute() ?>
			</div>
			<div>
				<ul>
					<? foreach ($page_roles as $role): ?>
						<li data-id="<?= $role->id ?>">
							<?= $role->description ?>

							<?= Form::radio( $role->id, 1, FALSE, array( 'id' => 'allow-' . $role->id )); ?><label for="allow-<?= $role->id ?>">A</label> 
							<?= Form::radio( $role->id, 0, FALSE, array( 'id' => 'deny-' . $role->id )); ?><label for="deny-<?= $role->id ?>">D</label> 
							<?= Form::radio( $role->id, -1, TRUE, array( 'id' => 'none-' . $role->id )); ?><label for="none-<?= $role->id ?>">X</label> 
						</li>
					<? endforeach; ?>
				</ul>
			</div>
		</div>

	</div>
</form>
