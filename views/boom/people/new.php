<?php
/**
* Form to add a new person.
*
* Rendered by: Controller_Cms_People::action_add()
*
*********************** Variables **********************
*	$groups	****	Array of Model_Group instances
********************************************************
*
*/
?>
<form method="post" action="/cms/people/add" id="boom-tagmanager-create-person-form">
	<?= Form::hidden('csrf', Security::token()) ?>
	<table width="100%">
		<tbody>
			<tr>
				<td><label for="create-email">Email</label></td>
				<td><input type="text" id="create-email" name="email" class="boom-input" /></td>
			</tr>
			<tr>
				<td><label for="create-group">Group</label></td>
				<td>
					<select id="create-group" name="group_id">
						<?
							foreach($groups as $group):
								echo "<option value='", $group->id, "'>", $group->name, "</option>";
							endforeach;
						?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</form>
