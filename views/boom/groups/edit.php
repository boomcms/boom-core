<form rel='<?= $group->id ?>' onsubmit='return false;'>
	<label for="b-people-group-name"><?= __('Name') ?></label>
	<input type="text" id="b-people-group-name" class="boom-input boom-input-medium" name="name" value="<?=$group->name?>" />

	<div class="boom-tabs ui-helper-clearfix">
		<ul>
			<li><a href="#b-group-roles-general"><?=__('CMS')?></a></li>
			<li><a href="#b-group-roles-pages"><?=__('Pages')?></a></li>
		</ul>

		<div class="b-group-roles-general ui-tabs-panel ui-widget-content ui-helper-left">
				<ul>
					<? foreach ($general_roles as $role): ?>
						<li data-id="<?= $role->id ?>">
							<?= $role->description ?>
						</li>
					<? endforeach; ?>
				</ul>
		</div>
		<div class="b-group-roles-pages ui-tabs-panel ui-widget-content ui-helper-left">
			<div>
				<?= Request::factory('cms/page/tree')->execute() ?>
			</div>
			<div>
				<ul>
					<? foreach ($page_roles as $role): ?>
						<li data-id="<?= $role->id ?>">
							<?= $role->description ?>
						</li>
					<? endforeach; ?>
				</ul>
			</div>
		</div>
	</div>
</form>