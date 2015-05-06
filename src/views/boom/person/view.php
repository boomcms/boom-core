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
                                    <?= Form::select('enabled',
                                        array(0 => 'Disabled', 1 => 'Enabled'),
                                        $person->isEnabled(),
                                        array('id' => 'person-status'))
                                    ?>
                                </label>

				<div>
					<?= new \BoomCMS\Core\UI\Button('accept', Lang::get('Save'), array('id' => 'b-person-save', 'class' => 'b-people-save')) ?>
					<?= new \BoomCMS\Core\UI\Button('delete', Lang::get('Delete'), array('id' => 'b-person-delete')) ?>
				</div>
			</form>
		</div>

		<div id="b-person-view-activity<?= $person->getId() ?>">
			<?php if (count($activities) > 0): ?>
				<table width="100%">
					<thead>
						<th>Time</th>
						<th>Activity</th>
						<th>Note</th>
					</thead>
					<tbody>
						<?php foreach ($activities as $al): ?>
							<tr class="boom-row-<?= Text::alternate('odd', 'even') ?>">
								<td><?= date('d F Y H:i:s', $al->time) ?></td>
								<td><?= $al->activity ?></td>
								<td><?= $al->note ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			<?php else: ?>
				<p>
					(No activity logged)
				</p>
			<?php endif ?>
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

			<?= new \BoomCMS\Core\UI\Button('add', Lang::get('Add group'), array('class' => 'b-person-addgroups', 'rel' => $person->getId())) ?>
		</div>
	</div>
</div>
