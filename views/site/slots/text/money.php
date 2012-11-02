<?php
/**
* Text chunk template for a money restricted text slot.
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
<span>
	<input  class="chunk-slot {text-restricted <?=$slotname ?>}" type="text" value="<?=$text?>" size="<?=strlen($text)+2?>" />
</span>
