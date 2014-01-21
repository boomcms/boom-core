<div id="boom-person-view">
	<form onsubmit="return false;">
		<?= Form::hidden('csrf', Security::token()) ?>
		<input type="hidden" name="person_id" id='person_id' value="<?=$person->id;?>" />
		<input type="hidden" name="groups" value="<?=implode(',', $person->groups->find_all()->as_array())?>" />

		<div class="boom-tabs ui-helper-clearfix">
			<ul>
				<li><a href="#boom-person-view-information<?=$person->id;?>">Information</a></li>
				<li><a href="#boom-person-view-activity<?=$person->id;?>">Activity</a></li>
				<li><a href="#boom-person-view-groups<?=$person->id;?>">Groups</a></li>
			</ul>

			<div id="boom-person-view-information<?=$person->id;?>" class="ui-helper-left">
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
										array(
											0	=>	'Disabled',
											1	=>	'Enabled',
										),
										$person->enabled,
										array(
											'id'	=>	'person-status',
											'class'	=>	'boom-input',
										)
									);
								?>
							</td>
						</tr>
					</tbody>
				</table>

				<div>
					<?= BoomUI::button('accept', __('Save'), array('id' => 'boom-tagmanager-save-person', 'class' => 'b-people-save', 'rel' => $person->id)) ?>
					<?= BoomUI::button('delete', __('Delete'), array('id' => 'b-delete-person', 'rel' => $person->id)) ?>
				</div>
			</div>

			<div id="boom-person-view-activity<?=$person->id;?>" class="ui-helper-left">
				<?
					if (count($activities) > 0):
						$i = 0;
						?>
						<table width="100%">
							<thead>
								<th>Time</th>
								<th>Activity</th>
								<th>Note</th>
							</thead>
							<tbody>
								<?foreach ($activities as $al):?>
									<tr class="boom-row-<?if (($i%2)==0) echo 'odd'; else echo 'even';?>">
										<td><?=date('d F Y H:i:s', $al->time);?></td>
										<td><?=$al->activity;?></td>
										<td><?=$al->note;?></td>
									</tr>
									<?$i++;?>
								<?endforeach;?>
							</tbody>
						</table>
					<?else:?>
						<p>
							(No activity logged)
						</p>
					<?endif;
				?>
			</div>

			<div id="boom-person-view-groups<?=$person->id;?>" rel='<?= $person->id ?>' class="ui-helper-left">
				<?
					echo $person->name;
					if (count($groups) == 0):
						echo " is not a member of any groups<br />";
					else:
						?>
							 is a member of these groups:
							<ul>
								<?
									foreach ($groups as $group):
										echo "<li>", $group->name, "&nbsp;<a rel='", $group->id, "' title='Remove user from group' class='b-people-group-delete' href='#'>x</a></li>";
									endforeach;
								?>
							</ul>
						<?
					endif;
				?>

				<?= BoomUI::button('add', __('Add group'), array('class' => 'b-people-groups-add', 'rel' => $person->id)) ?>
			</div>
		</div>
	</form>
</div>