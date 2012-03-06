<?php
/**
* Text chunk template for an array restricted slot.
*
* Rendered by: Model_Chunk_Text::show()
*
*********************** Variables **********************
*	$slotname		****	text			****	The name of the slot.
*	$text			****	text			****	Current vlaue of the slot.
*	$options		****	array 			****	Array of allowed values for the text slot.
********************************************************
*
* @package Templates
* @author Hoop Associates	www.thisishoop.com	mail@hoopassociates.co.uk
* @copyright 2012, Hoop Associates
*/
?>
<select name="<?=$slotname ?>" class="chunk-slot {text-restricted <?=$slotname ?>}">
	<? 
		foreach ($options as $option):
			echo "<option value='$option'";
			if ($text == $option):
				echo " selected='selected'";
			endif;
		
			echo ">$option</option>";
		endforeach; 
	?>
</select>
