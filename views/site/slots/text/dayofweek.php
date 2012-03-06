<?php
/**
* Text chunk template for a day of week restricted text slot.
*
* Rendered by: Model_Chunk_Text::show()
*
*********************** Variables **********************
*	$slotname		****	text			****	The name of the slot.
*	$text			****	text			****	Current vlaue of the slot.
********************************************************
*
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
?>
<select name="<?=$slotname?>" class="chunk-slot {text-restricted <?=$slotname ?>}">
	<?
		foreach (array('Sun','Mon','Tue','Wed','Thu','Fri','Sat') as $day):
			echo "<option value='$day'";
			if ($text == $day):
			 	echo " selected='selected'";
		  	endif;
		
			echo ">", __( $day ), "</option>";
		endforeach;
	?>
</select>
