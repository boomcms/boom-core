<div class="b-person-view" data-person-id='<?= $person->getId() ?>'>
	<div class="boom-tabs">
		<ul>
			<li><a href="#b-person-view-information<?= $person->getId() ?>">Information</a></li>
			<li><a href="#b-person-view-activity<?= $person->getId() ?>">Activity</a></li>
			<li><a href="#b-person-view-groups<?= $person->getId() ?>">Groups</a></li>
		</ul>

		<div id="b-person-view-information<?=$person->getId();?>">
			<form>
				<?= Form::hidden('csrf', Security::token()) ?>

				<table width="100%">
					<tbody>
						<tr>
							<td><label for="person-name">Name:</label></td>
							<td><input type="text" id="person-name" name="name" class="boom-input" value="<?= $person->getName() ?>" /></td>
						</tr>
						<tr>
							<td><label for="person-email">Email:</label></td>
							<td><input type="text" id="person-email" name="email" class="boom-input" disabled="disabled" value="<?= $person->getEmail() ?>" /></td>
						</tr>
						<tr>
							<td><label for='person-status'>Status:</label></td>
							<td>
								<?= Form::select('enabled',
									array(0 => 'Disabled', 1 => 'Enabled'),
									$person->isEnabled(),
									array('id' => 'person-status', 'class' => 'boom-input',)
								) ?>
							</td>
						</tr>
					</tbody>
				</table>
				<div>
					<?= \Boom\UI::button('accept', __('Save'), array('id' => 'b-person-save', 'class' => 'b-people-save')) ?>
					<?= \Boom\UI::button('delete', __('Delete'), array('id' => 'b-person-delete')) ?>
				</div>
			</form>
		</div>

		<div id="b-person-view-activity<?= $person->getId() ?>">
			<? if (count($activities) > 0): ?>
				<table width="100%">
					<thead>
						<th>Time</th>
						<th>Activity</th>
						<th>Note</th>
					</thead>
					<tbody>
						<? foreach ($activities as $al): ?>
							<tr class="boom-row-<?= Text::alternate('odd', 'even') ?>">
								<td><?= date('d F Y H:i:s', $al->time) ?></td>
								<td><?= $al->activity ?></td>
								<td><?= $al->note ?></td>
							</tr>
						<? endforeach ?>
					</tbody>
				</table>
			<? else: ?>
				<p>
					(No activity logged)
				</p>
			<? endif ?>
		</div>

		<div id="b-person-view-groups<?= $person->getId() ?>">
			<?= $person->getName() ?>
			<? if (count($groups) == 0): ?>
				is not a member of any groups<br />
			<? else: ?>
				is a member of these groups:
				<ul id='b-person-groups-list'>
					 <? foreach ($groups as $group): ?>
						 <li data-group-id='<?= $group->getId() ?>'>
							 <?= $group->getName() ?>&nbsp;<a title='Remove user from group' class='b-person-group-delete' href='#'>x</a>
						 </li>
					 <? endforeach ?>
				</ul>
			<? endif ?>

			<?= \Boom\UI::button('add', __('Add group'), array('class' => 'b-person-addgroups', 'rel' => $person->getId())) ?>
		</div>
	</div>
</div>