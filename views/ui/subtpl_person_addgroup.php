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
<div id="sledge-person-addgroup">

	<form action="/cms/people/add_group/<?= $person->id ?>" method='post'>
	
		Select some groups to add:
	
		<select name='group_id' multiple='multiple'>
			<?
				foreach( $groups as $group ):
					echo "<option value='", $group->id, "'>", $group->name, "</option>";
				endforeach;
			?>
		</select>
		
		<input type='submit' />
	
	</form>
</div>
