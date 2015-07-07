<div class="b-person-view" data-person-id='<?= $person->getId() ?>'>
	<div class="boom-tabs">
		<ul>
			<li><a href="#b-person-view-information<?= $person->getId() ?>">Information</a></li>
			<li><a href="#b-person-view-groups<?= $person->getId() ?>">Groups</a></li>
		</ul>

		<div id="b-person-view-information<?= $person->getId() ?>">
			<form>
				<label>
					Name
					<input type="text" name="name" value="<?= $person->getName() ?>" />
				</label>

				<label for="person-email">
					Email
					<input type="text" name="email" disabled="disabled" value="<?= $person->getEmail() ?>" />
				</label>

				<label for='person-status'>
					Status

					<select name="enabled" id="person-status">
						<option value="0"<?php if ( ! $person->isEnabled()): ?> selected="selected"<?php endif ?>>Disabled</option>
						<option value="1"<?php if ($person->isEnabled()): ?> selected="selected"<?php endif ?>>Enabled</option>
					</select>
				</label>
				
				<?php if ($auth->getPerson()->isSuperuser() && $auth->getPerson()->getId() != $person->getId()): ?>
					<label for='person-superuser'>
						Superuser

						<select name="superuser" id="person-superuser">
							<option value="0"<?php if ( ! $person->isSuperuser()): ?> selected="selected"<?php endif ?>>No</option>
							<option value="1"<?php if ($person->isSuperuser()): ?> selected="selected"<?php endif ?>>Yes</option>
						</select>
					</label>
				<?php endif ?>

				<div>
					<?= $button('save', Lang::get('Save'), ['id' => 'b-person-save', 'class' => 'b-people-save']) ?>
					<?= $button('trash-o', Lang::get('Delete'), ['id' => 'b-person-delete']) ?>
				</div>
			</form>
		</div>

		<div id="b-person-view-groups<?= $person->getId() ?>">
			<?= $person->getName() ?>
			<?php if (count($groups) == 0): ?>
				is not a member of any groups<br />
			<?php else: ?>
				is a member of these groups:
				<ul id='b-person-groups-list'>
					 <?php foreach ($groups as $group): ?>
						 <li data-group-id='<?= $group->getId() ?>'>
							 <?= $group->getName() ?>&nbsp;<a title='Remove user from group' class='b-person-group-delete' href='#'>x</a>
						 </li>
					 <?php endforeach ?>
				</ul>
			<?php endif ?>

			<?= $button('plus', Lang::get('Add group'), ['class' => 'b-person-addgroups', 'rel' => $person->getId()]) ?>
		</div>
	</div>
</div>
