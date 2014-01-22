<div class="b-person-view" data-person-id='<?= $person->id ?>'>
	<div class="boom-tabs">
		<ul>
			<li><a href="#b-person-view-information<?= $person->id ?>">Information</a></li>
			<li><a href="#b-person-view-activity<?= $person->id ?>">Activity</a></li>
			<li><a href="#b-person-view-groups<?= $person->id ?>">Groups</a></li>
		</ul>

		<div id="b-person-view-information<?=$person->id;?>">
			<form>
				<?= Form::hidden('csrf', Security::token()) ?>

				<table width="100%">
					<tbody>
						<tr>
							<td><label for="person-name">Name:</label></td>
							<td><input type="text" id="person-name" name="name" class="boom-input" value="<?=$person->name?>" /></td>
						</tr>
						<tr>
							<td><label for="person-email">Email:</label></td>
							<td><input type="text" id="person-email" name="email" class="boom-input" disabled="disabled" value="<?= $person->email ?>" /></td>
						</tr>
						<tr>
							<td><label for='person-status'>Status:</label></td>
							<td>
								<?= Form::select('enabled',
									array(0 => 'Disabled', 1 => 'Enabled'),
									$person->enabled,
									array('id' => 'person-status', 'class' => 'boom-input',)
								) ?>
							</td>
						</tr>
					</tbody>
				</table>
				<div>
					<?= BoomUI::button('accept', __('Save'), array('id' => 'b-person-save', 'class' => 'b-people-save')) ?>
					<?= BoomUI::button('delete', __('Delete'), array('id' => 'b-person-delete')) ?>
				</div>
			</form>
		</div>

		<div id="b-person-view-activity<?=$person->id;?>">
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

		<div id="b-person-view-groups<?= $person->id ?>">
			<?= $person->name ?>
			<? if (count($groups) == 0): ?>
				is not a member of any groups<br />
			<? else: ?>
				is a member of these groups:
				<ul id='b-person-groups-list'>
					 <? foreach ($groups as $group): ?>
						 <li data-group-id='<?= $group->id ?>'>
							 <?= $group->name ?>&nbsp;<a title='Remove user from group' class='b-person-group-delete' href='#'>x</a>
						 </li>
					 <? endforeach ?>
				</ul>
			<? endif ?>

			<?= BoomUI::button('add', __('Add group'), array('class' => 'b-person-addgroups', 'rel' => $person->id)) ?>
		</div>
	</div>
</div>