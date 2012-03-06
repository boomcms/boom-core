<?php
/**
* Text chunk template for a date restricted text slot.
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
<input type="text" name="<?=$slotname?>" value="<?=$text?>" id="<?=$slotname?>" class="chunk-slot {text-restricted <?=$slotname ?>} dateinput" size="10" />
