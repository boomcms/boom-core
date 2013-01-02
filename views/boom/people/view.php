<div id="boom-person-view" class="b-items-view">
	<form onsubmit="return false;">
		<?= Form::hidden('csrf', Security::token()) ?>
		<input type="hidden" name="person_id" id='person_id' value="<?=$person->id;?>" />
		<input type="hidden" name="groups" value="<?=implode(',', $person->groups->find_all()->as_array())?>" />

		<div class="boom-tabs ui-helper-clearfix">

			<ul>
				<li><a href="#boom-person-view-information<?=$person->id;?>">Information</a></li>
				<li><a href="#boom-person-view-activity<?=$person->id;?>">Activity</a></li>
				<li><a href="#boom-person-view-groups<?=$person->id;?>">Groups</a></li>
				<li><a href="#boom-person-view-permissions<?=$person->id;?>">Permissions</a></li>
			</ul>

			<div class="ui-tabs-panel ui-widget-content ui-helper-left">
				<a href='<?= URL::gravatar($person->email, array('s' => 80), Request::$initial->secure()) ?>'
					title="<?= $person->name ?>"
					title="Click for larger view"
					class="ui-helper-left boom-asset-preview">
					<img class="ui-state-active ui-corner-all" src="<?= URL::gravatar($person->email, array('s' => 80), Request::$initial->secure()) ?>" />
				</a>
			</div>

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
								<?= Form::select('status',
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
			</div>

			<div id="boom-person-view-activity<?=$person->id;?>" class="ui-helper-left">
				<?
					if ($person->logs->count_all() > 0):
						$i = 0;
						?>
						<table width="100%">
							<thead>
								<th>Time</th>
								<th>Activity</th>
								<th>Note</th>
							</thead>
							<tbody>
								<?foreach ($person->logs->order_by('time', 'desc')->limit(50)->find_all() as $al):?>
									<tr class="boom-row-<?if (($i%2)==0) echo 'odd'; else echo 'even';?>">
										<td><?=date('d F Y H:i:s', $al->time);?></td>
										<td><?=$al->description;?></td>
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
					if ($person->groups->count_all() == 0):
						echo " is not a member of any groups<br />";
					else:
						?>
							 is a member of these groups:
							<ul>
								<?
									foreach ($person->groups->find_all() as $group):
										echo "<li>", $group->name, "&nbsp;<a rel='", $group->id, "' title='Remove user from group' class='b-people-group-delete' href='#'>x</a></li>";
									endforeach;
								?>
							</ul>
						<?
					endif;
				?>

				<button class="boom-button ui-button-text-icon s-people-groups-add" rel='<?= $person->id ?>' data-icon="ui-icon-circle-close">
					Add Group
				</button>
			</div>

			<div id="boom-person-view-permissions<?=$person->id;?>" class="ui-helper-left">

			</div>

			<br class="ui-helper-clear" />

			<div style="padding: .8em 0 .8em .8em;border-color:#ccc;border-width:1px 0 0 0;" class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
				<button class="boom-button ui-button-text-icon s-people-save" rel="<?=$person->id?>" id='boom-tagmanager-save-person' data-icon="ui-icon-disk">
					Save
				</button>
				<button class="boom-button ui-button-text-icon s-people-delete" id="boom-tagmanager-delete-person" rel="<?=$person->id?>" data-icon="ui-icon-trash">
					Delete
				</button>
				<button class="boom-button ui-button-text-icon boom-person-reset-password" id="boom-person-reset-password" rel="<?=$person->id?>" data-icon="ui-icon-default">
					Reset password
				</button>
			</div>
		</div>
	</form>
</div>
