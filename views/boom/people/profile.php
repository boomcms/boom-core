<img src="<?= URL::gravatar($person->email) ?>" />

<div style="margin-top: 20px;">
	<form id="update-details-form" onsubmit='return false;'>
		<table>
			<? if (isset($people)): ?>
				<tr>
					<td>
						Real user:
					</td>
					<td>
						<?= $actual_person->name ?>
					</td>
				</tr>
				<tr>
					<td><label for="switch_user">Mimic user:</label></td>
					<td><select name="switch_user" id="switch_user">
						<option value="">- Select a user to mimic -</option>
						<?
							foreach ($people as $p):
								echo "<option value='$p->id'";

								if ($p->id == $person->id)
									echo 'selected="selected"';

								echo ">", $p->name, "</option>";
							endforeach;
						?>
					</td>
				</tr>
			<? endif; ?>
			<tr>
				<td><label for="theme">CMS theme:</label></td>
				<td>
					<? if (isset($mimicking)): ?>
						<?= Form::select('theme', Themes::find(), Themes::current(), array('id' => 'boom-theme-switch', 'disabled' => 'disabled')) ?>
					<? else: ?>
						<?= Form::select('theme', Themes::find(), Themes::current(), array('id' => 'boom-theme-switch')) ?>
					<? endif; ?>
				</td>
			</tr>
			<tr>
				<td><label for="name">Name:</label></td>
				<td>
					<? if (isset($mimicking)): ?>
						<?= Form::input('name', $person->name, array('size' => 35, 'disabled' => 'disabled')) ?>
					<? else: ?>
						<?= Form::input('name', $person->name, array('size' => 35)) ?>
					<? endif; ?>
				</td>
			</tr>
		</table>
	</form>
</div>