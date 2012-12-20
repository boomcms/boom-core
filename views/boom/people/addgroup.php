<?php
/**
* Form to add a person to a group.
*
* Rendered by: Controller_Cms_People::action_add_group()
*
*********************** Variables **********************
****	$person	****	Instance of Model_Person		****	The person being added to a group.
****	$groups	****	Array of Model_Group instances	****	Array of groups the user doesn't already belong to.
********************************************************
*
*/
?>
<div id="boom-person-addgroup">

	<form onsubmit='return false;'>
		<?= Form::hidden('csrf', Security::token()) ?>
		Select some groups to add:
	
		<select name='groups[]' multiple='multiple'>
			<?
				foreach($groups as $group):
					echo "<option value='", $group->id, "'>", $group->name, "</option>";
				endforeach;
			?>
		</select>	
	</form>
</div>
