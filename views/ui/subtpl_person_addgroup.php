<?php
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates		www.thisishoop.com	 mail@hoopassociates.co.uk
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
